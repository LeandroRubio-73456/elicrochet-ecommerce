<?php

namespace App\Services;

use App\Exceptions\BusinessLogicException;
use App\Mail\NewOrderAdminNotification;
use App\Mail\OrderPaidNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Orquesta la creación de órdenes desde el carrito y el procesamiento de
 * pagos. CheckoutController se limita a validar la petición HTTP y a
 * traducir el resultado de estos métodos en una respuesta.
 */
class CheckoutService
{
    public function __construct(protected PayPhoneService $payPhoneService) {}

    /**
     * Crea (o reutiliza) la orden "contenedora" del checkout a partir del
     * carrito actual del usuario, dentro de una única transacción.
     */
    public function createOrderFromCart(User $user, array $shippingData, Collection $cartItems): Order
    {
        return DB::transaction(function () use ($user, $shippingData, $cartItems) {
            $address = $this->syncUserAddress($user, $shippingData);
            $order = $this->resolveOrderForCheckout($user, $address, $shippingData);
            $this->processCartItems($order, $cartItems);
            $this->finalizeOrderTotal($order);

            Log::info("Order processed successfully (Fusion or Creation). ID: {$order->id}");

            return $order;
        });
    }

    /**
     * Prepara el pago de una orden ya existente y devuelve la URL a la que
     * debe redirigirse al usuario (PayPhone real o la pasarela simulada).
     */
    public function initiatePayment(Order $order): string
    {
        $response = $this->payPhoneService->prepare(
            (int) ($order->total_amount * 100),
            (string) $order->id.'-'.time()
        );

        Log::info("PayPhone link generated for Order ID: {$order->id}");

        return $response['payWithCard'];
    }

    /**
     * Confirma el pago ante PayPhone y, si fue aprobado, marca la orden
     * como pagada (stock, órdenes personalizadas vinculadas, correos).
     *
     * @throws BusinessLogicException si el pago no fue aprobado o la orden ya no es válida.
     */
    public function confirmPayment(int $payphoneId, string $rawOrderId, ?string $simulatedStatus, string $orderId): Order
    {
        $result = $this->payPhoneService->confirm($payphoneId, $rawOrderId, $simulatedStatus);
        Log::info('PayPhone Confirm Response (V1): '.json_encode($result));

        if (($result['transactionStatus'] ?? null) !== 'Approved') {
            $status = $result['transactionStatus'] ?? 'Unknown';
            Log::warning("Transaction not approved. Status: {$status}");

            throw new BusinessLogicException("Pago no aprobado. Estado: {$status}");
        }

        try {
            return $this->processApprovedPayment($orderId, $payphoneId);
        } catch (\Exception $e) {
            Log::error("Transaction Error processing Order {$orderId}: ".$e->getMessage());

            throw new BusinessLogicException('Error procesando el pedido: '.$e->getMessage());
        }
    }

    private function processApprovedPayment(string $orderId, int $payphoneId): Order
    {
        return DB::transaction(function () use ($orderId, $payphoneId) {
            // Nota: sin lockForUpdate() para que los tests con SQLite funcionen igual.
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

            // El envío de correos podría vivir fuera de la transacción,
            // pero se mantiene aquí para no cambiar el comportamiento actual.
            $this->sendOrderEmails($order);

            return $order;
        });
    }

