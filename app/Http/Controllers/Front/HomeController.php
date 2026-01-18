<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Productos Destacados
        // Solo trae los que tienen is_featured = 1 (true)
        $featuredProducts = Product::with('images')
            ->where('is_featured', true)
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

    public function shop(Request $request)
    {
        // 1. Obtener categorías
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('status', 'active');
        }])
            ->where('status', 'active')
            ->get();

        // 2. Query base de productos
        $query = Product::with('images')->where('status', 'active');

        // 2.0 Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2.1 Filtro de Precios
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 2.2 Ordenamiento
        $sort = $request->input('sort', 'newest'); // Por defecto 'newest'
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                // Si tienes un campo de views o sales_count, úsalo. Si no, por ahora id.
                // $query->orderBy('sales_count', 'desc');
                $query->orderBy('id', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 3. Paginar
        $products = $query->paginate(12)->withQueryString();

        return view('front.shop', compact('categories', 'products'));
    }

    public function notfound()
    {
        return view('errors.404');
    }

    public function bestseller()
    {
        return view('front.bestseller');
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|in:general,custom_order,order_status,wholesale',
            'message' => 'required|string|max:1000',
            'policy_check' => 'accepted',
        ]);

        // Here you would typically send an email.
        // Mail::to('admin@elicrochet.com')->send(new ContactFormMail($request->validated()));

        return back()->with('success', '¡Gracias por contactarnos! Tu mensaje ha sido enviado correctamente.');
    }

    public function single(string $slug)
    {
        // 1. Buscar el producto por su slug. Si no existe, lanza un error 404
        $product = Product::where('slug', $slug)
            ->with(['category', 'images', 'reviews.user' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        // 2. Puedes obtener productos relacionados aquí si lo deseas
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        // 3. Pasar el producto y los relacionados a la vista
        return view('front.single', compact('product', 'relatedProducts'));
    }

    public function categoryShow(Request $request, string $slug)
    {
        // 1. Buscar la categoría por su slug
        $category = Category::where('slug', $slug)->firstOrFail();

        // 2. Obtener TODAS las categorías para el sidebar
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('status', 'active');
        }])
            ->where('status', 'active')
            ->get();

        // 3. Query base
        $query = Product::where('category_id', $category->id)
            ->with('images')
            ->where('status', 'active');

        // 3.0 Búsqueda dentro de categoría
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 3.1 Filtro de Precios
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 3.2 Ordenamiento
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('id', 'desc'); // Placeholder
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 4. Paginar
        $products = $query->paginate(12)->withQueryString();

        return view('front.shop', compact('category', 'categories', 'products'));
    }
}
