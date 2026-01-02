<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        if ($request->ajax()) {
            $query = Category::withCount('products');

            $this->applyFilters($query, $request);

            // 4. Paginación
            $totalRecords = Category::count();
            $filteredRecords = $query->count();
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $categories = $query->skip($start)->take($length)->get();

            // 5. Transformación
            $data = $categories->map(function ($category) {
                // Icono
                $iconClass = $category->icon ?? 'ti ti-folder';
                if (!str_contains($iconClass, 'ti ')) {
                    $iconClass = str_replace('ti-', 'ti ti-', $iconClass);
                }
                $iconHtml = '<i class="'.$iconClass.' text-primary f-14 bg-light-primary p-2 rounded"></i>';

                // Productos link
                $productsLink = $category->products_count > 0
                    ? '<a href="'.route('admin.products.index', ['category_id' => $category->id]).'" class="badge bg-light-primary text-primary f-12">'.$category->products_count.' Productos</a>'
                    : '<span class="badge bg-light-secondary f-12">Sin productos</span>';

                // Estado
                $statusBadge = match ($category->status) {
                    'active' => '<span class="badge bg-light-success f-12">Activo</span>',
                    'inactive' => '<span class="badge bg-light-secondary f-12">Inactivo</span>',
                    'archived' => '<span class="badge bg-light-warning f-12">Archivado</span>',
                    default => '<span class="badge bg-light-dark f-12">'.$category->status.'</span>',
                };

                // Acciones
                $editUrl = route('admin.categories.edit', $category->id);
                $deleteUrl = route('admin.categories.destroy', $category->id);

                $actions = '
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="'.$editUrl.'" class="btn btn-outline-primary"><i class="ti ti-pencil"></i></a>
                        <button type="button" class="btn btn-outline-danger delete-category-btn"
                            data-category-id="'.$category->id.'"
                            data-action-url="'.$deleteUrl.'"
                            data-category-name="'.$category->name.'">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>';

                return [
                    'id' => $category->id,
                    'icon' => $iconHtml,
                    'name' => '<h6 class="mb-0">'.$category->name.'</h6><small class="text-muted">'.Str::limit($category->description, 50).'</small>',
                    'slug' => $category->slug,
                    'products_count' => $productsLink,
                    'status' => $statusBadge,
                    'actions' => $actions,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        }

        return view('back.categories.index');
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
            'icon' => 'nullable|string|max:50',
        ]);

        // Generar slug automático si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Si el slug ya existe, añadir número
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Category::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug.'-'.$count++;
            }
        }

        // Process Specs
        if ($request->has('required_specs')) {
            $validated['required_specs'] = json_decode($request->required_specs, true);
        }

        // Crear categoria
        $category = Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría "'.$category->name.'" creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
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
            'slug' => 'nullable|string|max:255|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,archived',
            'icon' => 'nullable|string|max:50',
        ]);

        $data = $request->all();

        // 2. Lógica para manejar el slug (igual que en el store)
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Process Specs
        if ($request->has('required_specs')) {
            $data['required_specs'] = json_decode($request->required_specs, true);
        }

        // 3. Actualizar los campos de la Categoría
        $category->update($data);

        // 4. Redireccionar al usuario (¡Ruta y mensaje de Categoría!)
        return redirect()->route('admin.categories.index')
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
            'message' => 'Categoría eliminada correctamente.',
        ]);
    }

    /**
     * Apply filters, search and sorting to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // 1. Busqueda Global
        if ($request->has('search') && ! empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('description', 'like', "%{$searchValue}%");
            });
        }

        // 2. Filtro por Estado (Columna 5)
        if ($request->has('columns')) {
            $statusSearch = $request->input('columns.5.search.value');
            if (! empty($statusSearch) && in_array($statusSearch, ['active', 'inactive', 'archived'])) {
                $query->where('status', $statusSearch);
            }
        }

        // 3. Ordenamiento
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $columns = ['id', 'icon', 'name', 'slug', 'products_count', 'status', 'actions'];

            if (isset($columns[$orderColumnIndex]) && $columns[$orderColumnIndex] !== 'actions' && $columns[$orderColumnIndex] !== 'icon') {
                $query->orderBy($columns[$orderColumnIndex], $orderDirection);
            }
        } else {
            $query->latest('id');
        }
    }
}