    private function syncUserAddress(User $user, array $shippingData)
    {
        $address = $user->addresses()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'street' => $shippingData['shipping_address'],
                'city' => $shippingData['shipping_city'],
                'province' => $shippingData['shipping_province'],
                'reference' => $shippingData['shipping_reference'] ?? null,
                'postal_code' => $shippingData['shipping_zip'],
                'phone' => $shippingData['customer_phone'],
                'customer_name' => $shippingData['customer_name'].' '.$shippingData['customer_lastname'],
                'customer_email' => $shippingData['customer_email'],
                // Campos redundantes que se mantienen por compatibilidad con vistas antiguas.
                'address' => $shippingData['shipping_address'],
                'details' => $shippingData['shipping_reference'] ?? null,
            ]
        );

        $user->update([
            'phone' => $shippingData['customer_phone'],
            'cedula' => $shippingData['customer_cedula'] ?? $user->cedula,
        ]);

        return $address;
    }

    private function resolveOrderForCheckout(User $user, $address, array $shippingData): Order
    {
        // Limpiar órdenes contenedoras abandonadas (>1 hora) para no acumularlas.
        Order::where('user_id', $user->id)
            ->whereIn('type', [Order::TYPE_STOCK, 'stock'])
            ->where('status', Order::STATUS_PENDING_PAYMENT)
            ->where('created_at', '<', now()->subHour())
            ->delete();

        // Reutilizar una orden contenedora pendiente reciente si existe, en
        // vez de crear una nueva en cada reintento de checkout.
        $existingOrder = Order::where('user_id', $user->id)
            ->where('status', Order::STATUS_PENDING_PAYMENT)
            ->whereIn('type', [Order::TYPE_STOCK, 'stock'])
            ->latest()
            ->first();

        if ($existingOrder) {
            $existingOrder->update([
                'address_id' => $address->id,
                'customer_name' => $shippingData['customer_name'].' '.$shippingData['customer_lastname'],
                'customer_email' => $shippingData['customer_email'],
                'customer_phone' => $shippingData['customer_phone'],
                'shipping_address' => $shippingData['shipping_address'],
                'shipping_city' => $shippingData['shipping_city'],
                'shipping_province' => $shippingData['shipping_province'],
                'shipping_zip' => $shippingData['shipping_zip'],
            ]);

            // Repoblar los items con el estado actual del carrito. Las
            // órdenes personalizadas vinculadas no se borran, solo el
            // OrderItem que las enlaza.
            $existingOrder->items()->delete();

            $existingOrder->update([
                'shipping_cost' => $this->calculateShippingCost($shippingData['shipping_city']),
            ]);

            Log::info("Reusing existing Pending Order ID: {$existingOrder->id}");

            return $existingOrder;
        }

        return Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => Order::STATUS_PENDING_PAYMENT,
            'customer_name' => $shippingData['customer_name'].' '.$shippingData['customer_lastname'],
            'customer_email' => $shippingData['customer_email'],
            'customer_phone' => $shippingData['customer_phone'],
            'shipping_address' => $shippingData['shipping_address'],
            'shipping_city' => $shippingData['shipping_city'],
            'shipping_province' => $shippingData['shipping_province'],
            'shipping_zip' => $shippingData['shipping_zip'],
            'total_amount' => 0,
            'shipping_cost' => $this->calculateShippingCost($shippingData['shipping_city']),
            'type' => Order::TYPE_STOCK,
        ]);
    }

    private function processCartItems(Order $order, Collection $cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            if ($cartItem->custom_order_id) {
                $this->processCustomOrderItem($order, $cartItem);

                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'custom_order_id' => null,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);
        }
    }

    private function processCustomOrderItem(Order $order, $cartItem): void
    {
        $customOrder = Order::find($cartItem->custom_order_id);

        if (! $customOrder) {
            return;
        }

        $customItem = $customOrder->items()->whereNull('product_id')->first();

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => null,
            'custom_order_id' => $customOrder->id,
            'custom_description' => $customItem ? $customItem->custom_description : 'Pedido Personalizado #'.$customOrder->id,
            'price' => $cartItem->price,
            'quantity' => 1,
            'images' => $customItem ? $customItem->images : [],
            'custom_specs' => $customItem ? $customItem->custom_specs : [],
        ]);

        // No se marca como LINKED antes de que el pago se apruebe: se deja
        // en IN_CART mientras dura el checkout, así cancelar el pago no
        // deja pedidos personalizados huérfanos en estado LINKED.
        if ($customOrder->status === Order::STATUS_PENDING_PAYMENT) {
            $customOrder->update(['status' => Order::STATUS_IN_CART]);
        }
    }

    private function validateProcessingOrder(?Order $order): void
    {
        if (! $order) {
            throw new BusinessLogicException('Orden no encontrada durante el procesamiento (Race Condition check).');
        }

        if ($order->status === Order::STATUS_PAID) {
            throw new BusinessLogicException('Esta orden ya fue procesada anteriormente.');
        }
    }

    private function handleCustomOrderLinking(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->custom_order_id) {
                $customOrder = Order::find($item->custom_order_id);
                if ($customOrder && $customOrder->status !== Order::STATUS_LINKED) {
                    $customOrder->update(['status' => Order::STATUS_LINKED]);
                }
            }
        }
    }

    private function decrementOrderStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = Product::find($item->product_id);

            if (! $product) {
                continue;
            }

            if ($product->stock < $item->quantity) {
                throw new BusinessLogicException("Stock insuficiente para el producto '{$product->name}'. La compra ha sido revertida.");
            }

            $product->decrement('stock', $item->quantity);

            if ($product->refresh()->stock <= 0) {
                $product->update(['status' => 'draft']);
                Log::info("Producto ID {$product->id} marcado como borrador por falta de stock.");
            }
        }
    }

    private function clearUserCart(Order $order): void
    {
        if ($order->user) {
            $order->user->cartItems()->delete();
        }
    }

    private function sendOrderEmails(Order $order): void
    {
        try {
            Mail::to($order->customer_email)->send(new OrderPaidNotification($order));
        } catch (\Exception $e) {
            Log::error('Error sending OrderPaid email: '.$e->getMessage());
        }

        try {
            $adminEmail = config('mail.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewOrderAdminNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Error sending NewOrderAdminNotification (Paid): '.$e->getMessage());
        }
    }

    private function finalizeOrderTotal(Order $order): void
    {
        $order->total_amount = $order->recalculateTotal();
        $order->save();
    }

    private function calculateShippingCost(string $city): float
    {
        return strtolower(trim($city)) === 'quito' ? 3.00 : 5.00;
    }
}
