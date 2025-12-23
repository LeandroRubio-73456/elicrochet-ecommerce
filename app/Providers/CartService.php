<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{
    // Obtener todos los items del carrito del usuario actual
    public function getCart()
    {
        if (!Auth::check()) {
            return collect();
        }
        
        return CartItem::where('user_id', Auth::id())
            ->with(['product.images', 'customOrder']) // Eager load custom order
            ->get();
    }

    // Agregar producto al carrito
    public function addToCart(Product $product, $quantity = 1, $attributes = [])
    {
        if (!Auth::check()) {
            return false;
        }

        // Verificar si ya existe en el carrito para calcular el total
        $existingItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        $currentQuantityInCart = $existingItem ? $existingItem->quantity : 0;
        $totalQuantity = $currentQuantityInCart + $quantity;

        // Verificar stock con el total acumulado
        if ($totalQuantity > $product->stock) {
            throw new \Exception('Stock insuficiente. Ya tienes ' . $currentQuantityInCart . ' en el carrito y solo quedan ' . $product->stock . ' unidades disponibles en total.');
        }

        if ($existingItem) {
            // Actualizar cantidad
            $existingItem->quantity += $quantity;
            $existingItem->save();
        } else {
            // Crear nuevo item
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'attributes' => array_merge($attributes, [
                    'image' => $product->images->first()?->image_path,
                    'slug' => $product->slug,
                    'name' => $product->name
                ])
            ]);
        }

        return true;
    }

    // Agregar pedido personalizado al carrito
    public function addCustomOrder(\App\Models\Order $order)
    {
        if (!Auth::check()) {
            return false;
        }

        // Verificar si ya existe en el carrito
        $existingItem = CartItem::where('user_id', Auth::id())
            ->where('custom_order_id', $order->id)
            ->first();

        if ($existingItem) {
            throw new \Exception('Este pedido personalizado ya está en tu carrito.');
        }

        // Update Order Status to IN_CART to prevent other actions
        if ($order->status === \App\Models\Order::STATUS_PENDING_PAYMENT) {
            $order->update(['status' => \App\Models\Order::STATUS_IN_CART]);
        }
        
        // Crear nuevo item
        CartItem::create([
            'user_id' => Auth::id(),
            'product_id' => null, // No es un producto de catálogo
            'custom_order_id' => $order->id,
            'quantity' => 1,
            'price' => $order->total_amount,
            'attributes' => [
                'image' => $order->custom_image_path ?? null, // Usar imagen del pedido si tiene
                'name' => 'Pedido Personalizado #' . $order->id,
                'is_custom' => true
            ]
        ]);

        return true;
    }

    // Actualizar cantidad
    public function updateQuantity($productId, $quantity)
    {
        if (!Auth::check()) {
            return false;
        }

        $item = CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $item->quantity = $quantity;
            $item->save();
            return true;
        }

        return false;
    }

    // Eliminar del carrito
    public function removeFromCart($id)
    {
        if (!Auth::check()) {
            return false;
        }

        $item = CartItem::where('user_id', Auth::id())
            ->where(function($query) use ($id) {
                $query->where('product_id', $id)
                      ->orWhere('custom_order_id', $id);
            })
            ->first();

        if ($item) {
            // Revert status if it's a custom order
            if ($item->custom_order_id) {
                $order = \App\Models\Order::find($item->custom_order_id);
                if ($order && $order->status === \App\Models\Order::STATUS_IN_CART) {
                    $order->update(['status' => \App\Models\Order::STATUS_PENDING_PAYMENT]);
                }
            }
            $item->delete();
            return true;
        }
        return false;
    }

    // Vaciar carrito
    public function clearCart()
    {
        if (!Auth::check()) {
            return false;
        }

        // Revert status for all custom orders in cart before deleting
        $cartItems = CartItem::where('user_id', Auth::id())->whereNotNull('custom_order_id')->get();
        foreach ($cartItems as $item) {
            $order = \App\Models\Order::find($item->custom_order_id);
             if ($order && $order->status === \App\Models\Order::STATUS_IN_CART) {
                $order->update(['status' => \App\Models\Order::STATUS_PENDING_PAYMENT]);
            }
        }

        return CartItem::where('user_id', Auth::id())->delete();
    }

    // Obtener total del carrito
    public function getTotal()
    {
        return $this->getCart()->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    // Obtener cantidad total de items
    public function getCount()
    {
        return $this->getCart()->sum('quantity');
    }
}