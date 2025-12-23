@extends('layouts.front-layout')

@section('title', $product->name . ' | EliCrochet')

@section('content')
<div class="bg-light py-4 mb-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop') }}" class="text-decoration-none text-muted">Tienda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.show', $product->category->slug) }}" class="text-decoration-none text-muted">{{ $product->category->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="container mb-5">
    <div class="row g-5">
        <!-- Columna Imagen -->
        <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.2s">
            <div class="card border-0 shadow-lg overflow-hidden mb-3">
                <div class="position-relative">
                    @if($product->created_at->diffInDays(now()) < 30)
                        <span class="badge bg-primary position-absolute top-0 start-0 m-3 fs-6 shadow-sm">Nuevo</span>
                    @endif
                    @php
                        $mainImage = $product->images->first();
                        $imagePath = $mainImage ? asset('storage/' . $mainImage->image_path) : 'https://placehold.co/600x600?text=No+Image';
                    @endphp
                    <img id="mainProductImage" src="{{ $imagePath }}" 
                         alt="{{ $product->name }}" class="img-fluid w-100 object-fit-cover" style="min-height: 500px; max-height: 600px;">
                </div>
            </div>
            <!-- Thumbnails -->
            <div class="row g-2">
                @foreach($product->images as $image)
                <div class="col-3">
                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                         class="img-fluid rounded cursor-pointer border border-2 product-thumbnail {{ $loop->first ? 'border-primary' : 'border-transparent' }}" 
                         style="aspect-ratio: 1/1; object-fit: cover;"
                         onclick="changeImage(this, '{{ asset('storage/' . $image->image_path) }}')"
                         alt="Thumb">
                </div>
                @endforeach
            </div>
        </div>

        <!-- Columna Detalles -->
        <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.4s">
            <h6 class="text-primary fw-bold text-uppercase mb-2 letter-spacing-1">{{ $product->category->name }}</h6>
            <h1 class="display-5 fw-bold mb-3 font-heading">{{ $product->name }}</h1>
            
            <div class="d-flex align-items-center mb-4">
                <div class="text-warning me-2 fs-5">
                    <i class="ti ti-star-filled"></i>
                    <i class="ti ti-star-filled"></i>
                    <i class="ti ti-star-filled"></i>
                    <i class="ti ti-star-filled"></i>
                    <i class="ti ti-star-half-filled"></i>
                </div>
                <span class="text-muted small border-start ps-3 ms-2">
                    <span class="fw-bold text-dark">4.8</span> (24 Opiniones)
                </span>
            </div>

            <h2 class="display-6 fw-bold text-dark mb-4">${{ number_format($product->price, 0, ',', '.') }}</h2>

            <p class="text-muted lead mb-4">{{ $product->short_description ?? Str::limit($product->description, 150) }}</p>

            <div class="mb-4 bg-light p-3 rounded">
                <div class="d-flex align-items-center mb-2">
                    <i class="ti ti-check text-success me-2 fs-5"></i> 
                    <span class="text-dark">Hecho 100% a mano con hilo de algodón premium.</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="ti ti-truck-delivery text-primary me-2 fs-5"></i> 
                    <span class="text-dark">Envío gratis en compras superiores a $50.000.</span>
                </div>
            </div>

            <hr class="my-4 opacity-10">

            @if($product->isAvailable())
                @auth
                <form action="{{ route('cart.add', $product->slug) }}" method="POST">
                    @csrf
                    <div class="row align-items-end g-3">
                        <div class="col-auto">
                            <label class="form-label fw-bold mb-1 small text-uppercase text-muted">Cantidad</label> 
                            <div class="input-group border rounded-3 overflow-hidden">
                                <button type="button" class="btn btn-light px-3" onclick="updateQuantity(-1)">
                                    <i class="ti ti-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantityInput" class="form-control text-center border-0 bg-white" value="1" min="1" max="{{ $product->stock }}" style="width: 60px;" readonly>
                                <button type="button" class="btn btn-light px-3" onclick="updateQuantity(1)">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col">
                            <button class="btn btn-primary w-100 py-2 shadow-sm pulse-button d-flex align-items-center justify-content-center" type="submit">
                                <i class="ti ti-shopping-cart-plus me-2 fs-4"></i> 
                                <span class="fw-bold">Añadir al Carrito</span>
                            </button>
                        </div>
                    </div>
                </form>
                </form>
                @if($product->stock <= 0)
                   <script>
                       document.addEventListener('DOMContentLoaded', function() {
                           const btn = document.querySelector('button[type="submit"]');
                           if(btn) {
                               btn.disabled = true;
                               btn.innerHTML = '<span class="fw-bold">Agotado</span>';
                               btn.classList.remove('btn-primary');
                               btn.classList.add('btn-secondary');
                           }
                           const input = document.getElementById('quantityInput');
                           if(input) input.disabled = true;
                       });
                   </script>
                @endif
                @else
                <div class="alert alert-info border-0 shadow-sm">
                    <div class="d-flex">
                        <i class="ti ti-info-circle me-3 fs-3 text-info"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Inicia sesión para comprar</h6>
                            <p class="mb-2 small text-muted">Necesitas una cuenta para agregar productos y completar tu orden.</p>
                            <div>
                                <a href="{{ route('login') }}" class="btn btn-sm btn-info text-white px-3 fw-bold">Entrar</a>
                                <a href="{{ route('register') }}" class="btn btn-sm btn-outline-info px-3 fw-bold ms-1">Registrarse</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth
                
                <div class="mt-3 text-success small d-flex align-items-center">
                    <div class="spinner-grow text-success spinner-grow-sm me-2" role="status" style="width: 0.5rem; height: 0.5rem;"></div>
                    <span>Stock Disponible: <strong>{{ $product->stock }} unidades</strong></span>
                </div>
            @else
                <div class="alert alert-danger d-inline-flex align-items-center shadow-sm">
                    <i class="ti ti-alert-circle me-2 fs-4"></i> 
                    <div>
                        <strong>Producto Agotado</strong>
                        <div class="small">Lo sentimos, este producto no está disponible por el momento.</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Tabs Info -->
