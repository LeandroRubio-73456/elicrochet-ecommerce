@extends('layouts.front-layout')

@section('title', $product->name . ' | EliCrochet')

@section('content')

<!-- Breadcrumb Moderno -->
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb-modern">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">
                        <i class="ti ti-home"></i>
                        Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('shop') }}">Tienda</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
                </li>
                <li class="breadcrumb-item active">{{ Str::limit($product->name, 30) }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Details -->
<section class="product-section">
    <div class="container">
        <div class="row g-5">
            
            <!-- Gallery Column -->
            <div class="col-lg-6">
                <div class="product-gallery">
                    <!-- Main Image -->
                    <div class="main-image-wrapper">
                        @if($product->created_at->diffInDays(now()) < 30)
                            <span class="product-badge badge-new">Nuevo</span>
                        @endif
                        @php
                            $mainImage = $product->images->first();
                            $imagePath = $mainImage ? asset('storage/' . $mainImage->image_path) : 'https://placehold.co/600x600?text=No+Image';
                        @endphp
                        <img id="mainProductImage" 
                             src="{{ $imagePath }}"
                             alt="{{ $product->name }}"
                             class="main-image">
                    </div>

                    <!-- Thumbnails -->
                    @if($product->images->count() > 1)
                    <div class="thumbnails-grid">
                        @foreach($product->images as $image)
                        <button type="button" class="thumbnail-item {{ $loop->first ? 'active' : '' }} border-0 bg-transparent p-0"
                             onclick="changeImage(this, '{{ asset('storage/' . $image->image_path) }}')"
                             aria-label="Ver imagen {{ $loop->iteration }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Thumbnail {{ $loop->iteration }}">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Info Column -->
            <div class="col-lg-6">
                <div class="product-info">
                    
                    <!-- Category Badge -->
                    <div class="category-badge">{{ $product->category->name }}</div>
                    
                    <!-- Product Title -->
                    <h1 class="product-title">{{ $product->name }}</h1>
                    
                    <!-- Rating -->
                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($product->average_rating))
                                    <i class="ti ti-star-filled text-warning"></i>
                                @elseif($i - 0.5 == round($product->average_rating))
                                    <i class="ti ti-star-half-filled text-warning"></i>
                                @else
                                    <i class="ti ti-star text-muted"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-text"><strong>{{ number_format($product->average_rating, 1) }}</strong> ({{ $product->total_reviews }} opiniones)</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="product-price">${{ number_format($product->price, 0, ',', '.') }}</div>
                    
                    <!-- Short Description -->
                    <p class="product-description">
                        {{ $product->short_description ?? Str::limit($product->description, 150) }}
                    </p>
                    
                    <!-- Features -->
                    <div class="product-features">
                        <div class="feature-item">
                            <i class="ti ti-needle-thread fs-4 text-primary"></i>
                            <span>Hecho 100% a mano con hilo de algodón premium</span>
                        </div>
                        <div class="feature-item">
                            <i class="ti ti-truck-delivery fs-4 text-primary"></i>
                            <span>Envío gratis en compras superiores a $50.000</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    @if($product->isAvailable())
                        @auth
                        <!-- Add to Cart Form -->
                        <form action="{{ route('cart.add', $product->slug) }}" method="POST" class="add-to-cart-form">
                            @csrf
                            <div class="quantity-cart-wrapper">
                                <!-- Quantity Selector -->
                                <div class="quantity-selector">
                                    <label for="quantityInput" class="quantity-label">Cantidad</label>
                                    <div class="quantity-controls">
                                        <button type="button" class="qty-btn" onclick="updateQuantity(-1)">
                                            <i class="ti ti-minus"></i>
                                        </button>
                                        <input type="number" 
                                               name="quantity" 
                                               id="quantityInput" 
                                               class="qty-input" 
                                               value="1" 
                                               min="1" 
                                               max="{{ $product->stock }}" 
                                               readonly>
                                        <button type="button" class="qty-btn" onclick="updateQuantity(1)">
                                            <i class="ti ti-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Add to Cart Button -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">
                                    <i class="ti ti-shopping-cart-plus me-2"></i>
                                    Añadir al carrito
                                </button>
                            </div>
                        </form>

                        @if($product->stock <= 0)
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const btn = document.querySelector('.btn-add-to-cart');
                                if(btn) {
                                    btn.disabled = true;
                                    btn.innerHTML = 'Agotado';
                                    btn.classList.add('disabled');
                                }
                            });
                        </script>
                        @endif

                        <!-- Stock Info -->
                        <div class="stock-info">
                            <div class="stock-indicator"></div>
                            <span>Stock disponible: <strong>{{ $product->stock }} unidades</strong></span>
                        </div>

                        @else
                        <!-- Login Required -->
                        <div class="login-required">
                            <div class="login-icon">
                                <i class="ti ti-lock"></i>
                            </div>
                            <div class="login-content">
                                <h6>Inicia sesión para comprar</h6>
                                <p>Necesitas una cuenta para agregar productos y completar tu orden.</p>
                                <div class="login-actions">
                                    <a href="{{ route('login') }}" class="btn-login">Entrar</a>
                                    <a href="{{ route('register') }}" class="btn-register">Registrarse</a>
                                </div>
                            </div>
                        </div>
                        @endauth
                    @else
                        <!-- Out of Stock -->
                        <div class="out-of-stock">
                            <i class="ti ti-alert-circle fs-1 text-muted mb-3"></i>
                            <div>
                                <strong>Producto Agotado</strong>
                                <p>Lo sentimos, este producto no está disponible por el momento.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Tabs -->
