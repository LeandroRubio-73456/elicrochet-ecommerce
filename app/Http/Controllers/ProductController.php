<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Foundation\Console\StorageLinkCommand;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Inicializar la consulta con eager loading (relación Category)
        $query = Product::with('category');

        // --- FILTROS CONDICIONALES ---

        // 2. Filtrar por Estado (si está presente Y es un valor válido)
        $allowedStatuses = ['draft', 'active', 'out_of_stock', 'discontinued', 'archived'];

        if ($request->filled('status') && in_array($request->status, $allowedStatuses)) {
            $query->where('status', $request->status);
        }

        // 3. Filtrar por Categoría (si está presente y tiene un valor)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 4. Buscar por Nombre Y Descripción (si está presente y tiene un valor)
        if ($request->filled('search')) { //  CORRECCIÓN 1: Usar filled()
            $searchTerm = $request->search;
            //  CORRECCIÓN 2: Buscar en Name O Description
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // 5. Ordenar y Ejecutar la consulta
        $products = $query->latest('id')->paginate(15);

        // 6. Obtener categorías para rellenar el select del filtro
        $categories = Category::all();

        return view('back.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('back.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:draft,active,out_of_stock,discontinued,archived',
            // [NUEVO] Permitir que is_featured sea opcional y sea tratado como booleano
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
        ]);

        // [LÓGICA ADICIONAL] Manejar el Checkbox y el Slug

        // Asignar el valor del checkbox: true si fue enviado, false si no.
        $validated['is_featured'] = $request->has('is_featured');

        // Generar slug automático si no se proporcionó
        if (empty($validated['slug'])) {
            // ... [Lógica de generación de slug] ...
            $validated['slug'] = Str::slug($validated['name']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Product::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Crear producto
        $product = Product::create($validated);

        // Guardar imágenes si hay
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');

                $product->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('back.products.index')
            ->with('success', 'Producto "' . $product->name . '" creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('back.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('back.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // 1. Validar los datos del formulario (debes incluir todas las reglas)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Asegúrate de que el slug es único, excepto para el producto actual
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:draft,active,out_of_stock,discontinued,archived',
            // [NUEVO] Validación para el campo destacado
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
        ]);

        // 2. [LÓGICA CRUCIAL] Manejar el Checkbox y el Slug ANTES de actualizar

        // Asignar el valor del checkbox: true si fue enviado, false si no.
        $validated['is_featured'] = $request->has('is_featured');

        // Generar slug si el campo está vacío (similar a store)
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Opcional: Manejar la unicidad del slug
        }


        // 3. Actualizar los campos del producto
        // Usamos $validated directamente, ya que contiene todos los campos necesarios.
        $product->update($validated);

        // 4. Actualizar imágenes si se proporcionan nuevas (Solo Añadir)
        if ($request->hasFile('images')) {
            // ... (Lógica de subir y guardar imágenes, que ya está correcta)
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');

                $product->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        // 5. Redireccionar al usuario
        return redirect()->route('back.products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.'
        ]);
    }
}
