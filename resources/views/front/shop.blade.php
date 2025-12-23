@extends('layouts.front-layout')

@section('title', 'Tienda | EliCrochet')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-5 fw-bold mb-0">
                    @if(isset($category))
                        {{ $category->name }}
                    @else
                        Nuestra Tienda
                    @endif
                </h1>
                <p class="text-muted mt-2 mb-0">
                    @if(isset($category))
                        {{ $category->description ?? 'Explora nuestros productos en esta categoría.' }}
                    @else
                        Explore nuestra colección de amigurumis hechos a mano.
                    @endif
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tienda</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Filtros (Sidebar) -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold py-3">Categorías</div>
                    <ul class="list-group list-group-flush">
                        {{-- Enlace a "Todas" --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center action-hover">
                            <a href="{{ route('shop') }}" class="text-decoration-none {{ !isset($category) ? 'fw-bold text-gradient-purple' : 'text-dark' }} stretched-link">
                                Ver Todo
                            </a>
                        </li>
                        @forelse($categories as $cat)
                        <li class="list-group-item d-flex justify-content-between align-items-center action-hover">
                            <a href="{{ route('category.show', $cat->slug) }}" 
                               class="text-decoration-none {{ (isset($category) && $category->id == $cat->id) ? 'fw-bold text-gradient-purple' : 'text-dark' }} stretched-link">
                                {{ $cat->name }}
                            </a>
                            <span class="badge bg-light text-dark rounded-pill">{{ $cat->products_count }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-muted small">No hay categorías</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Range filter UI (Visual only for now) -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3">Rango de Precio</div>
                    <div class="card-body">
                        <input type="range" class="form-range" id="priceRange" disabled title="Próximamente">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>$0</span>
                            <span>$max</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Listado de Productos -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-muted">
                        Mostrando {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} de {{ $products->total() }} productos
                    </span>
                    <!-- Sorting Mockup -->
                    <select class="form-select w-auto" disabled title="Próximamente">
                        <option selected>Más Recientes</option>
                        <option value="1">Precio: Menor a Mayor</option>
                        <option value="2">Precio: Mayor a Menor</option>
                    </select>
                </div>

                <div class="row g-4">
                    @forelse($products as $product)
                        <div class="col-md-4 col-sm-6">
                            <x-product-card :product="$product" />
                        </div>
                    @empty
                        <div class="col-12 py-5 text-center">
                            <i class="bi bi-emoji-frown display-4 text-muted mb-3"></i>
                            <h4>No se encontraron productos</h4>
                            <p class="text-muted">Intenta seleccionar otra categoría o revisa luego.</p>
                            <a href="{{ route('shop') }}" class="btn btn-outline-primary">Ver Todos los Productos</a>
                        </div>
                    @endforelse
                </div>

                <!-- Paginación -->
                <div class="mt-5 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
