@extends('layouts.front-layout')

@section('title', 'Tienda | EliCrochet')

@section('content')

<!-- Hero Header Minimalista -->
<div class="shop-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="header-content">
                    <h1 class="header-title">
                        @if(isset($category))
                            {{ $category->name }}
                        @else
                            Nuestra Tienda
                        @endif
                    </h1>
                    <p class="header-description">
                        @if(isset($category))
                            {{ $category->description ?? 'Explora nuestros productos en esta categoría.' }}
                        @else
                            Descubre nuestra colección de amigurumis hechos a mano con amor.
                        @endif
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <nav aria-label="breadcrumb" class="breadcrumb-modern">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <i class="ti ti-home"></i>
                                Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Tienda</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="shop-section">
    <div class="container">
        <div class="row g-4">
            
            <!-- Sidebar Filters -->
            <!-- Mobile Filter Button -->
            <div class="col-12 d-lg-none mb-3">
                <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#shopFiltersOffcanvas" aria-controls="shopFiltersOffcanvas">
                    <i class="ti ti-filter"></i> Filtrar Productos
                </button>
            </div>

            <!-- Sidebar Filters (Desktop) -->
            <aside class="col-lg-3 d-none d-lg-block">
                <form action="{{ isset($category) ? route('category.show', $category->slug) : route('shop') }}" method="GET" id="shopFiltersDesktop">
                    <!-- Maintain sort if set -->
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    @include('front.partials.shop-filters')
                </form>
            </aside>

            <!-- Products Grid -->
            <div class="col-lg-9">
                
                <!-- Toolbar -->
                <div class="products-toolbar">
                    <div class="toolbar-info">
                        <span class="results-count">
                            Mostrando <strong>{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</strong> de <strong>{{ $products->total() }}</strong> productos
                        </span>
                    </div>
                    <div class="toolbar-actions">
                        <select class="sort-select" name="sort" onchange="submitShopFilters(this.value)">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Más Recientes</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Más Populares</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <!-- Products Grid -->
                <div class="products-grid">
                    @forelse($products as $product)
                        <x-product-card :product="$product" />
                    @empty
                        <div class="col-span-full text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="ti ti-shopping-bag-off fs-1"></i>
                                </div>
                                <h3 class="empty-title">No se encontraron productos</h3>
                                <p class="empty-text">Intenta seleccionar otra categoría o revisa más tarde.</p>
                                <a href="{{ route('shop') }}" class="btn btn-primary">Ver Todos los Productos</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                <div class="pagination-wrapper">
                    {{ $products->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>
</section>

<!-- Offcanvas Filters (Mobile) - Moved outside container for better z-index handling -->
<div class="offcanvas offcanvas-start border-0" tabindex="-1" id="shopFiltersOffcanvas" aria-labelledby="shopFiltersOffcanvasLabel" style="z-index: 1050;">
    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title fw-bold" id="shopFiltersOffcanvasLabel">
            <i class="ti ti-filter me-2 text-primary"></i>Filtros
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-4">
            <form action="{{ isset($category) ? route('category.show', $category->slug) : route('shop') }}" method="GET" id="shopFiltersMobile">
                <!-- Maintain sort if set -->
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                @include('front.partials.shop-filters')
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function submitShopFilters(sortValue) {
        // Determine which form is currently applicable (desktop or mobile)
        const isMobile = window.innerWidth < 992; 
        const formId = isMobile ? 'shopFiltersMobile' : 'shopFiltersDesktop';
        const form = document.getElementById(formId);
        
        if (form) {
            // Ensure the sort input exists in the form
            let sortInput = form.querySelector('input[name="sort"]');
            if (!sortInput) {
                sortInput = document.createElement('input');
                sortInput.type = 'hidden';
                sortInput.name = 'sort';
                form.appendChild(sortInput);
            }
            sortInput.value = sortValue;
            
            form.submit();
        }
    }
</script>
@endpush
