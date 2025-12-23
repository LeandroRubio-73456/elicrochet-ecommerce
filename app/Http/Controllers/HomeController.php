<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Providers\CartService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Productos Destacados
        // Solo trae los que tienen is_featured = 1 (true)
        $featuredProducts = Product::where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // 2. Categorías Destacadas
        // Trae las 6 categorías con más productos asociados
        $categories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(6)
            ->get();

        return view('front.home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }

    public function shop()
    {
        // 1. Obtener categorías con contador de productos y solo las activas si aplica
        $categories = Category::withCount('products')
            ->where('status', 'active') // Asumiendo que hay status
            ->get();

        // 2. Obtener productos con paginación
        $products = Product::where('status', 'active') // Asumiendo status
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('front.shop', compact('categories', 'products'));
    }

    public function notfound()
    {
        return view('front.404');
    }

    public function bestseller()
    {
        return view('front.bestseller');
    }

    public function cart()
    {
        return view('front.cart');
    }

    public function checkout(CartService $cartService)
    {
        $cartItems = $cartService->getCart();
        $total = $cartService->getTotal();

        return view('front.checkout', compact('cartItems', 'total'));
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function single(string $slug)
    {
        // 1. Buscar el producto por su slug. Si no existe, lanza un error 404
        $product = Product::where('slug', $slug)
            ->with(['category', 'images']) // Opcional: Eager load las relaciones
            ->firstOrFail();

        // 2. Puedes obtener productos relacionados aquí si lo deseas
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        // 3. Pasar el producto y los relacionados a la vista
        return view('front.single', compact('product', 'relatedProducts'));
    }

    public function categoryShow(string $slug)
    {
        // 1. Buscar la categoría por su slug
        $category = Category::where('slug', $slug)->firstOrFail();

        // 2. Obtener TODAS las categorías para el sidebar
        $categories = Category::withCount('products')
            ->where('status', 'active')
            ->get();

        // 3. Obtener los productos de esa categoría
        $products = Product::where('category_id', $category->id)
            ->with('images')
            ->where('status', 'active')
            ->paginate(12);
        
        // 4. Devolver la vista de la tienda pasando todo
        return view('front.shop', compact('category', 'categories', 'products'));
    }

    public function addToCart(Request $request, $id)
    {
        // Lógica del carrito pendiente de implementar
        // o usar una librería de carrito
        
        return redirect()->back()->with('success', 'Producto añadido al carrito (Simulación)');
    }
}