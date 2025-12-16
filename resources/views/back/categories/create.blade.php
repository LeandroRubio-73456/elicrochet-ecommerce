<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.head-page-meta', ['title' => 'Añadir Categoría'])
    @include('layouts.head-css')
</head>
<body @bodySetup>
    @include('layouts.layout-vertical')

    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', [
                'breadcrumb-item' => 'Categorías',
                'breadcrumb-item-active' => 'Crear Nueva',
            ])
            <h5 class="">Añadir una nueva Categoría</h5>
            
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            
                            <form method="POST" action="{{ route('back.categories.store') }}">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label fw-bold">Nombre de la Categoría
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                placeholder="Ej. Amigurumis" 
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="slug" class="form-label fw-bold">URL amigable (slug)</label>
                                            <input type="text" name="slug" id="slug" class="form-control"
                                                placeholder="Ej. amigurumis" 
                                                value="{{ old('slug') }}">
                                            <small class="text-muted">Si lo dejas vacío, se generará automáticamente</small>
                                            @error('slug')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Estado de la Categoría</label>
                                            <div class="row g-3">
                                                @foreach ([
                                                    'active' => ['Activa', 'Visible en la tienda', 'success', 'ti-eye'],
                                                    'inactive' => ['Inactiva', 'No visible en la tienda', 'warning', 'ti-eye-off'],
                                                    'archived' => ['Archivada', 'Oculta del sistema', 'secondary', 'ti-archive']
                                                ] as $value => [$label, $description, $color, $icon])
                                                    <div class="col-md-6">
                                                        <div class="form-check card-radio">
                                                            <input class="form-check-input" type="radio" 
                                                                    name="status" id="status_{{ $value }}" 
                                                                    value="{{ $value }}"
                                                                    {{ old('status', 'active') == $value ? 'checked' : '' }}
                                                                    required>
                                                            <label class="form-check-label" for="status_{{ $value }}">
                                                                <span class="mb-1 d-block fw-bold">
                                                                    <i class="ti {{ $icon }} text-{{ $color }} me-2"></i>
                                                                    {{ $label }}
                                                                </span>
                                                                <span class="text-muted small d-block">{{ $description }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('status')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">Descripción
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" name="description" id="description" rows="4" 
                                                required placeholder="Describe esta categoría...">{{ old('description') }}</textarea>
                                    <small class="text-muted">Describe los productos que pertenecen a esta categoría.</small>
                                    @error('description')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('back.categories.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Categoría
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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