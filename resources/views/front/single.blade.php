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
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-half-filled text-warning"></i>
                        </div>
                        <span class="rating-text"><strong>4.8</strong> (24 opiniones)</span>
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
                                    <label class="quantity-label">Cantidad</label>
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
                                <button type="submit" class="btn-add-to-cart">
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
                    <div class="reviews-notice">
                        <i class="ti ti-message-circle me-2"></i>
                        <span>Las opiniones mostradas son un ejemplo visual. Próximamente integración real.</span>
                    </div>

                    <div class="reviews-list">
                        <div class="review-item">
                            <div class="review-avatar">AM</div>
                            <div class="review-content">
                                <div class="review-header">
                                    <h6>Ana María</h6>
                                    <div class="review-stars">
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                    </div>
                                </div>
                                <p>¡Es precioso! Mucho mejor que en las fotos. Los detalles son increíbles y la calidad del hilo se nota a simple vista.</p>
                            </div>
                        </div>

                        <div class="review-item">
                            <div class="review-avatar" style="background: #e0f2fe; color: #0284c7;">CR</div>
                            <div class="review-content">
                                <div class="review-header">
                                    <h6>Carlos Ruiz</h6>
                                    <div class="review-stars">
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                    </div>
                                </div>
                                <p>¡Es precioso! Mucho mejor que en las fotos. Los detalles son increíbles y la calidad del hilo se nota a simple vista.</p>
                            </div>
                        </div>

                        <div class="review-item">
                            <div class="review-avatar" style="background: #e0f2fe; color: #0284c7;">CR</div>
                            <div class="review-content">
                                <div class="review-header">
                                    <h6>Carlos Ruiz</h6>
                                    <div class="review-stars">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p>Llegó muy rápido y el envoltorio era hermoso. Ideal para regalo, mi esposa quedó encantada.</p>
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
</script>