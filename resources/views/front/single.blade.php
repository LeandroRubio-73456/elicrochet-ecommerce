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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
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
                        <div class="thumbnail-item {{ $loop->first ? 'active' : '' }}" 
                             onclick="changeImage(this, '{{ asset('storage/' . $image->image_path) }}')">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="Thumbnail">
                        </div>
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
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24" class="half-star">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>Hecho 100% a mano con hilo de algodón premium</span>
                        </div>
                        <div class="feature-item">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="1" y="3" width="15" height="13"/>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
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
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <line x1="5" y1="12" x2="19" y2="12"/>
                                            </svg>
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
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <line x1="12" y1="5" x2="12" y2="19"/>
                                                <line x1="5" y1="12" x2="19" y2="12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Add to Cart Button -->
                                <button type="submit" class="btn-add-to-cart">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="9" cy="21" r="1"/>
                                        <circle cx="20" cy="21" r="1"/>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                                    </svg>
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
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4M12 8h.01"/>
                                </svg>
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
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 8v4M12 16h.01"/>
                            </svg>
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
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p>No hay especificaciones adicionales para mostrar</p>
                    </div>
                    @endif
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews">
                    <div class="reviews-notice">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                        <span>Las opiniones mostradas son un ejemplo visual. Próximamente integración real.</span>
                    </div>

                    <div class="reviews-list">
                        <div class="review-item">
                            <div class="review-avatar">AM</div>
                            <div class="review-content">
                                <div class="review-header">
                                    <h6>Ana María</h6>
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
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
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