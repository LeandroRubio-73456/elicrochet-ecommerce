@extends('layouts.back-layout')

@section('title', 'Editar Producto: ' . $product->name)

@section('content')
    @include('layouts.breadcrumb', [
        'item' => 'Productos',
        'active' => 'Editar',
    ])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4 m-0">Editar Producto: {{ $product->name }}</h4>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ti ti-check me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Columna Izquierda --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label fw-bold">URL amigable (slug)</label>
                                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $product->slug) }}">
                                    @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-bold">Categoría <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" id="category_id" required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">Descripción <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" id="description" rows="10" required>{{ old('description', $product->description) }}</textarea>
                                    @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <!-- Dynamic Specs Container -->
                                <div id="dynamicSpecsContainer" class="mb-3 d-none p-3 bg-light rounded border">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="ti ti-ruler me-2"></i>Especificaciones</h6>
                                    <div class="row" id="dynamicSpecsRow"></div>
                                </div>
                            </div>

                            {{-- Columna Derecha --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label fw-bold">Precio (USD) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" id="price" class="form-control" step="0.01" min="0.01" value="{{ old('price', $product->price) }}" required>
                                    </div>
                                    @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="stock" class="form-label fw-bold">Stock Disponible <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" id="stock" class="form-control" min="0" value="{{ old('stock', $product->stock) }}" required>
                                    @error('stock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch p-0 d-flex align-items-center gap-3">
                                        <input class="form-check-input ms-0" type="checkbox" id="is_featured_switch" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_featured_switch">Producto Destacado</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Estado</label>
                                    <div class="row g-2">
                                        @foreach (['draft' => ['Borrador','warning','pencil'], 'active' => ['Activo','success','eye'], 'discontinued' => ['Descontinuado','secondary','na'], 'archived' => ['Archivado','dark','archive']] as $val => [$label,$color,$icon])
                                            <div class="col-6">
                                                <div class="form-check card-radio p-2 border rounded">
                                                    <input class="form-check-input" type="radio" name="status" id="status_{{ $val }}" value="{{ $val }}" {{ old('status', $product->status) == $val ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="status_{{ $val }}">
                                                        <i class="ti ti-{{ $icon }} text-{{ $color }} me-1"></i> {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($product->images->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Imágenes Actuales</label>
                                        <div class="row g-2">
                                            @foreach ($product->images as $image)
                                                <div class="col-4 image-thumbnail-container">
                                                    <div class="border rounded p-1 text-center">
                                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid rounded mb-1" style="height: 60px; object-fit: cover;">
                                                        <button type="button" class="btn btn-sm btn-danger p-0 px-1 remove-image-btn" data-image-id="{{ $image->id }}"><i class="ti ti-trash"></i></button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="images" class="form-label fw-bold">Nuevas Imágenes</label>
                                    <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary"><i class="ti ti-x me-1"></i> Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Actualizar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/libs/sweetalert2.all.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Specs Logic
        const categorySelect = document.getElementById('category_id');
        const container = document.getElementById('dynamicSpecsContainer');
        const row = document.getElementById('dynamicSpecsRow');
        const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'specs' => $c->required_specs]));
        const existingSpecs = @json(old('specs', $product->specs ?? []));

        function renderSpecs(catId) {
            const category = categories.find(c => c.id == catId);
            row.innerHTML = '';
            if (category && category.specs && category.specs.length > 0) {
                container.classList.remove('d-none');
                category.specs.forEach(spec => {
                    const col = document.createElement('div'); col.className = 'col-6 mb-2';
                    const val = existingSpecs[spec.name] || '';
                    let input = spec.type === 'select' 
                        ? `<select name="specs[${spec.name}]" class="form-select form-select-sm">${spec.options.map(opt => `<option value="${opt}" ${val == opt ? 'selected' : ''}>${opt}</option>`).join('')}</select>`
                        : `<input type="${spec.type==='number'?'number':'text'}" name="specs[${spec.name}]" class="form-control form-control-sm" value="${val}">`;
                    col.innerHTML = `<label class="small fw-bold">${spec.name}</label>` + input;
                    row.appendChild(col);
                });
            } else { container.classList.add('d-none'); }
        }
        categorySelect.addEventListener('change', e => renderSpecs(e.target.value));
        if(categorySelect.value) renderSpecs(categorySelect.value);

        // Delete Image AJAX
        document.querySelectorAll('.remove-image-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.imageId;
                const parent = this.closest('.image-thumbnail-container');
                Swal.fire({ title: '¿Eliminar?', icon: 'warning', showCancelButton: true }).then(res => {
                    if (res.isConfirmed) {
                        fetch(`{{ url('products/images') }}/${id}`, {
                            method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ _method: 'DELETE' })
                        }).then(() => parent.remove());
                    }
                });
            });
        });
    });
</script>
@endpush