<section class="product-tabs-section">
    <div class="container">
        <div class="tabs-wrapper">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs tabs-nav" id="productTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button">
                        Descripción
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button">
                        Especificaciones
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                        Opiniones
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content tabs-content" id="productTabContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="desc">
                    <div class="description-content">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specs">
                    @if($product->specs && count($product->specs) > 0)
                    <div class="specs-table">
                        @foreach($product->specs as $key => $value)
                        <div class="spec-row">
                            <div class="spec-label">{{ str_replace('_', ' ', ucfirst($key)) }}</div>
                            <div class="spec-value">{{ $value }}</div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="ti ti-clipboard-list fs-1 text-muted mb-3"></i>
                        <p>No hay especificaciones adicionales para mostrar</p>
                    </div>
                    @endif
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews">
                    <!-- Write Review Section -->
                    <div class="write-review-section mb-5">
                        @auth
                            @if(auth()->user()->hasReviewed($product))
                                <div class="alert alert-success d-flex align-items-center mb-4">
                                    <i class="ti ti-circle-check fs-3 me-3"></i>
                                    <div>
                                        <strong>¡Gracias por tu opinión!</strong>
                                        <p class="mb-0">Ya has compartido una reseña para este producto.</p>
                                    </div>
                                </div>
                            @elseif(auth()->user()->hasPurchased($product))
                                <div class="card border-0 shadow-sm mb-4" id="reviews-form">
                                    <div class="card-body p-4">
                                        <h5 class="card-title mb-4">Escribir una reseña</h5>
                                        <form action="{{ route('reviews.store', $product->slug) }}" method="POST">
                                            @csrf
                                            <!-- Rating -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Calificación</label>
                                                <div class="rating-input">
                                                    <div class="stars-input d-flex flex-row-reverse justify-content-end">
                                                        <input type="radio" id="star5" name="rating" value="5" class="d-none peer" required />
                                                        <label for="star5" class="ti ti-star fs-3 text-muted cursor-pointer hover-warning peer-active-warning"></label>
                                                        
                                                        <input type="radio" id="star4" name="rating" value="4" class="d-none peer" />
                                                        <label for="star4" class="ti ti-star fs-3 text-muted cursor-pointer hover-warning peer-active-warning"></label>
                                                        
                                                        <input type="radio" id="star3" name="rating" value="3" class="d-none peer" />
                                                        <label for="star3" class="ti ti-star fs-3 text-muted cursor-pointer hover-warning peer-active-warning"></label>
                                                        
                                                        <input type="radio" id="star2" name="rating" value="2" class="d-none peer" />
                                                        <label for="star2" class="ti ti-star fs-3 text-muted cursor-pointer hover-warning peer-active-warning"></label>
                                                        
                                                        <input type="radio" id="star1" name="rating" value="1" class="d-none peer" />
                                                        <label for="star1" class="ti ti-star fs-3 text-muted cursor-pointer hover-warning peer-active-warning"></label>
                                                    </div>
                                                    <style>
                                                        .stars-input input:checked ~ label,
                                                        .stars-input label:hover,
                                                        .stars-input label:hover ~ label {
                                                            color: #ffc107 !important; /* warning color */
                                                            content: "\eb5e"; /* filled star code if using tabler font directly, or just reliance on color fill if using svg based font usually checks class change. Since we use ti-icons, we rely on color mainly or changing class via JS. Let's start simple with color. */
                                                        }
                                                        /* Simple helper for filled state if just changing color isn't enough */
                                                        .stars-input input:checked ~ label:before {
                                                            content: "\eb5e"; /* ti-star-filled */
                                                        }
                                                        .stars-input label:hover:before,
                                                        .stars-input label:hover ~ label:before {
                                                            content: "\eb5e";
                                                        }
                                                    </style>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="title" class="form-label">Título (Opcional)</label>
                                                <input type="text" class="form-control" id="title" name="title" placeholder="Ej: ¡Excelente calidad!">
                                            </div>

                                            <div class="mb-3">
                                                <label for="comment" class="form-label">Tu opinión</label>
                                                <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Cuéntanos más sobre tu experiencia..."></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Publicar Reseña</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>

                    @if($product->reviews->isEmpty())
                        <div class="empty-state text-center py-5">
                            <i class="ti ti-message-off fs-1 text-muted mb-3"></i>
                            <p class="text-muted">Aún no hay opiniones para este producto. @auth @if(auth()->user()->hasPurchased($product)) ¡Sé el primero en compartir tu experiencia! @endif @endauth</p>
                        </div>
                    @else
                        <div class="reviews-summary mb-4 p-4 bg-light rounded">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center border-end">
                                    <h2 class="display-4 fw-bold mb-0">{{ number_format($product->average_rating, 1) }}</h2>
                                    <div class="stars mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($product->average_rating))
                                                <i class="ti ti-star-filled text-warning"></i>
                                            @else
                                                <i class="ti ti-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="text-muted mb-0">Basado en {{ $product->total_reviews }} reseñas</p>
                                </div>
                                <div class="col-md-8 ps-md-4">
                                    <!-- Aquí podrías agregar barras de progreso por estrellas si lo deseas en el futuro -->
                                    <p class="text-muted mb-0">Opiniones de clientes verificados</p>
                                </div>
                            </div>
                        </div>

                        <div class="reviews-list">
                            @foreach($product->reviews as $review)
                            <div class="review-item border-bottom pb-4 mb-4">
                                <div class="d-flex">
                                    <div class="review-avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight: bold; font-size: 1.2rem;">
                                        {{ strtoupper(substr($review->user->name, 0, 2)) }}
                                    </div>
                                    <div class="review-content flex-grow-1">
                                        <div class="review-header d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $review->user->name }}
                                                    @if($review->is_verified_purchase)
                                                        <span class="badge bg-success-subtle text-success ms-2" style="font-size: 0.75rem;">
                                                            <i class="ti ti-circle-check-filled me-1"></i>Compra Verificada
                                                        </span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">{{ $review->created_at->format('d M, Y') }}</small>
                                            </div>
                                            <div class="review-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="ti ti-star-filled text-warning"></i>
                                                    @else
                                                        <i class="ti ti-star text-muted"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        @if($review->title)
                                            <h6 class="review-title fw-bold mb-2">{{ $review->title }}</h6>
                                        @endif
                                        <p class="mb-0 text-secondary">{{ $review->comment }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
@if(isset($relatedProducts) && count($relatedProducts) > 0)
<section class="related-products-section section-padding bg-soft">
    <div class="container">
        <x-section-header label="Recomendados" title="También te podría gustar" />
        
        <div class="products-grid">
            @foreach($relatedProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@push('styles')
@endpush

<script>
    function changeImage(thumbnail, src) {
        document.getElementById('mainProductImage').src = src;
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.thumbnail-item').forEach(el => {
            el.classList.remove('active');
        });
        
        // Add active class to clicked
        thumbnail.classList.add('active');
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

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                title: '¡Excelente!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Aceptar',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Error',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'Entendido',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        @endif
        
        @if($errors->any())
            Swal.fire({
                title: 'Atención',
                html: '<ul class="text-start mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                icon: 'warning',
                confirmButtonText: 'Revisar',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        @endif
    });
</script>
