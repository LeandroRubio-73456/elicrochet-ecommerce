<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
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

            // 1. Filtrado Global (Search)
            if ($request->has('search') && ! empty($request->input('search.value'))) {
                $searchValue = $request->input('search.value');
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                        ->orWhere('description', 'like', "%{$searchValue}%")
                        ->orWhereHas('category', function ($catQ) use ($searchValue) {
                            $catQ->where('name', 'like', "%{$searchValue}%");
                        });
                });
            }

            // 2. Filtro por Columna (Status y Category)
            if ($request->has('columns')) {
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

            // 3. Ordenamiento
            if ($request->has('order')) {
                $orderColumnIndex = $request->input('order.0.column');
                $orderDirection = $request->input('order.0.dir');
                $columns = ['id', 'images', 'name', 'category_id', 'price', 'stock', 'status', 'actions'];

                if (isset($columns[$orderColumnIndex]) && $columns[$orderColumnIndex] !== 'actions' && $columns[$orderColumnIndex] !== 'images') {
                    $columnName = $columns[$orderColumnIndex];
                    if ($columnName === 'category_id') { // Ordenar por nombre de categoría
                        $query->join('categories', 'products.category_id', '=', 'categories.id')
                            ->orderBy('categories.name', $orderDirection)
                            ->select('products.*');
                    } else {
                        $query->orderBy($columnName, $orderDirection);
                    }
                }
            } else {
                $query->latest('id');
            }

            // 4. Paginación
            $totalRecords = Product::count();
            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $products = $query->skip($start)->take($length)->get();

            // 5. Transformación de datos para DataTables
            $data = $products->map(function ($product) {
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

                // Acciones (Updated Routes to admin.)
                $editUrl = route('admin.products.edit', $product);
                $showUrl = route('admin.products.show', $product);
                $deleteUrl = route('admin.products.destroy', $product);

                $actions = '
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="'.$editUrl.'" class="btn btn-outline-primary"><i class="ti-pencil"></i></a>
                        <a href="'.$showUrl.'" class="btn btn-outline-info"><i class="ti-eye"></i></a>
                        <button type="button" class="btn btn-outline-danger delete-product-btn" data-product-id="'.$product->id.'" 
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
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        }

        $categories = Category::all();

        // Updated View Path (back.products.index)
        return view('back.products.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('back.products.create', compact('categories'));
    }

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
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
            'specs' => 'nullable|array', // Basic Validation
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        // Dynamic Specs Validation
        $category = Category::findOrFail($validated['category_id']);
        $specErrors = $category->validateSpecs($request->specs ?? []);

        if (! empty($specErrors)) {
            return back()->withErrors($specErrors)->withInput();
        }
        $validated['specs'] = $request->specs ?? [];

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Product::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug.'-'.$count++;
            }
        }

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto "'.$product->name.'" creado exitosamente.');
    }

    public function show(Product $product)
    {
        return view('back.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('back.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,'.$product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:draft,active,out_of_stock,discontinued,archived',
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
            'specs' => 'nullable|array',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        // Dynamic Specs Validation
        $category = Category::findOrFail($validated['category_id']);
        // Note: If category changed, we validate against NEW category.
        // If category didn't change, we validate against existing.
        $specErrors = $category->validateSpecs($request->specs ?? []);

        if (! empty($specErrors)) {
            return back()->withErrors($specErrors)->withInput();
        }
        $validated['specs'] = $request->specs ?? [];

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.',
        ]);
    }
}
