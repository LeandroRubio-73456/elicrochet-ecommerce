@extends('layouts.front-layout')

@section('title', 'EliCrochet | Hecho a Mano con Amor')

@section('content')

<style>
    /* Estilos adicionales para mejorar la vista */
    .hero-section {
        background: linear-gradient(135deg, #fdf6f9 0%, #fcecf4 50%, #fbe5f0 100%);
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23f06292' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.6;
    }

    .hero-image {
        position: relative;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .floating-badge {
        position: absolute;
        padding: 10px 15px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        animation: badgeFloat 4s ease-in-out infinite;
        z-index: 10;
    }

    .badge-1 {
        top: 20%;
        left: -20px;
        animation-delay: 0s;
    }

    @keyframes badgeFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(5deg); }
    }

    .category-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        background: white;
        overflow: hidden;
        position: relative;
        cursor: pointer;
    }

    .category-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(240, 98, 146, 0.15);
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px 15px 0 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .category-card:hover::before {
        opacity: 1;
    }

    .category-icon {
        transition: transform 0.3s ease;
    }

    .category-card:hover .category-icon {
        transform: scale(1.2);
    }

    .product-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .product-badges {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 2;
    }

    .product-badges .badge {
        font-size: 0.75rem;
        padding: 5px 10px;
        margin-right: 5px;
        font-weight: 600;
    }

    .product-image {
        position: relative;
        overflow: hidden;
        height: 250px;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 20px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .product-card:hover .product-overlay {
        transform: translateY(0);
    }

    .rating i {
        color: #ffc107;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-outline-primary {
        border: 2px solid #667eea;
        color: #667eea;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
    }

    .testimonial-card {
        border: none;
        border-radius: 15px;
        background: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .testimonial-card::before {
        content: '"';
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 60px;
        color: #667eea;
        opacity: 0.1;
        font-family: serif;
    }

    .newsletter-section {
        background: linear-gradient(135deg, #fdf6f9 0%, #fcecf4 100%);
        position: relative;
        overflow: hidden;
    }

    .newsletter-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23667eea' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .feature-highlight {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 50px 0;
    }

    .feature-item {
        text-align: center;
        padding: 30px;
        transition: transform 0.3s ease;
    }

    .feature-item:hover {
        transform: translateY(-10px);
    }

    .feature-icon-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .feature-icon-circle i {
        font-size: 32px;
        color: white;
    }

    .countdown-timer {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
    }

    .countdown-label {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>

<header id="home">
    <div class="hero-section" style="padding-top: 100px;">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6">
                    <h1 class="mt-sm-5 text-dark mb-4 f-w-600 wow fadeInUp" data-wow-delay="0.2s">
                        Muñecos y Amigurumis <span class="text-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Hechos con Amor</span>
                    </h1>
                    <h5 class="mb-4 text-dark opacity-75 wow fadeInUp" data-wow-delay="0.4s">
                        Descubre nuestra colección única de figuras de crochet. Cada pieza es tejida a mano con dedicación, utilizando materiales premium que garantizan calidad y durabilidad.
                    </h5>
                    <div class="my-5 wow fadeInUp" data-wow-delay="0.6s">
                        <a href="#productos" class="btn btn-primary me-3 shadow-lg">
                            <i class="ti ti-heart-handshake me-2"></i> Explorar Colección
                        </a>
                        <a href="#ofertas" class="btn btn-outline-primary">
                            <i class="ti ti-discount me-2"></i> Ver Ofertas Especiales
                        </a>
                    </div>
                    <div class="row mt-4 wow fadeInUp" data-wow-delay="0.8s">
                        <div class="col-4 text-center">
                            <div class="countdown-timer">500+</div>
                            <div class="countdown-label">Clientes Felices</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="countdown-timer">250+</div>
                            <div class="countdown-label">Diseños Únicos</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="countdown-timer">100%</div>
                            <div class="countdown-label">Hecho a Mano</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="hero-image position-relative wow fadeInUp" data-wow-delay="0.4s">
                        <img src="{{ asset('assets/images/ecommerce/hero-crochet.png') }}" alt="Productos de Crochet" class="img-fluid rounded shadow-lg">
                        <div class="floating-badge badge-1">
                            <span class="badge bg-primary mb-1">🌟 Artesanía Premium</span>
                            <small class="text-muted">Hecho con amor</small>
                        </div>
                        <div class="floating-badge" style="top: 70%; right: -20px; animation-delay: 2s;">
                            <span class="badge bg-warning mb-1">🎁 Envío Gratis</span>
                            <small class="text-muted">+$50.000</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Sección de Categorías -->
<section id="categorias" class="py-5">
    <div class="container">
        <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
            <div class="col-md-10 col-xl-6">
                <h5 class="text-primary mb-0">Explora la Magia</h5>
                <h2 class="my-3">Categorías de Amigurumis</h2>
                <p class="mb-0">Encuentra personajes, accesorios y kits para tejer, organizados por temática.</p>
            </div>
        </div>
        <div class="row mt-4 g-4">
            @forelse ($categories as $category)
                <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                    <div class="card category-card wow fadeInUp h-100" data-wow-delay="{{ $loop->index * 0.1 + 0.3 }}s">
                        <div class="card-body text-center d-flex flex-column justify-content-center p-4">
                            <div class="category-icon mb-3">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i class="ti ti-yarn f-24 text-white"></i>
                                </div>
                            </div>
                            <h6 class="mb-2 fw-bold">{{ $category->name }}</h6>
                            <p class="text-muted small mb-0">{{ $category->products_count ?? 0 }} productos</p>
                            <div class="mt-2">
                                <span class="badge bg-light text-primary">Ver más →</span>
                            </div>
                            <a href="{{ route('back.categories.show', $category->slug) }}" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="ti ti-mood-sad f-48 text-muted mb-3"></i>
                        <p class="text-muted">No hay categorías disponibles por el momento.</p>
                    </div>
                </div>
            @endforelse
        </div>
        @if($categories->count() > 6)
            <div class="text-center mt-5 wow fadeInUp" data-wow-delay="0.5s">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                    <i class="ti ti-arrows-sort me-2"></i> Ver Todas las Categorías
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Sección de Características -->
<section class="feature-highlight py-5">
    <div class="container">
        <div class="row justify-content-center text-center mb-5 wow fadeInUp" data-wow-delay="0.2s">
            <div class="col-md-10 col-xl-6">
                <h2 class="text-white mb-3">¿Por qué Elegir EliCrochet?</h2>
                <p class="text-white-50 mb-0">Calidad, dedicación y atención personalizada en cada creación</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-item wow fadeInUp" data-wow-delay="0.3s">
                    <div class="feature-icon-circle">
                        <i class="ti ti-hand-finger"></i>
                    </div>
                    <h4 class="text-white mb-3">Hecho 100% a Mano</h4>
                    <p class="text-white-50">Cada pieza es tejida manualmente con técnicas tradicionales y mucho cuidado.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item wow fadeInUp" data-wow-delay="0.4s">
                    <div class="feature-icon-circle">
                        <i class="ti ti-seeding"></i>
                    </div>
                    <h4 class="text-white mb-3">Materiales Premium</h4>
                    <p class="text-white-50">Utilizamos hilos de la mejor calidad, hipoalergénicos y seguros para todas las edades.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item wow fadeInUp" data-wow-delay="0.5s">
                    <div class="feature-icon-circle">
                        <i class="ti ti-truck-delivery"></i>
                    </div>
                    <h4 class="text-white mb-3">Envío Rápido y Seguro</h4>
                    <p class="text-white-50">Tu amigurumi llegará en perfectas condiciones con nuestro embalaje especial.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de Productos Destacados -->
<section id="productos" class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
            <div class="col-md-10 col-xl-6">
                <h5 class="text-primary mb-0">Lo Más Popular</h5>
                <h2 class="my-3">Amigurumis Destacados</h2>
                <p class="mb-0">Los favoritos de nuestros clientes, seleccionados por su calidad y diseño.</p>
            </div>
        </div>
        <div class="row mt-4 g-4">
            @forelse ($featuredProducts as $product)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card product-card wow fadeInUp h-100" data-wow-delay="{{ $loop->index * 0.1 + 0.3 }}s">
                        <div class="product-badges">
                            @if($product->is_on_sale)
                                <span class="badge bg-danger">🔥 {{ $product->discount_percentage ?? '10%' }} OFF</span>
                            @endif
                            @if($product->is_new)
                                <span class="badge bg-success">✨ Nuevo</span>
                            @endif
                            @if($product->is_bestseller)
                                <span class="badge bg-warning text-dark">⭐ Más Vendido</span>
                            @endif
                        </div>
                        <div class="product-image">
                            @php
                                $mainImage = $product->images->first();
                                $imagePath = $mainImage ? $mainImage->image_path : 'placeholder/no-image.jpg';
                            @endphp
                            <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $product->name }}" class="img-fluid">
                            <div class="product-overlay">
                                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-eye me-1"></i> Ver
                                </a>
                                <button class="btn btn-light btn-sm add-to-cart" data-product-id="{{ $product->id }}">
                                    <i class="ti ti-shopping-cart me-1"></i> Carrito
                                </button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none">
                                    <h6 class="mb-0 text-dark fw-bold">{{ $product->name }}</h6>
                                </a>
                                <div class="rating">
                                    <i class="ti ti-star-filled text-warning"></i>
                                    <small class="text-muted ms-1">{{ $product->average_rating ?? 5.0 }}</small>
                                </div>
                            </div>
                            <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($product->short_description ?? $product->description, 60) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    @if($product->old_price)
                                        <span class="text-decoration-line-through text-muted me-2 small">${{ number_format($product->old_price, 0) }}</span>
                                    @endif
                                    <span class="h5 mb-0 text-primary fw-bold">${{ number_format($product->price, 0) }}</span>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" title="Añadir a Favoritos" onclick="addToWishlist({{ $product->id }})">
                                    <i class="ti ti-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="ti ti-package f-48 text-muted mb-3"></i>
                        <p class="text-muted">Pronto tendremos nuevos amigurumis disponibles</p>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary mt-3">
                            <i class="ti ti-bell-ringing me-2"></i> Notificarme
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
        @if($featuredProducts->count() > 0)
            <div class="text-center mt-5 wow fadeInUp" data-wow-delay="0.5s">
                <a href="" class="btn btn-primary px-5">
                    <i class="ti ti-arrow-right me-2"></i> Ver Todos los Productos
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Sección de Testimonios -->
<section id="testimonios" class="py-5">
    <div class="container">
        <div class="row justify-content-center text-center mb-5 wow fadeInUp" data-wow-delay="0.2s">
            <div class="col-md-10 col-xl-6">
                <h5 class="text-primary mb-0">Historias de Amor</h5>
                <h2 class="my-3">Lo que dicen nuestros clientes</h2>
                <p class="mb-0">La felicidad de nuestros clientes es nuestra mayor recompensa</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card p-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=María+García&background=667eea&color=fff" alt="María García" class="rounded-circle me-3" width="50">
                        <div>
                            <h6 class="mb-1 fw-bold">María García</h6>
                            <div class="star">
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-0">"El osito que compré para mi sobrina es perfecto. La calidad del tejido es impresionante y llegó antes de lo esperado."</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card p-4 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=Carlos+Rodríguez&background=764ba2&color=fff" alt="Carlos Rodríguez" class="rounded-circle me-3" width="50">
                        <div>
                            <h6 class="mb-1 fw-bold">Carlos Rodríguez</h6>
                            <div class="star">
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-0">"Excelente atención al cliente y productos de alta calidad. Mi hija ama su nuevo amigurumi de unicornio."</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card p-4 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=Ana+Martínez&background=f06292&color=fff" alt="Ana Martínez" class="rounded-circle me-3" width="50">
                        <div>
                            <h6 class="mb-1 fw-bold">Ana Martínez</h6>
                            <div class="star">
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-filled text-warning"></i>
                                <i class="ti ti-star-half-filled text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-0">"Pedí un diseño personalizado y superó todas mis expectativas. Definitivamente volveré a comprar."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección Nosotros -->
<section id="nosotros" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="wow fadeInLeft" data-wow-delay="0.2s">
                    <h5 class="text-primary mb-0">Nuestra Historia</h5>
                    <h2 class="my-3">Hilos, Agujas y Mucho Cariño</h2>
                    <p class="mb-4">En EliCrochet, cada puntada cuenta una historia. Desde 2018, hemos tejido sonrisas y creado compañeros especiales para momentos inolvidables. Nuestra misión es llevar la calidez de lo artesanal a cada hogar.</p>
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-check text-primary me-2"></i>
                                <span>Materiales Premium</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-check text-primary me-2"></i>
                                <span>Hecho 100% a Mano</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-check text-primary me-2"></i>
                                <span>Envío Rápido</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-check text-primary me-2"></i>
                                <span>Garantía de Calidad</span>
                            </div>
                        </div>
                    </div>
                    <a href="" class="btn btn-primary">
                        <i class="ti ti-info-circle me-2"></i> Conocer Nuestra Historia
                    </a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="position-relative wow fadeInRight" data-wow-delay="0.4s">
                    <img src="{{ asset('assets/images/ecommerce/crochet-maker.jpg') }}" alt="Filosofía EliCrochet" class="img-fluid rounded shadow-lg">
                    <div class="position-absolute bottom-0 start-0 m-4">
                        <div class="bg-white rounded-pill px-4 py-2 shadow-sm">
                            <span class="text-primary fw-bold">🎨 5+ años de experiencia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter-section py-5">
    <div class="container">
        <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
            <div class="col-md-8 col-xl-6">
                <h2 class="mb-3">¡No te pierdas nuestras novedades!</h2>
                <p class="mb-4">Suscríbete para recibir ofertas exclusivas, tutoriales y nuevas colecciones.</p>
                <form class="position-relative">
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="email" class="form-control border-0" placeholder="Tu correo electrónico" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="ti ti-send me-2"></i> Suscribirse
                        </button>
                    </div>
                </form>
                <p class="small text-muted mt-3">Prometemos no enviar spam. Puedes darte de baja en cualquier momento.</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Animación para los badges flotantes
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar evento a botones de carrito
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                addToCart(productId);
            });
        });

        // Contador animado
        const counters = document.querySelectorAll('.countdown-timer');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            const increment = target / 100;
            let current = 0;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.floor(current) + '+';
                    setTimeout(updateCounter, 20);
                } else {
                    counter.textContent = target + '+';
                }
            };
            
            updateCounter();
        });
    });

    function addToCart(productId) {
        // Aquí iría la lógica para añadir al carrito
        Toastify({
            text: "Producto añadido al carrito",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        }).showToast();
    }

    function addToWishlist(productId) {
        // Aquí iría la lógica para añadir a favoritos
        Toastify({
            text: "Añadido a favoritos",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        }).showToast();
    }
</script>
@endpush