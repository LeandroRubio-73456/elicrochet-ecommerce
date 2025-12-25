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
            <aside class="col-lg-3">
                <form action="{{ isset($category) ? route('category.show', $category->slug) : route('shop') }}" method="GET" id="shopFilters">
                    <!-- Maintain sort if set -->
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

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
                                    <span class="category-name">{{ $cat->name }}</span>
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
                        <div class="cta-card-mini mt-2">
                            <div class="cta-icon">
                                <i class="ti-wand fs-4"></i>
                            </div>
                            <h4 class="cta-title">¿Buscas algo único?</h4>
                            <p class="cta-text">Creamos diseños exclusivos a tu medida</p>
                            <a href="{{ route('customer.custom.create') }}" class="btn-cta-mini">
                                Solicitar ahora
                                <i class="ti ti-arrow-right"></i>
                            </a>
                        </div>

                    </div>
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
                        <select class="sort-select" name="sort" onchange="document.getElementById('shopFilters').submit();">
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

@endsection

@push('css')
@endpush