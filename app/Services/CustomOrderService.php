<?php

namespace App\Services;

use App\Mail\CustomOrderReceived;
use App\Mail\NewOrderAdminNotification;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Crea cotizaciones de pedidos personalizados y notifica por correo al
 * cliente y al administrador. Customer\OrderController se limita a validar
 * la petición, subir las imágenes y llamar a este servicio.
 */
class CustomOrderService
{
    /**
     * Un usuario solo puede tener una cotización de pedido personalizado
     * activa a la vez (regla anti-spam).
     */
    public function hasActiveQuotation(User $user): bool
    {
        return Order::where('user_id', $user->id)
            ->where('type', Order::TYPE_CUSTOM)
            ->where('status', Order::STATUS_QUOTATION)
            ->exists();
    }

    public function create(User $user, Category $category, string $description, array $customSpecs, array $imagePaths): Order
    {
        $order = new Order;
        $order->user_id = $user->id;
        $order->status = Order::STATUS_QUOTATION;
        $order->type = Order::TYPE_CUSTOM;
        $order->customer_name = $user->name;
        $order->customer_email = $user->email;

        // Usar la información de envío por defecto del usuario.
        $order->shipping_address = $user->shipping_address;
        $order->shipping_city = $user->shipping_city;
        $order->shipping_zip = $user->shipping_zip;

        $order->total_amount = 0; // Lo define el admin al cotizar
        $order->save();

        $item = new OrderItem;
        $item->order_id = $order->id;
        $item->product_id = null;
        $item->custom_description = "Categoría: {$category->name}\n\n".$description;
        $item->price = 0;
        $item->quantity = 1;
        $item->custom_specs = $customSpecs;
        $item->images = $imagePaths;
        $item->save();

        $this->sendCustomOrderEmails($order);

        return $order;
    }

    private function sendCustomOrderEmails(Order $order): void
    {
        try {
            Mail::to($order->customer_email)->queue(new CustomOrderReceived($order));
        } catch (\Exception $e) {
            Log::error('Error queuing CustomOrderReceived email: '.$e->getMessage());
        }

        try {
            $adminEmail = config('mail.admin_email') ?: User::where('role', 'admin')->value('email');

            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new NewOrderAdminNotification($order));
            } else {
                Log::warning('No admin email configured/found for NewOrderAdminNotification.');
            }
        } catch (\Exception $e) {
            Log::error('Error sending NewOrderAdminNotification: '.$e->getMessage());
        }
    }
}
