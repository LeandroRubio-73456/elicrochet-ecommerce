@extends('layouts.back-layout')

@section('title', 'Editar Producto: ' . $product->name)

@section('content')
    @include('layouts.breadcrumb', [
        'item' => 'Productos',
        'active' => 'Editar',
    ])

    <!-- [ sample-page ] start -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4 m-0">Editar Producto: {{ $product->name }}</h4>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ti-check me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.products.update', $product) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Columna Izquierda: Información Básica -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Nombre del Producto
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Ej. Amigurumi Foxy"
                                        value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label fw-bold">URL amigable (slug)</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        placeholder="Ej. amigurumi-foxy"
                                        value="{{ old('slug', $product->slug) }}">
                                    <small class="text-muted">Si lo dejas vacío, se generará
                                        automáticamente</small>
                                    @error('slug')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-bold">Categoría
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="category_id" id="category_id" required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Dynamic Specs Container -->
                                <div id="dynamicSpecsContainer" class="mb-3 d-none p-3 bg-light rounded border">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="ti ti-ruler me-2"></i>Especificaciones del Producto</h6>
                                    <div class="row" id="dynamicSpecsRow">
                                        <!-- Inputs injected via JS -->
                                    </div>
                                </div>

                                @push('scripts')
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const categorySelect = document.getElementById('category_id');
                                        const container = document.getElementById('dynamicSpecsContainer');
                                        const row = document.getElementById('dynamicSpecsRow');
                                        
                                        // Categories with their specs passed from controller
                                        const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'specs' => $c->required_specs]));
                                        
                                        // Existing Specs or Old Input
                                        const existingSpecs = @json(old('specs', $product->specs ?? []));

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
                                                    const value = existingSpecs[spec.name] || '';

                                                    let inputHtml = '';
                                                    if (spec.type === 'select') {
                                                        let options = spec.options.map(opt => 
                                                            `<option value="${opt}" ${value == opt ? 'selected' : ''}>${opt}</option>`
                                                        ).join('');
                                                        inputHtml = `<select name="${fieldName}" class="form-select form-select-sm" ${isRequired}><option value="">Seleccionar...</option>${options}</select>`;
                                                    } else if (spec.type === 'number') {
                                                        inputHtml = `<input type="number" name="${fieldName}" class="form-control form-control-sm" value="${value}" ${isRequired}>`;
                                                    } else {
                                                        inputHtml = `<input type="text" name="${fieldName}" class="form-control form-control-sm" value="${value}" ${isRequired}>`;
                                                    }
                                                    
                                                    col.innerHTML = `${label}${inputHtml}`;
                                                    row.appendChild(col);
                                                });
                                            } else {
                                                container.classList.add('d-none');
                                            }
                                        }

                                        categorySelect.addEventListener('change', function() {
                                            // Note: If category changes, existing specs might not match. 
                                            // Ideally we clear them or try to match by name. 
                                            // For simplicity, we just re-render. If field names match, nice.
                                            renderSpecs(this.value);
                                        });

                                        // Initial Render
                                        if(categorySelect.value) {
                                            renderSpecs(categorySelect.value);
                                        }
                                    });
                                </script>
                                @endpush

                                <div class="mb-3">
                                    <label for="price" class="form-label fw-bold">Precio (USD)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" id="price" class="form-control"
                                            step="0.01" min="0.01" placeholder="25.99"
                                            value="{{ old('price', $product->price) }}" required>
                                    </div>
                                    @error('price')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 card">
                                    <div class="form-check form-switch card-body d-flex justify-content-center gap-3">
                                        <input class="form-check-input" type="checkbox" id="is_featured_switch"
                                            name="is_featured" value="1"
                                            {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_featured_switch">
                                            Producto Destacado en la Página Principal
                                        </label>
                                    </div>
                                    @error('is_featured')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Columna Derecha: Inventario, Medios y Estado -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label fw-bold">Stock Disponible
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="stock" id="stock" class="form-control"
                                        min="0" placeholder="10"
                                        value="{{ old('stock', $product->stock) }}" required>
                                    @error('stock')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Estado del Producto</label>
                                    <div class="row g-3">
                                        @foreach ([
        'draft' => ['Borrador', 'No visible en tienda', 'warning', 'ti-pencil'],
        'active' => ['Activo', 'Visible en tienda', 'success', 'ti-eye'],
        'discontinued' => ['Descontinuado', 'No se fabrica más', 'secondary', 'ti-na'],
        'archived' => ['Archivado', 'No disponible', 'dark', 'ti-archive'],
    ] as $value => [$label, $description, $color, $icon])
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio"
                                                        name="status" id="status_{{ $value }}"
                                                        value="{{ $value }}"
                                                        {{ old('status', $product->status) == $value ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="status_{{ $value }}">
                                                        <span class="mb-1 d-block fw-bold">
                                                            <i
                                                                class="{{ $icon }} text-{{ $color }} me-2"></i>
                                                            {{ $label }}
                                                        </span>
                                                        <span
                                                            class="text-muted small d-block">{{ $description }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('status')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Imágenes existentes -->
                                @if ($product->images && $product->images->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Imágenes Actuales (Eliminar
                                            permanentemente)</label>
                                        <div class="row g-2">
                                            @foreach ($product->images as $image)
                                                {{-- CRÍTICO: Este div contiene toda la miniatura y su botón. --}}
                                                <div class="col-6 col-md-4 col-lg-3 image-thumbnail-container">
                                                    <div
                                                        class="border rounded p-2 text-center position-relative">

                                                        {{-- Miniatura de la imagen --}}
                                                        <img src="{{ asset('storage/' . $image->image_path) }}"
                                                            class="img-fluid rounded mb-2"
                                                            style="height: 100px; width: 100%; object-fit: cover;">

                                                        {{-- NUEVO: Botón de eliminación basado en JavaScript/AJAX --}}
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger w-100 remove-image-btn"
                                                            data-image-id="{{ $image->id }}">
                                                            <i class="ti-trash"></i> Eliminar
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="images" class="form-label fw-bold">Nuevas Imágenes
                                        (Añadir)</label>
                                    <input type="file" name="images[]" id="images"
                                        class="form-control" multiple accept="image/*">
                                    <small class="text-muted">
                                        Máximo 5 imágenes en total (existentes + nuevas). Máx. 5MB por imagen.
                                    </small>
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
                            <label for="description" class="form-label fw-bold">Descripción
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="description" id="description" rows="5" required
                                placeholder="Describe tu producto de crochet (materiales, medidas, etc.)">{{ old('description', $product->description) }}</textarea>
                            <small class="text-muted">Describe tu producto de crochet (materiales, medidas,
                                etc.)</small>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                <i class="ti-x me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-device-floppy me-2"></i>Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            // --- 2. Validación de IMÁGENES (Lado del Cliente) ---
            // Usamos el conteo de imágenes visibles para limitar el total, sin campos ocultos.
            const imageInput = document.getElementById('images');
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const files = this.files;
                    const maxFiles = 5;
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    let totalSize = 0;

                    // Contar imágenes existentes visibles en el DOM
                    // Usamos la clase 'image-thumbnail-container' que añadimos arriba.
                    const remainingImages = document.querySelectorAll('.image-thumbnail-container').length;

                    // 2a. Validar Límite de Archivos
                    if (files.length + remainingImages > maxFiles) {
                        alert(
                            `Máximo ${maxFiles} imágenes permitidas en total. Tienes ${remainingImages} imágenes existentes.`
                        );
                        this.value = '';
                        return;
                    }

                    // 2b. Validar Tamaño Individual
                    for (let i = 0; i < files.length; i++) {
                        if (files[i].size > maxSize) {
                            alert(
                                `La imagen "${files[i].name}" excede el tamaño máximo individual de 5MB.`
                            );
                            this.value = '';
                            return;
                        }
                        totalSize += files[i].size;
                    }
                });
            }

            // --- GESTIÓN DE ELIMINACIÓN AJAX ---
            document.querySelectorAll('.remove-image-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const imageId = this.getAttribute('data-image-id');
                    const container = this.closest('.image-thumbnail-container');

                    // 1. Mostrar la alerta de confirmación
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¡Esta acción eliminará la imagen de forma permanente y es irreversible!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545', // Color rojo (danger)
                        cancelButtonColor: '#6c757d', // Color gris (secondary)
                        confirmButtonText: 'Sí, ¡Eliminar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        // 2. Si el usuario confirma (result.isConfirmed es true)
                        if (result.isConfirmed) {

                            // 3. Ejecutar la solicitud DELETE (AJAX)
                            fetch(`{{ url('products/images') }}/${imageId}`, {
                                    method: 'POST', // Usamos POST para enviar _method: 'DELETE'
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        _method: 'DELETE' // Sobrescribimos a DELETE
                                    })
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(
                                            'Error en la respuesta del servidor');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    // 4. Si la eliminación es exitosa, ocultar la miniatura
                                    container.remove();

                                    // Mostrar alerta de éxito (Toast)
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Eliminada!',
                                        text: 'La imagen ha sido eliminada permanentemente.',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                })
                                .catch(error => {
                                    // 5. Mostrar alerta de error
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'No se pudo eliminar la imagen. Inténtalo de nuevo.'
                                    });
                                    console.error('Error de eliminación:', error);
                                });
                        }
                    });
                });
            });
        });
    </script>
@endpush
