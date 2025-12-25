<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Providers\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getCart();
        $cartTotal = $this->cartService->getTotal();

        return view('front.cart', compact('cartItems', 'cartTotal'));
    }

    public function addToCart(Request $request, Product $product)
    {
        try {
            DB::beginTransaction();

            // Lock the product row to ensure we read the real stock right now
            $freshProduct = Product::lockForUpdate()->find($product->id);

            if (! $freshProduct) {
                throw new \Exception('Producto no encontrado.');
            }

            if ($freshProduct->stock < $request->input('quantity', 1)) {
                throw new \Exception("Stock insuficiente (Actual: {$freshProduct->stock}). Por favor intenta con menos cantidad.");
            }

            $this->cartService->addToCart(
                $freshProduct,
                $request->input('quantity', 1),
                [
                    'image' => $freshProduct->images->first()?->image_path,
                    'slug' => $freshProduct->slug,
                ]
            );

            DB::commit();

            return redirect()->route('cart')
                ->with('success', '¡'.$freshProduct->name.' agregado al carrito!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function remove($productId)
    {
        $this->cartService->removeFromCart($productId);

        return back()->with('success', 'Producto eliminado del carrito');
    }

    public function update(Request $request)
    {
        $this->cartService->updateQuantity($request->product_id, $request->quantity);

        // Recalcular
        $cartItems = $this->cartService->getCart();
        $item = $cartItems->where('product_id', $request->product_id)->first();

        return response()->json([
            'success' => true,
            'cartTotal' => number_format($this->cartService->getTotal(), 2),
            'cartCount' => $this->cartService->getCount(),
            'itemTotal' => $item ? number_format($item->subtotal, 2) : '0.00',
        ]);
    }

    // Ruta pública para mensaje
    public function showMessage()
    {
        return view('auth.cart-login-required');
    }
}
