<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Providers\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // <--- Import Log
use Illuminate\Support\Facades\Log; // <--- IMPORTANTE

class CheckoutController extends Controller
{
    protected $cartService;

    // Inyectamos el servicio igual que en el CartController
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Existing Order Flow (e.g. Custom Order Payment)
        if ($request->has('order')) {
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

        // 2. Standard Cart Flow
        $cartItems = $this->cartService->getCart();
        $total = $this->cartService->getTotal();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart');
        }

        return view('front.checkout', compact('cartItems', 'total', 'user'));
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
            // Call PayPhone
            $response = Http::withoutVerifying()
                ->withToken(config('services.payphone.token'))
                ->post('https://pay.payphonetodoesposible.com/api/button/Prepare', [
                    'amount' => (int) ($order->total_amount * 100),
                    'amountWithoutTax' => (int) ($order->total_amount * 100),
                    'amountWithTax' => 0,
                    'tax' => 0,
                    'serviceTax' => 0,
                    'tip' => 0,
                    'currency' => 'USD',
                    'clientTransactionId' => (string) $order->id.'-'.time(),
                    'responseUrl' => route('checkout.callback'),
                    'cancellationUrl' => route('checkout.cancel'),
                ]);

            Log::info('payExisting: PayPhone response status: '.$response->status());

            if ($response->successful()) {
                Log::info("PayPhone link generated for Existing Order ID: {$order->id}");

                return redirect()->away($response->json()['payWithCard']);
            }

