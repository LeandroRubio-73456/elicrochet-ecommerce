<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.head-page-meta', ['title' => 'Añadir Producto']) @include('layouts.head-css')
</head>
<body @bodySetup>
    @include('layouts.layout-vertical')

    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', [
                'breadcrumb-item' => 'Productos',
                'breadcrumb-item-active' => 'Crear Nuevo',
            ])
            <h5 class="">Añadir un nuevo producto</h5>

            {{-- Contenedor Principal (Reemplazado) --}}
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('back.products.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            {{-- Columna Izquierda: Información Básica --}}
                            <div class="col-md-6">
                                {{-- Quitamos el <div class="card"> interno para simplificar el diseño --}}
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Nombre del Producto <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Ej. Amigurumi Foxy" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label fw-bold">URL amigable (slug)</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        placeholder="Ej. amigurumi-foxy" value="{{ old('slug') }}">
                                    <small class="text-muted">Si lo dejas vacío, se generará automáticamente</small>
                                    @error('slug')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-bold">Categoria <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" id="category_id" required>
                                        <option value="">Selecciona una categoría</option>
                                        {{-- Asegúrate de que $categories se pase desde el controlador --}}
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="price" class="form-label fw-bold">Precio (USD) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" id="price" class="form-control"
                                            step="0.01" min="0" placeholder="25.99"
                                            value="{{ old('price') }}" required>
                                    </div>
                                    @error('price')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Columna Derecha: Inventario, Medios y Estado --}}
                            <div class="col-md-6">
                                {{-- Quitamos el <div class="card"> interno para simplificar el diseño --}}

                                <div class="mb-3">
                                    <label for="stock" class="form-label fw-bold">Stock Disponible <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="stock" id="stock" class="form-control"
                                        min="0" placeholder="10" value="{{ old('stock', 0) }}" required>
                                    @error('stock')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Estado del Producto</label>
                                    <div class="row g-3">
                                        @foreach ([
                                            'draft' => ['Borrador', 'No visible en tienda', 'warning', 'fa-edit'],
                                            'active' => ['Activo', 'Visible en tienda', 'success', 'fa-eye'],
                                            // Puedes agregar más estados aquí si es necesario (ej: out_of_stock, archived)
                                        ] as $value => [$label, $description, $color, $icon])
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="status_{{ $value }}" value="{{ $value }}"
                                                        {{ old('status', 'draft') == $value ? 'checked' : '' }}
                                                        required>
                                                    <label class="form-check-label fw-bold d-flex align-items-center"
                                                        for="status_{{ $value }}">
                                                        <i
                                                            class="fas {{ $icon }} text-{{ $color }} fa-lg me-2"></i>
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                                <p class="text-muted small mb-0 mt-2 ms-4">{{ $description }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('status')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="images" class="form-label fw-bold">Imágenes del Producto</label>
                                    <input type="file" name="images[]" id="images" class="form-control"
                                        multiple accept="image/*">
                                    <small class="text-muted">Puedes seleccionar múltiples imágenes, max. 5, con peso
                                        máximo de 5MB por imagen. Recomendada 640x640.</small>
                                    @error('images')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('images.*')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Descripción <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="description" rows="4" required placeholder="Describe tu producto de crochet (materiales, medidas, etc.)">{{ old('description') }}</textarea>
                            <small class="text-muted">Describe tu producto de crochet (materiales, medidas,
                                etc.)</small>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('back.products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer-block')

    <script src="{{ asset('assets/js/admin/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/admin/pages/dashboard-default.js') }}"></script>
    @include('layouts.footer-js')
</body>
</html>