<div class="filters-wrapper">
    
    <!-- Categories Filter -->
    <div class="filter-card">
        <h3 class="filter-title">Categorías</h3>
        <div class="filter-content">
            <a href="{{ route('shop') }}"
               class="category-item {{ !isset($category) ? 'active' : '' }}">
                <span class="category-name">Ver Todo</span>
                <i class="ti ti-layers-union"></i>
            </a>
            @forelse($categories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="category-item {{ (isset($category) && $category->id == $cat->id) ? 'active' : '' }}">
                <span class="category-name">{{ $cat->name }}
                    @if($cat->icon)
                        <i class="ti ti-{{ preg_replace('/^(ti-?|ti\s)/', '', $cat->icon) }}"></i>
                    @endif
                </span>
                <span class="category-count">{{ $cat->products_count }}</span>
            </a>
            @empty
            <div class="text-muted small text-center py-3">No hay categorías</div>
            @endforelse
        </div>
    </div>

    <!-- Price Range Filter -->
    <div class="filter-card">
        <h3 class="filter-title">Filtrar por Precio</h3>
        <div class="filter-content p-3">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="form-floating flex-grow-1">
                    <input type="number" class="form-control form-control-sm" id="min_price" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                    <label for="min_price">Mín</label>
                </div>
                <span class="text-muted">-</span>
                <div class="form-floating flex-grow-1">
                    <input type="number" class="form-control form-control-sm" id="max_price" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                    <label for="max_price">Máx</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-sm">Aplicar Filtro</button>
        </div>
    </div>

    <!-- Custom Order CTA -->
    <div class="cta-card-mini mt-2 text-center">
        <img src="{{ asset('assets/images/PedidoPersonalizado.webp') }}"
             alt="Custom"
             width="60"
             height="60"
             class="img-fluid mb-3">
        <h4 class="cta-title">¿Buscas algo único?</h4>
        <p class="cta-text">Creamos diseños exclusivos a tu medida</p>
        <a href="{{ route('customer.custom.create') }}" class="btn-cta-mini">
            Solicitar ahora
            <i class="ti ti-arrow-right"></i>
        </a>
    </div>

</div>