<section class="py-5 bg-light position-relative overflow-hidden">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom pt-4 px-4 rounded-top-3">
                        <ul class="nav nav-pills card-header-tabs gap-3" id="productTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-bold px-4" id="desc-tab" data-bs-toggle="tab" href="#desc" role="tab" aria-selected="true">Descripción</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold px-4" id="specs-tab" data-bs-toggle="tab" href="#specs" role="tab" aria-selected="false">Especificaciones</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold px-4" id="reviews-tab" data-bs-toggle="tab" href="#reviews" role="tab" aria-selected="false">Opiniones (Mockup)</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="tab-content" id="productTabContent">
                            <div class="tab-pane fade show active" id="desc" role="tabpanel">
                                <div class="prose text-muted">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="specs" role="tabpanel">
                                @if($product->specs && count($product->specs) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" style="width: 30%;">Característica</th>
                                                    <th scope="col">Detalle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($product->specs as $key => $value)
                                                <tr>
                                                    <th scope="row" class="text-muted fw-normal text-capitalize">{{ str_replace('_', ' ', $key) }}</th>
                                                    <td class="fw-bold text-dark">{{ $value }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="ti ti-list-details fs-1 mb-3 opacity-50 d-block"></i>
                                        <p class="mb-0 fs-5">No hay especificaciones adicionales para mostrar.</p>
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <!-- Mockup Reviews -->
                                <div class="alert alert-light border mb-4">
                                    <i class="ti ti-info-circle me-2"></i> Las opiniones mostradas son un ejemplo visual. Próximamente integración real.
                                </div>
                                
                                <div class="vstack gap-4">
                                    <div class="d-flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <span class="fw-bold">AM</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center mb-1">
                                                <h6 class="fw-bold mb-0 me-2">Ana María</h6>
                                                <div class="text-warning small">
                                                    <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i>
                                                </div>
                                            </div>
                                            <p class="text-muted mb-0">¡Es precioso! Mucho mejor que en las fotos. Los detalles son increíbles y la calidad del hilo se nota a simple vista.</p>
                                        </div>
                                    </div>
                                    <hr class="text-muted opacity-10 my-0">
                                    <div class="d-flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar bg-light-info text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <span class="fw-bold">CR</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center mb-1">
                                                <h6 class="fw-bold mb-0 me-2">Carlos Ruiz</h6>
                                                <div class="text-warning small">
                                                    <i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star-filled"></i><i class="ti ti-star"></i>
                                                </div>
                                            </div>
                                            <p class="text-muted mb-0">Llegó muy rápido y el envoltorio era hermoso. Ideal para regalo, mi esposa quedó encantada.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
@if(isset($relatedProducts) && count($relatedProducts) > 0)
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0 position-relative d-inline-block">
                También te podría gustar
                <span class="position-absolute bottom-0 start-0 w-50 border-bottom border-3 border-primary rounded"></span>
            </h3>
        </div>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
                <div class="col-md-3">
                     <div class="card product-card border-0 shadow-sm h-100 transition-hover">
                        @php
                            $relatedImage = $related->images->first();
                            $relatedImagePath = $relatedImage ? asset('storage/' . $relatedImage->image_path) : 'https://placehold.co/600x600?text=No+Image';
                        @endphp
                        <div class="position-relative overflow-hidden">
                             <a href="{{ route('product.show', $related->slug) }}">
                                <img src="{{ $relatedImagePath }}" class="card-img-top object-fit-cover" alt="{{ $related->name }}" style="height: 250px;">
                             </a>
                             <div class="action-buttons position-absolute bottom-0 start-50 translate-middle-x mb-3 opacity-0 transition-opacity">
                                <a href="{{ route('product.show', $related->slug) }}" class="btn btn-light rounded-circle shadow-sm p-2 mx-1" title="Ver Producto"><i class="ti ti-eye"></i></a>
                             </div>
                        </div>
                        <div class="card-body">
                            <div class="small text-muted mb-1">{{ $related->category->name }}</div>
                            <h6 class="card-title fw-bold text-truncate"><a href="{{ route('product.show', $related->slug) }}" class="text-decoration-none text-dark stretched-link">{{ $related->name }}</a></h6>
                            <p class="text-primary fw-bold mb-0 lead">${{ number_format($related->price, 0, ',', '.') }}</p>
                        </div>
                     </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
    .pulse-button:hover {
        animation: pulse-soft 2s infinite;
    }
    .letter-spacing-1 { letter-spacing: 1px; }
    .transition-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .transition-hover:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    .transition-hover:hover .action-buttons { opacity: 1; }
    
    @keyframes pulse-soft {
        0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
        100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }
    
    /* Custom Scrollbar for specs if needed */
    .prose { line-height: 1.8; }
</style>

<script>
    function changeImage(thumbnail, src) {
        document.getElementById('mainProductImage').src = src;
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.product-thumbnail').forEach(el => {
            el.classList.remove('border-primary');
            el.classList.add('border-transparent');
        });
        
        // Add active class to clicked
        thumbnail.classList.remove('border-transparent');
        thumbnail.classList.add('border-primary');
    }

    function updateQuantity(change) {
        const input = document.getElementById('quantityInput');
        let val = parseInt(input.value) || 0;
        let min = parseInt(input.min) || 1;
        let max = parseInt(input.max) || 100;
        
        let newValue = val + change;
        
        if (newValue >= min && newValue <= max) {
            input.value = newValue;
        }
    }
</script>
@endsection