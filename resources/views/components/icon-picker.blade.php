@props(['name' => 'icon', 'value' => '', 'label' => 'Icono de la Categoría'])

@php
    $icons = [
        // Animales
        'ti-heart',          // Para "Animales Adorables" o "Favoritos"
        'ti-face-smile',     // Para "Animales Divertidos" o "Personajes"
        'ti-shine',          // Para "Animales Mágicos" o "Especiales"
        
        // Personajes/Creaturas
        'ti-crown',          // Para "Princesas", "Reyes" o "Personajes Reales"
        'ti-music',          // Para "Músicos" o "Personajes Musicales"
        
        // Accesorios/Decoración
        'ti-gift',           // Para "Regalos" o "Accesorios Especiales"
        'ti-tag',            // Para "Ofertas" o "Accesorios con Descuento"
        'ti-star',           // Para "Destacados" o "Accesorios Premium"
        'ti-bell',           // Para "Navidad" o "Accesorios Festivos"
        
        // Herramientas/Materiales
        'ti-palette',        // Para "Colores" o "Mezclas de Lana"
        'ti-cut',            // Para "Herramientas" o "Corte"
        'ti-ruler-pencil',   // Para "Patrones" o "Diseños"
        'ti-paint-bucket',   // Para "Tintes" o "Coloración"
        
        // Kits/Paquetes
        'ti-home',           // Para "Kits Hogar" o "Kits Básicos"
        'ti-cup',            // Para "Kits Premio" o "Kits Especiales"
        
        // Estilos/Técnicas
        'ti-camera',         // Para "Fotografía" o "Kits para Fotos"
        'ti-image',          // Para "Diseños" o "Ilustraciones"
        'ti-wand',           // Para "Mágico" o "Técnicas Avanzadas"
        'ti-world',          // Para "Internacional" o "Estilos del Mundo"
        
        // Temporalidad
        'ti-thought',        // Para "Personalizados" o "Por Encargo"
        'ti-pin',            // Para "Colección" o "Edición Limitada"
    ];
@endphp

<div class="mb-3">
    <label class="form-label fw-bold">{{ $label }}</label>
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
</div>
