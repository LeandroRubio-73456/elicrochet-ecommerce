@props(['name' => 'icon', 'value' => '', 'label' => 'Icono de la Categoría'])

@php
    $icons = [
        // Muñecos / Infantil
        'ti ti-horse-toy' => 'Juguete / Caballito',
        'ti ti-robot' => 'Robot / Muñeco',
        'ti ti-alien' => 'Alien / Fantasía',
        'ti ti-ghost' => 'Fantasma / Halloween',
        'ti ti-cat' => 'Gato / Animales',
        'ti ti-mood-smile' => 'Carita Feliz',
        'ti ti-baby-carriage' => 'Bebés',
        
        // Tejido / Costura
        'ti ti-brand-yarn' => 'Ovillo / Lana',
        'ti ti-needle-thread' => 'Aguja e Hilo',
        'ti ti-needle' => 'Aguja de Tejer',
        'ti ti-cut' => 'Tijeras',
        'ti ti-ruler-2' => 'Medidas / Regla',
        'ti ti-palette' => 'Colores',
        'ti ti-hanger' => 'Ropa / Vestuario',
        'ti ti-shirt' => 'Prenda Tejida',
        
        // Destacados / Otros
        'ti ti-star' => 'Estrella / Destacado',
        'ti ti-heart' => 'Corazón / Amor',
        'ti ti-gift' => 'Regalo',
        'ti ti-shopping-cart' => 'Venta / Pedido',
        'ti ti-truck-delivery' => 'Envío',
        'ti ti-package' => 'Paquete'
    ];
@endphp

<fieldset class="mb-4">
    <legend class="form-label fw-bold p-0 mb-3 border-0">{{ $label }}</legend>
    
    <div class="d-flex flex-wrap gap-3">
        @foreach($icons as $icon => $title)
            <div class="icon-option position-relative" title="{{ $title }}">
                <input type="radio"
                       name="{{ $name }}"
                       id="icon_{{ str_replace(' ', '_', $icon) }}"
                       value="{{ $icon }}"
                       class="btn-check"
                       {{ old($name, $value) == $icon ? 'checked' : '' }}>
                <label class="btn btn-outline-primary d-flex align-items-center justify-content-center"
                       for="icon_{{ str_replace(' ', '_', $icon) }}" 
                       style="width: 50px; height: 50px; font-size: 1.4rem; cursor: pointer;"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="{{ $title }}">
                    <i class="{{ $icon }}"></i>
                </label>
            </div>
        @endforeach
    </div>
    
    <div class="form-text mt-2">
        <i class="ti ti-{{ $value ?: 'help-circle' }} me-1"></i>
        {{ $value ? "Seleccionado: " . ($icons[$value] ?? $value) : "Selecciona un icono para esta categoría" }}
    </div>
    
    @error($name)
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</fieldset>

@push('scripts')
<script>
    // Inicializar tooltips de Bootstrap si los usas
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
</script>
@endpush
