<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filtro por estado
        if ($request->has('status') && in_array($request->status, ['active', 'inactive', 'archived'])) {
            $query->where('status', $request->status);
        }

        $categories = $query->get();

        return view('back.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('back.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,archived',
        ]);

        // Generar slug automático si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Si el slug ya existe, añadir número
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Category::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Crear categoria
        $category = Category::create($validated);

        return redirect()->route('back.categories.index')
            ->with('success', 'Producto "' . $category->name . '" creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('back.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // 1. Validación Corregida: Ignora el slug actual y corrige la lista de estados
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,archived',
        ]);

        $data = $request->all();

        // 2. Lógica para manejar el slug (igual que en el store)
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // 3. Actualizar los campos de la Categoría
        $category->update($data); // O $category->update($request->only(['name', 'slug', 'description', 'status']));

        // 4. Redireccionar al usuario (¡Ruta y mensaje de Categoría!)
        return redirect()->route('back.categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada correctamente.'
        ]);
    }
}
