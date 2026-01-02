@extends('layouts.back-layout')

@section('title', 'Añadir Producto')

@section('content')
    @include('layouts.breadcrumb', [
        'item' => 'Productos',
        'active' => 'Crear Nuevo',
    ])
    <h4 class="m-0 mb-3">Añadir un nuevo producto</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    {{-- Columna Izquierda --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Ej. Amigurumi Foxy" value="{{ old('name') }}" required>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label fw-bold">URL amigable (slug)</label>
                            <input type="text" name="slug" id="slug" class="form-control" placeholder="Ej. amigurumi-foxy" value="{{ old('slug') }}">
                            <small class="text-muted">Si lo dejas vacío, se generará automáticamente</small>
                            @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="category_id" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="description" rows="8" required placeholder="Describe tu producto...">{{ old('description') }}</textarea>
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
                                <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" value="{{ old('price') }}" required>
                            </div>
                            @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label fw-bold">Stock Disponible <span class="text-danger">*</span></label>
                            <input type="number" name="stock" id="stock" class="form-control" min="0" value="{{ old('stock', 0) }}" required>
                            @error('stock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Estado</label>
                            <div class="row g-3">
                                @foreach (['draft' => ['Borrador', 'warning', 'pencil'], 'active' => ['Activo', 'success', 'eye']] as $val => [$label, $color, $icon])
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="status_{{ $val }}" value="{{ $val }}" {{ old('status', 'draft') == $val ? 'checked' : '' }} required>
                                            <label class="form-check-label fw-bold" for="status_{{ $val }}">
                                                <i class="ti ti-{{ $icon }} text-{{ $color }} me-1"></i> {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold">Imágenes</label>
                            <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                            <small class="text-muted d-block mt-1">Sube hasta 5 imágenes (máx. 5MB c/u).</small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary"><i class="ti ti-x me-1"></i> Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_id');
        const container = document.getElementById('dynamicSpecsContainer');
        const row = document.getElementById('dynamicSpecsRow');
        const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'specs' => $c->required_specs]));
        const oldSpecs = @json(old('specs', []));

        function renderSpecs(catId) {
            const category = categories.find(c => c.id == catId);
            row.innerHTML = '';
            if (category && category.specs && category.specs.length > 0) {
                container.classList.remove('d-none');
                category.specs.forEach(spec => {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 mb-3';
                    const fieldName = `specs[${spec.name}]`;
                    const isRequired = spec.required ? 'required' : '';
                    const label = `<label class="form-label small fw-bold text-uppercase">${spec.name} ${spec.required ? '<span class="text-danger">*</span>' : ''}</label>`;
                    const oldValue = oldSpecs[spec.name] || '';
                    let inputHtml = spec.type === 'select' 
                        ? `<select name="${fieldName}" class="form-select form-select-sm" ${isRequired}><option value="">Seleccionar...</option>${spec.options.map(opt => `<option value="${opt}" ${oldValue == opt ? 'selected' : ''}>${opt}</option>`).join('')}</select>`
                        : `<input type="${spec.type === 'number' ? 'number' : 'text'}" name="${fieldName}" class="form-control form-control-sm" value="${oldValue}" ${isRequired}>`;
                    col.innerHTML = label + inputHtml;
                    row.appendChild(col);
                });
            } else {
                container.classList.add('d-none');
            }
        }
        categorySelect.addEventListener('change', e => renderSpecs(e.target.value));
        if(categorySelect.value) renderSpecs(categorySelect.value);
    });
</script>
@endpush
