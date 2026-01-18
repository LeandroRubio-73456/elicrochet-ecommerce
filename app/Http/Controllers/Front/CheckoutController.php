<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Providers\CartService;
use App\Services\PayPhoneService; // <--- Import Service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $cartService;

    protected $payPhoneService; // <--- Service Property

    // Inyectamos el servicio igual que en el CartController
    public function __construct(CartService $cartService, PayPhoneService $payPhoneService)
    {
        $this->cartService = $cartService;
        $this->payPhoneService = $payPhoneService; // <--- DI
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Existing Order Flow (e.g. Custom Order Payment)
        if ($request->has('order')) {
            return $this->handleExistingOrderCheckout($request, $user);
        }

        // 2. Standard Cart Flow
        $cartItems = $this->cartService->getCart();
        $total = $this->cartService->getTotal();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart');
        }

        // Validate Status of Custom Orders
        foreach ($cartItems as $cItem) {
            if ($cItem->custom_order_id) {
                $cOrder = \App\Models\Order::find($cItem->custom_order_id);
                if (! $cOrder || $cOrder->status === 'cancelled') {
                    $this->cartService->removeFromCart($cItem->product_id ?? $cItem->custom_order_id);

                    return redirect()->route('cart')->with('error', 'Se detectó un pedido cancelado en tu carrito y fue eliminado.');
                }
            }
        }

        return view('front.checkout', compact('cartItems', 'total', 'user'));
    }

    private function handleExistingOrderCheckout($request, $user)
    {
        Log::info("Checkout payment retry requested. Order ID: {$request->order}, User ID: {$user->id}");

        $order = Order::where('id', $request->order)
            ->where('user_id', $user->id)
            ->firstOrFail();

        Log::info("Order found. Status: '{$order->status}'");

        if ($order->status !== 'pending_payment') {
            Log::warning('Order status mismatch. Redirecting to show.');

            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Esta orden no está pendiente de pago.');
        }

        return view('front.checkout_payment', compact('order', 'user'));
    }

    /**
     * Process payment for an EXISTING order (e.g. Custom Orders already in DB)
     */
    public function payExisting(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        if ($order->status !== 'pending_payment') {
            Log::warning("payExisting: Order {$order->id} is not pending payment. Status: {$order->status}");

            return redirect()->route('customer.orders.show', $order->id)->with('error', 'Orden no válida para pago.');
        }

        try {
            Log::info("payExisting: Preparing PayPhone request for Order {$order->id}");

            // Call PayPhone Service
            $response = $this->payPhoneService->prepare(
                (int) ($order->total_amount * 100),
                (string) $order->id.'-'.time()
            );

            Log::info("PayPhone link generated for Existing Order ID: {$order->id}");

            return redirect()->away($response['payWithCard']);

        } catch (\Exception $e) {
            $errorMsg = 'Error: '.$e->getMessage();
            Log::error('PayExisting Exception: '.$e->getMessage());
        }

        return back()->with('error', $errorMsg);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // 1. Validar inputs
        $validated = $this->validateShipping($request);

        // 2. Obtener items desde el SERVICIO
        $cartItems = $this->cartService->getCart();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'El carrito está vacío en la sesión.');
        }

        // Validate Status of Custom Orders in Cart
        foreach ($cartItems as $cItem) {
            if ($cItem->custom_order_id) {
                $cOrder = \App\Models\Order::find($cItem->custom_order_id);
                if (! $cOrder || $cOrder->status === 'cancelled') {
                    $this->cartService->removeFromCart($cItem->product_id ?? $cItem->custom_order_id);

                    return redirect()->route('cart')->with('error', 'Un pedido personalizado en tu carrito ya no es válido o fue cancelado. Se ha eliminado automáticamente.');
                }
            }
        }

        try {
            // Use DB::transaction closure to handle nesting correctly
            $order = DB::transaction(function () use ($user, $validated, $cartItems) {
                // 2.a Sync Address with User Profile
                $address = $this->syncUserAddress($user, $validated);

                // --- FUSION LOGIC START ---
                // 1. Resolve Master Order or Create New
                [$order, $masterOriginalItem] = $this->resolveOrderForCheckout($user, $address, $cartItems, $validated);

                // 2. Process Cart Items
                $this->processCartItems($order, $cartItems, $masterOriginalItem);

                // 3. Recalculate Final Total
                $this->finalizeOrderTotal($order);

                Log::info("Order processed successfully (Fusion or Creation). ID: {$order->id}");

                return $order;
            });

            // 3. Llamada a PayPhone (fuera de la transacción de DB)
            return $this->initiatePayPhone($order);

        } catch (\Exception $e) {
            Log::error('Checkout Exception: '.$e->getMessage());

            return redirect()->route('checkout')->with('error', 'Error: '.$e->getMessage());
        }
    }

    /**
     * Callback de PayPhone: Aquí llega el usuario tras pagar
     */
    public function callback(Request $request)
    {
        $payphoneId = $request->query('id');
        $rawOrderId = $request->query('clientTransactionId');

        $orderIdParts = explode('-', $rawOrderId);
        $orderId = $orderIdParts[0];

        Log::info("PayPhone Callback received. PayPhone ID: {$payphoneId}, Raw Order ID: {$rawOrderId}, Real Order ID: {$orderId}");
        Log::info('Full Callback Request: ', $request->all());

        $error = 'No se recibió el ID de pago.';

        if ($payphoneId) {
            try {
                // Call PayPhone Service
                $result = $this->payPhoneService->confirm((int) $payphoneId, (string) $rawOrderId);
                Log::info('PayPhone Confirm Response (V1): '.json_encode($result));

                if (isset($result['transactionStatus']) && $result['transactionStatus'] === 'Approved') {
                    return $this->processSuccessfulTransaction($orderId, $payphoneId);
                }

                $status = $result['transactionStatus'] ?? 'Unknown';
                Log::warning("Transaction not approved. Status: {$status}");
                $error = "Pago no aprobado. Estado: {$status}";

            } catch (\Exception $e) {
                Log::error('Callback Exception: '.$e->getMessage());
                $error = 'Error al confirmar: '.$e->getMessage();
            }
        }

        return redirect()->route('cart')->with('error', $error);
    }

    private function validateShipping(Request $request)
    {
        return $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_lastname' => 'required|string|max:255',
            'customer_cedula' => 'required|string|max:13',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_province' => 'required|string',
            'shipping_reference' => 'nullable|string',
            'shipping_zip' => 'required|string',
        ]);
    }

    private function initiatePayPhone(Order $order)
    {
        try {
            $response = $this->payPhoneService->prepare(
                (int) ($order->total_amount * 100),
                (string) $order->id.'-'.time()
            );

            Log::info("PayPhone link generated for Order ID: {$order->id}");

            return redirect()->away($response['payWithCard']);

        } catch (\Exception $e) {
            Log::error("PayPhone Error for Order ID {$order->id}: ".$e->getMessage());

            return redirect()->route('checkout')->with('error', 'Error al generar link de pago: '.$e->getMessage());
        }
    }

    private function syncUserAddress($user, $validated)
    {
        $address = $user->addresses()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'street' => $validated['shipping_address'],
                'city' => $validated['shipping_city'],
                'province' => $validated['shipping_province'],
                'reference' => $validated['shipping_reference'] ?? null,
                'postal_code' => $validated['shipping_zip'],
                'phone' => $validated['customer_phone'],
                'customer_name' => $validated['customer_name'].' '.$validated['customer_lastname'],
                'customer_email' => $validated['customer_email'],
                // Legacy redundant fields
                'address' => $validated['shipping_address'],
                'details' => $validated['shipping_reference'] ?? null,
            ]
        );

        // Also update legacy user columns
        $user->update([
            'phone' => $validated['customer_phone'],
            'cedula' => $validated['customer_cedula'] ?? $user->cedula, // Update only if provided
        ]);

        return $address;
    }

    private function resolveOrderForCheckout($user, $address, $cartItems, $validated)
    {
        // 1. Check for an existing PENDING order for this user (Reuse Strategy)
        // We look for a recent 'stock' type order pending payment to avoid creating duplicates on retry.
        $existingOrder = Order::where('user_id', $user->id)
            ->where('status', Order::STATUS_PENDING_PAYMENT)
            ->where('type', 'stock') // Master orders are 'stock'
            ->latest()
            ->first();

        if ($existingOrder) {
            // Update the existing order with latest contact/address info
            $existingOrder->update([
                'address_id' => $address->id,
                'customer_name' => $validated['customer_name'].' '.$validated['customer_lastname'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_zip' => $validated['shipping_zip'],
            ]);

            // CLEAR previous items so we can re-populate with current Cart state
            // This ensures if the user changed the cart, the order reflects it.
            // Custom Orders linked to this are NOT deleted (they are separate entities),
            // but the 'OrderItem' linking them is deleted here.
            $existingOrder->items()->delete();

            // Recalculate Shipping
            $shippingCost = $this->calculateShippingCost($validated['shipping_city']);
            $existingOrder->update(['shipping_cost' => $shippingCost]);

            Log::info("Reusing existing Pending Order ID: {$existingOrder->id}");

            return [$existingOrder, null];
        }

        // 2. Create NEW Master Order if none exists
        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => Order::STATUS_PENDING_PAYMENT,
            'customer_name' => $validated['customer_name'].' '.$validated['customer_lastname'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'shipping_city' => $validated['shipping_city'],
            'shipping_province' => $validated['shipping_province'],
            'shipping_zip' => $validated['shipping_zip'],
            'total_amount' => 0,
            'shipping_cost' => $this->calculateShippingCost($validated['shipping_city']),
            'type' => 'stock',
        ]);

        return [$order, null];
    }

    private function processCartItems($order, $cartItems, $masterOriginalItem)
    {
        foreach ($cartItems as $cartItem) {
            if ($cartItem->custom_order_id) {
                $this->processCustomOrderItem($order, $cartItem);

                continue;
            }

            // Standard Stock Items
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'custom_order_id' => null,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);
        }
    }

    private function processCustomOrderItem($order, $cartItem)
    {
        $customOrder = Order::find($cartItem->custom_order_id);

        if ($customOrder) {
            // Get the main item from the custom order to copy details
            $customItem = $customOrder->items()->whereNull('product_id')->first();

            // Link the Custom Order to the new Master Order
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null,
                'custom_order_id' => $customOrder->id, // LINK: Parent -> Child
                'custom_description' => $customItem ? $customItem->custom_description : 'Pedido Personalizado #'.$customOrder->id,
                'price' => $cartItem->price,
                'quantity' => 1,
                'images' => $customItem ? $customItem->images : [],
                'custom_specs' => $customItem ? $customItem->custom_specs : [],
            ]);

            // Update Child Order Status to indicate it's linked
            // Do NOT cancel it. Keep it locked.
            $customOrder->update([
                'status' => Order::STATUS_LINKED,
            ]);
        }
    }

    private function processSuccessfulTransaction($orderId, $payphoneId)
    {
        try {
            return DB::transaction(function () use ($orderId, $payphoneId) {
                // Note: Removed lockForUpdate() to compatible with SQLite testing
                $order = Order::with('items')->find($orderId);

                $this->validateProcessingOrder($order);

                $order->update([
                    'status' => 'in_review',
                    'payphone_transaction_id' => $payphoneId,
                    'payphone_status' => 'Approved',
                ]);

                $this->handleCustomOrderLinking($order);
                $this->decrementOrderStock($order);

                $order->update(['status' => Order::STATUS_PAID]);

                $this->clearUserCart($order);

                // Send Emails (outside transaction ideally, but fine here)
                $this->sendOrderEmails($order);

                return view('front.checkout-success', compact('order'));
            });

        } catch (\Exception $e) {
            Log::error("Transaction Error processing Order {$orderId}: ".$e->getMessage());

            return redirect()->route('cart')->with('error', 'Error procesando el pedido: '.$e->getMessage());
        }
    }

    private function validateProcessingOrder($order)
    {
        if (! $order) {
            throw new \App\Exceptions\BusinessLogicException('Orden no encontrada durante el procesamiento (Race Condition check).');
        }

        if ($order->status === Order::STATUS_PAID) {
            throw new \App\Exceptions\BusinessLogicException('Esta orden ya fue procesada anteriormente.');
        }
    }

    private function handleCustomOrderLinking(Order $order)
    {
        foreach ($order->items as $item) {
            if ($item->custom_order_id) {
                $customOrder = Order::find($item->custom_order_id);
                if ($customOrder) {
                    if ($customOrder->status !== Order::STATUS_LINKED) {
                        $customOrder->update(['status' => Order::STATUS_LINKED]);
                    }
                }
            }
        }
    }

    private function decrementOrderStock(Order $order)
    {
        foreach ($order->items as $item) {
            if ($item->product_id) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    if ($product->stock < $item->quantity) {
                        throw new \App\Exceptions\BusinessLogicException("Stock insuficiente para el producto '{$product->name}'. La compra ha sido revertida.");
                    }
                    $product->decrement('stock', $item->quantity);

                    // Automáticamente pasar a borrador si el stock llega a 0
                    if ($product->refresh()->stock <= 0) {
                        $product->update(['status' => 'draft']);
                        Log::info("Producto ID {$product->id} marcado como borrador por falta de stock.");
                    }
                }
            }
        }
    }

    private function clearUserCart(Order $order)
    {
        if ($order->user) {
            $order->user->cartItems()->delete();
        }
    }

    private function sendOrderEmails($order)
    {
        try {
            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderPaidNotification($order));
        } catch (\Exception $e) {
            Log::error('Error sending OrderPaid email: '.$e->getMessage());
        }

        try {
            $adminEmail = config('mail.admin_email');
            if ($adminEmail) {
                \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\NewOrderAdminNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Error sending NewOrderAdminNotification (Paid): '.$e->getMessage());
        }
    }

    private function finalizeOrderTotal(Order $order)
    {
        $total = $order->recalculateTotal();
        $order->total_amount = $total;
        $order->save();
    }

    private function calculateShippingCost($city)
    {
        // Normalize city string for comparison
        $normalizedCity = strtolower(trim($city));

        // Logic: Quito = $3, Others = $5
        if ($normalizedCity === 'quito') {
            return 3.00;
        }

        return 5.00;
    }
}
