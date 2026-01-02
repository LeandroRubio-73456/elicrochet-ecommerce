@props(['name' => 'icon', 'value' => '', 'label' => 'Icono de la Categoría'])

@php
    $icons = [
        // Animales
        'ti ti-heart',          // Para "Animales Adorables" o "Favoritos"
        'ti ti-mood-smile',     // Para "Animales Divertidos" (face-smile -> mood-smile in v2)
        'ti ti-sparkles',       // Para "Animales Mágicos" (shine -> sparkles)
        
        // Personajes/Creaturas
        'ti ti-crown',          // Para "Princesas", "Reyes"
        'ti ti-music',          // Para "Músicos"
        
        // Accesorios/Decoración
        'ti ti-gift',           // Para "Regalos"
        'ti ti-tag',            // Para "Ofertas"
        'ti ti-star',           // Para "Destacados"
        'ti ti-bell',           // Para "Navidad"
        
        // Herramientas/Materiales
        'ti ti-palette',        // Para "Colores"
        'ti ti-cut',            // Para "Herramientas"
        'ti ti-ruler-2',        // Para "Patrones" (ruler-pencil -> ruler-2)
        'ti ti-bucket',         // Para "Tintes" (paint-bucket -> bucket)
        
        // Kits/Paquetes
        'ti ti-home',           // Para "Kits Hogar"
        'ti ti-trophy',         // Para "Kits Premio" (cup -> trophy)
        
        // Estilos/Técnicas
        'ti ti-camera',         // Para "Fotografía"
        'ti ti-photo',          // Para "Diseños" (image -> photo)
        'ti ti-wand',           // Para "Mágico"
        'ti ti-world',          // Para "Internacional"
        
        // Temporalidad
        'ti ti-brain',          // Para "Personalizados" (thought -> brain)
        'ti ti-pinned',         // Para "Colección" (pin -> pinned)
    ];
@endphp

<fieldset class="mb-3">
    <legend class="form-label fw-bold p-0 mb-2 border-0" style="font-size: 1rem;">{{ $label }}</legend>
    <div class="d-flex flex-wrap gap-2">
        @foreach($icons as $icon)
            <div class="icon-option">
                <input type="radio"
                       name="{{ $name }}"
                       id="icon_{{ $icon }}"
                       value="{{ $icon }}"
                       class="btn-check"
                       {{ old($name, $value) == $icon ? 'checked' : '' }}>
                <label class="btn btn-outline-primary d-flex align-items-center justify-content-center"
                       for="icon_{{ $icon }}" 
                       style="width: 45px; height: 45px; font-size: 1.25rem;">
                    <i class="{{ $icon }}"></i>
                </label>
            </div>
        @endforeach
    </div>
    <div class="form-text">Selecciona un icono que represente visualmente esta categoría en la tienda.</div>
    @error($name)
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</fieldset>