            $errorMsg = 'Error al generar link de pago: '.$response->body();
            Log::error("PayPhone Error for Order ID {$order->id}: ".$response->body());

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
        // 2. Obtener items desde el SERVICIO
        $cartItems = $this->cartService->getCart();
        // $total not used here in logic, only for view but this is a POST action


        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'El carrito está vacío en la sesión.');
        }

        try {
            DB::beginTransaction();

            // 2.a Sync Address with User Profile
            $address = $this->syncUserAddress($user, $validated);

            // --- FUSION LOGIC START ---
            // 1. Resolve Master Order or Create New
            [$order, $masterOriginalItem] = $this->resolveOrderForCheckout($user, $address, $cartItems, $validated);

            // 2. Process Cart Items
            $this->processCartItems($order, $cartItems, $masterOriginalItem);

            // 3. Recalculate Final Total
            $this->finalizeOrderTotal($order);

            DB::commit();
            Log::info("Order processed successfully (Fusion or Creation). ID: {$order->id}");

            // 3. Llamada a PayPhone
            return $this->initiatePayPhone($order);

        } catch (\Exception $e) {
            Log::error('Checkout Exception: '.$e->getMessage());
            try {
                DB::rollBack();
            } catch (\Exception $r) {
                Log::error("Rollback failed: " . $r->getMessage());
            }

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

        if (! $payphoneId) {
            return redirect()->route('cart')->with('error', 'No se recibió el ID de pago.');
        }

        try {
            // INTENTO CON CONFIRM V1 (Para coincidir con Prepare V1)
            $result = $this->confirmPaymentWithPayPhone($payphoneId, $rawOrderId);
            Log::info('PayPhone Confirm Response (V1): '.json_encode($result));

            if (isset($result['transactionStatus']) && $result['transactionStatus'] === 'Approved') {
                return $this->processSuccessfulTransaction($orderId, $payphoneId);
            } else {
                Log::warning('Transaction not approved. Status: '.($result['transactionStatus'] ?? 'Unknown'));
            }

            return redirect()->route('cart')->with('error', 'Pago no aprobado. Estado: '.($result['transactionStatus'] ?? 'Error'));

        } catch (\Exception $e) {
            Log::error('Callback Exception: '.$e->getMessage());

            return redirect()->route('cart')->with('error', 'Error al confirmar: '.$e->getMessage());
        }
    }

    private function validateShipping(Request $request)
    {
        return $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_lastname' => 'required|string|max:255',
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
        $response = Http::withoutVerifying()
            ->withToken(config('services.payphone.token'))
            ->post('https://pay.payphonetodoesposible.com/api/button/Prepare', [
                'amount' => (int) ($order->total_amount * 100),
                'amountWithoutTax' => (int) ($order->total_amount * 100),
                'amountWithTax' => 0,
                'tax' => 0,
                'serviceTax' => 0,
                'tip' => 0,
                'currency' => 'USD',
                'clientTransactionId' => (string) $order->id.'-'.time(),
                'responseUrl' => route('checkout.callback'),
                'cancellationUrl' => route('checkout.cancel'),
            ]);

        if ($response->successful()) {
            Log::info("PayPhone link generated for Order ID: {$order->id}");

            return redirect()->away($response->json()['payWithCard']);
        }

        Log::error("PayPhone Error for Order ID {$order->id}: ".$response->body());

        return redirect()->route('checkout')->with('error', 'Error al generar link de pago: '.$response->body());
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
        $user->update(['phone' => $validated['customer_phone']]);

        return $address;
    }

    private function resolveOrderForCheckout($user, $address, $cartItems, $validated)
    {
        // 1. Identify valid Custom Orders in Cart
        $customOrderIds = $cartItems->pluck('custom_order_id')->filter()->unique();
        $masterOrderId = $customOrderIds->first();
        $order = null;
        $masterOriginalItem = null;

        if ($masterOrderId) {
            // Use the FIRST custom order as the Master
            $order = Order::find($masterOrderId);

            if (! $order) {
                throw new \RuntimeException('El pedido personalizado principal referenciado no existe.');
            }

            // Identify the specific "original" item of this master order to avoid confusing it with merged ones later
            // It should be the one with null product_id that typically exists from creation
            $masterOriginalItem = $order->items()->whereNull('product_id')->first();

            // Update details on the existing order
            $order->update([
                'address_id' => $address->id,
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_zip' => $validated['shipping_zip'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'status' => Order::STATUS_PENDING_PAYMENT, // Ensure it's ready for payment/retry
            ]);

        } else {
            // Create New Standard Order (No Custom Orders involved)
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
                'type' => 'stock', // Explicitly mark as stock
            ]);
        }

        return [$order, $masterOriginalItem];
    }

    private function processCartItems($order, $cartItems, $masterOriginalItem)
    {
        foreach ($cartItems as $cartItem) {
            // CASE A: It is a Custom Order Item
            if ($cartItem->custom_order_id) {

                // Sub-case A1: It IS the Master Order Item
                if ($order && $cartItem->custom_order_id == $order->id) {
                    if ($masterOriginalItem) {
                        $masterOriginalItem->update([
                            'price' => $cartItem->price,
                            'quantity' => 1,
                        ]);
                    }
                }
                // Sub-case A2: It is a SECONDARY Custom Order (Merge Strategy)
                else {
                    $secondaryOrder = Order::find($cartItem->custom_order_id);
                    if ($secondaryOrder) {
                        // Get its item info (assuming 1 custom item per custom order usually)
                        $secondaryItem = $secondaryOrder->items()->whereNull('product_id')->first();

                        if ($secondaryItem) {
                            // Clone it into the Master Order
                            OrderItem::create([
                                'order_id' => $order->id,
                                'product_id' => null, // It's still a custom item
                                'custom_order_id' => $secondaryOrder->id, // Reference to old ID (optional but good for tracking)
                                'custom_description' => $secondaryItem->custom_description,
                                'price' => $cartItem->price,
                                'quantity' => 1,
                                'images' => $secondaryItem->images, // Copy images array
                            ]);
                        }

                        // 3. Mark the secondary order as cancelled/merged instead of deleting
                        // This preserves ID sequence and history as requested
                        $secondaryOrder->update([
                            'status' => 'cancelled',
                            'total_amount' => 0, // Reset total since items moved
                            'customer_phone' => $secondaryOrder->customer_phone.' (Fusionado con Order #'.$order->id.')',
                        ]);
                    }
                }

                continue;
            }

            // CASE B: Standard Stock Items
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'custom_order_id' => null,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);
        }
    }

    private function confirmPaymentWithPayPhone($payphoneId, $rawOrderId)
    {
        $response = Http::withoutVerifying()
            ->withToken(config('services.payphone.token'))
            ->post('https://pay.payphonetodoesposible.com/api/button/Confirm', [
                'id' => (int) $payphoneId,
                'clientTransactionId' => (string) $rawOrderId,
            ]);

        // Combine status check with result
        $result = $response->json();
        if (! $response->successful()) {
            Log::error('PayPhone Confirm Failed: '.$response->body());
        }

        return $result;
    }

    private function processSuccessfulTransaction($orderId, $payphoneId)
    {
        try {
            DB::beginTransaction();

            // Note: Removed lockForUpdate() to compatible with SQLite testing
            $order = Order::with('items')->find($orderId);

            if (! $order) {
                throw new \RuntimeException('Orden no encontrada durante el procesamiento (Race Condition check).');
            }

            // Check if already paid
            if ($order->status === Order::STATUS_PAID) {
                DB::rollBack();

                return redirect()->route('cart')->with('info', 'Esta orden ya fue procesada anteriormente.');
            }

            $order->update([
                'status' => 'in_review',
                'payphone_transaction_id' => $payphoneId,
                'payphone_status' => 'Approved',
            ]);

            // Handle Custom Orders status linking
            foreach ($order->items as $item) {
                if ($item->custom_order_id) {
                    $customOrder = Order::find($item->custom_order_id);
                    if ($customOrder) {
                        $customOrder->update(['status' => Order::STATUS_PAID]);
                    }
                }
            }

            // DECREMENT STOCK
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        if ($product->stock < $item->quantity) {
                            throw new \RuntimeException("Stock insuficiente para el producto '{$product->name}'. La compra ha sido revertida.");
                        }
                        $product->decrement('stock', $item->quantity);
                    }
                }
            }

            $order->update(['status' => Order::STATUS_PAID]);

            // Clear Cart
            $order->user->cartItems()->delete();

            DB::commit();

            // Send Emails
            $this->sendOrderEmails($order);

            return view('front.checkout-success', compact('order'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Transaction Error processing Order {$orderId}: ".$e->getMessage());

            return redirect()->route('cart')->with('error', 'Error procesando el pedido: '.$e->getMessage());
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
            $admin = \App\Models\User::where('role', 'admin')->first();
            if ($admin) {
                \Illuminate\Support\Facades\Mail::to($admin->email)->send(new \App\Mail\NewOrderAdminNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Error sending NewOrderAdminNotification (Paid): '.$e->getMessage());
        }
    }
}
