<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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
        return view('front.shop');
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

    public function cheackout()
    {
        return view('front.cheackout');
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
        // 1. Buscar la categoría por su slug (y forzar 404 si no existe)
        $category = Category::where('slug', $slug)
                            ->firstOrFail();

        // 2. Obtener los productos de esa categoría
        // Opcional: paginar, filtrar por stock, etc.
        $products = Product::where('category_id', $category->id)
                           ->with('images') // Cargar imágenes
                           ->active()       // Solo mostrar activos (si el scope está disponible)
                           ->paginate(12);
        
        // 3. Devolver la vista de la tienda/categoría con los datos
        return view('front.shop', compact('category', 'products'));
    }
}
