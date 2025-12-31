<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['category', 'images']);

            $this->applyFilters($query, $request);

            // 4. Paginación
            $totalRecords = Product::count();
            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $products = $query->skip($start)->take($length)->get();

            // 5. Transformación de datos para DataTables
            $data = $products->map(fn($product) => $this->transformProduct($product));

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        }

        $categories = Category::all();

        return view('back.products.index', compact('categories'));
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
                $validated['slug'] = $originalSlug.'-'.$count++;
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
            ->with('success', 'Producto "'.$product->name.'" creado exitosamente.');
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
        $validated = $this->validateProduct($request, $product->id);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['slug'] = $this->ensureSlug($validated['slug'] ?? null, $validated['name'], $product->id);

        $product->update($validated);

        if ($request->hasFile('images')) {
            $this->handleImageUploads($request->file('images'), $product);
        }

        return redirect()->route('back.products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.',
        ]);
        }

    private function applyFilters($query, Request $request)
    {
        $this->applySearch($query, $request);
        $this->applyColumnFilters($query, $request);
        $this->applySorting($query, $request);
    }

    private function applySearch($query, Request $request)
    {
        if ($request->has('search') && ! empty($request->input('search.value'))) {
            $this->addSearchConditions($query, $request->input('search.value'));
        }
    }

    private function addSearchConditions($query, $searchValue)
    {
        $query->where(function ($q) use ($searchValue) {
            $q->where('name', 'like', "%{$searchValue}%")
                ->orWhere('description', 'like', "%{$searchValue}%")
                ->orWhereHas('category', function ($catQ) use ($searchValue) {
                    $catQ->where('name', 'like', "%{$searchValue}%");
                });
            });

    }

    private function applyColumnFilters($query, Request $request)
    {
        if (! $request->has('columns')) {
            return;
        }

        // Filtro Estado (Columna 6)
        $statusSearch = $request->input('columns.6.search.value');
        if (! empty($statusSearch)) {
            $query->where('status', $statusSearch);
        }

        // Filtro Categoría (Columna 3 - Nombre de categoría)
        $categorySearch = $request->input('columns.3.search.value');
        if (! empty($categorySearch)) {
            $query->whereHas('category', function ($q) use ($categorySearch) {
                $q->where('name', $categorySearch);
            });
        }
    }

    private function applySorting($query, Request $request)
    {
        if (! $request->has('order')) {
            $query->latest('id');
            return;
        }

        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columns = ['id', 'images', 'name', 'category_id', 'price', 'stock', 'status', 'actions'];

        if (isset($columns[$orderColumnIndex]) && !in_array($columns[$orderColumnIndex], ['actions', 'images'])) {
            $columnName = $columns[$orderColumnIndex];
            if ($columnName === 'category_id') {
                $query->join('categories', 'products.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', $orderDirection)
                    ->select('products.*');
            } else {
                $query->orderBy($columnName, $orderDirection);
            }
        }
    }

    private function transformProduct($product)
    {
        // Imagen
        $imageHtml = '<div class="d-flex align-items-center"><div class="rounded me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="ti ti-photo text-muted"></i></div></div>';
        if ($product->images->count() > 0) {
            $imageHtml = '<div class="d-flex align-items-center"><img src="'.asset('storage/'.$product->images->first()->image_path).'" alt="" class="rounded me-2" width="40" height="40" style="object-fit: cover;"></div>';
        }

        // Estado (Badge)
        $statusBadge = match ($product->status) {
            'draft' => '<span class="badge bg-light-secondary f-12">Borrador</span>',
            'active' => '<span class="badge bg-light-success f-12">Activo</span>',
            'out_of_stock' => '<span class="badge bg-light-danger f-12">Sin Stock</span>',
            'discontinued' => '<span class="badge bg-light-dark f-12">Descontinuado</span>',
            'archived' => '<span class="badge bg-light-warning f-12">Archivado</span>',
            default => '<span class="badge bg-light-secondary f-12">'.$product->status.'</span>',
        };

        if ($product->status === 'active' && $product->stock <= 0) {
            $statusBadge .= '<span class="badge bg-danger mt-1 f-12"><i class="ti-exclamation-triangle me-1"></i> Sin Stock</span>';
        }

        // Acciones
        $editUrl = route('back.products.edit', $product);
        $showUrl = route('back.products.show', $product);
        $deleteUrl = route('back.products.destroy', $product);

        $actions = '
            <div class="d-flex gap-2 justify-content-center">
                <a href="'.$editUrl.'" class="btn btn-outline-primary"><i class="ti-pencil"></i></a>
                <a href="'.$showUrl.'" class="btn btn-outline-info"><i class="ti-eye"></i></a>
                <button type="button" class="btn btn-outline-danger delete-product-btn" 
                    data-product-id="'.$product->id.'" 
                    data-action-url="'.$deleteUrl.'">
                    <i class="ti-trash"></i>
                </button>
            </div>';

        return [
            'id' => $product->id,
            'image' => $imageHtml,
            'name' => '<h6 class="mb-0">'.$product->name.'</h6>',
            'category' => $product->category ? $product->category->name : 'N/A',
            'price' => '$'.number_format($product->price, 2),
            'stock' => $product->stock,
            'status' => $statusBadge,
            'actions' => $actions,
        ];
    }

    private function validateProduct(Request $request, $ignoreId = null)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $ignoreId,
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:draft,active,out_of_stock,discontinued,archived',
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
        ]);
    }

    private function ensureSlug($slug, $name, $ignoreId = null)
    {
        if (!empty($slug)) {
            return $slug;
        }

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    private function handleImageUploads($images, Product $product)
    {
        foreach ($images as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['image_path' => $path]);
        }
    }
}
