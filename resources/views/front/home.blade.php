@extends('layouts.front-layout')
@section('content')

<!-- Hero Section with Parallax Effect -->
<section class="hero-section position-relative overflow-hidden">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-lg-6 text-center text-lg-start py-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3 d-inline-flex align-items-center">
                    <i class="bi bi-star-fill me-2"></i> Hecho a mano con amor
                </span>
                <h1 class="display-4 fw-bold mb-4 text-dark">Crea momentos mágicos <span class="text-primary">con amor tejido</span></h1>
                <p class="lead text-muted mb-4">Descubre nuestra colección única de amigurumis y accesorios tejidos a mano con los mejores materiales y mucha dedicación.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#featured" class="btn btn-primary btn-lg px-4 py-3 shadow">
                        Ver Colección <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    <a href="#categories" class="btn btn-outline-primary btn-lg px-4 py-3">
                        Nuestras Categorías
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4 mt-4">
                    <div class="d-flex">
                        <div class="d-flex">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" class="rounded-circle border border-3 border-white" width="40" height="40" alt="Cliente satisfecho">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="rounded-circle border border-3 border-white ms-n2" width="40" height="40" alt="Cliente satisfecho">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle border border-3 border-white ms-n2" width="40" height="40" alt="Cliente satisfecho">
                        </div>
                        <div class="ms-2">
                            <div class="d-flex text-warning small">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <span class="small text-muted">+500 clientes felices</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image-container position-relative">
                    <div class="hero-shape-1"></div>
                    <div class="hero-shape-2"></div>
                    <div class="hero-main-image position-relative">
                        <img src="https://images.unsplash.com/photo-1618354691373-85154177d6db?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" 
                             alt="Amigurumis hechos a mano" 
                             class="img-fluid rounded-4 shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-wave">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
        </svg>
    </div>
</section>

<!-- Featured Categories Section -->
<section id="categories" class="py-5 bg-light">
    <div class="container py-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="text-primary fw-bold d-block mb-2">Nuestras Categorías</span>
                <h2 class="fw-bold mb-3">Explora nuestros productos por categoría</h2>
                <p class="text-muted">Encuentra el amigurumi perfecto para ti o para regalar en ocasiones especiales.</p>
            </div>
        </div>
        <div class="row g-4">
            @forelse($categories as $category)
            <div class="col-lg-4 col-md-6">
                <div class="category-card card border-0 h-100 shadow-sm overflow-hidden hover-lift">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-4">
                            <i class="{{ $category->icon ?? 'bi bi-grid' }} text-primary" style="font-size: 1.75rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-bold">{{ $category->name }}</h5>
                            <p class="text-muted small mb-0">{{ Str::limit($category->description, 60) }}</p>
                        </div>
                        <i class="bi bi-arrow-right-short text-primary fs-4"></i>
                    </div>
                    <a href="{{ route('category.show', $category->slug) }}" class="stretched-link"></a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted">No hay categorías disponibles en este momento.</div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured" class="py-5 bg-white">
    <div class="container py-5">
        <div class="row justify-content-between align-items-center mb-5">
            <div class="col-md-8">
                <span class="text-primary fw-bold d-block mb-2">Productos Destacados</span>
                <h2 class="fw-bold mb-0">Nuestras creaciones más populares</h2>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                    Ver todos los productos <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        <div class="row g-4">
            @forelse($featuredProducts as $product)
            <div class="col-lg-3 col-md-6">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <div class="position-relative overflow-hidden">
                        @if($product->images->first())
                        <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                             class="card-img-top" 
                             alt="{{ $product->name }}"
                             style="height: 200px; object-fit: cover;">
                        @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                        @endif
                        @if($product->is_featured)
                        <span class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-danger">Destacado</span>
                        </span>
                        @endif
                        <div class="product-actions position-absolute w-100 d-flex justify-content-center" style="bottom: -50px; transition: all 0.3s ease;">
                            <button class="btn btn-primary rounded-circle me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Añadir al carrito">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                            <button class="btn btn-outline-primary rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <a href="{{ route('product.show', $product->slug) }}" class="text-dark text-decoration-none">
                                <h5 class="card-title mb-1">{{ $product->name }}</h5>
                            </a>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <span class="small">4.8</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-2">{{ Str::limit($product->description, 60) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary fw-bold">${{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-muted small">En stock: {{ $product->stock }}</span>
                        </div>
                    </div>
                    <a href="{{ route('product.show', $product->slug) }}" class="stretched-link"></a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted">No hay productos destacados en este momento.</div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="text-primary fw-bold d-block mb-2">¿Por qué elegirnos?</span>
                <h2 class="fw-bold mb-3">Hecho con amor y dedicación</h2>
                <p class="text-muted">Cada pieza es única y creada con los más altos estándares de calidad.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                        <i class="bi bi-hand-thumbs-up text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Calidad Premium</h5>
                    <p class="text-muted mb-0">Utilizamos solo los mejores materiales para garantizar la durabilidad y suavidad de cada pieza.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                        <i class="bi bi-heart text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Hecho a Mano</h5>
                    <p class="text-muted mb-0">Cada pieza es tejida a mano con amor y atención a los detalles más pequeños.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mx-auto">
                <div class="feature-card p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                        <i class="bi bi-truck text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Envíos Rápidos</h5>
                    <p class="text-muted mb-0">Entregamos tus productos de forma rápida y segura a cualquier parte del país.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-white">
    <div class="container py-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="text-primary fw-bold d-block mb-2">Testimonios</span>
                <h2 class="fw-bold mb-3">Lo que dicen nuestros clientes</h2>
                <p class="text-muted">Descubre la experiencia de quienes ya han comprado con nosotros.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card p-4 h-100 bg-light rounded-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" class="rounded-circle me-3" width="60" height="60" alt="Cliente satisfecho">
                        <div>
                            <h6 class="mb-0 fw-bold">María González</h6>
                            <div class="text-warning small">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">"¡Los amigurumis son hermosos! La calidad del tejido es increíble y los detalles son perfectos. Mi sobrina los adora."</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card p-4 h-100 bg-light rounded-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" class="rounded-circle me-3" width="60" height="60" alt="Cliente satisfecho">
                        <div>
                            <h6 class="mb-0 fw-bold">Carlos Rodríguez</h6>
                            <div class="text-warning small">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">"Excelente atención al cliente y productos de primera calidad. El envío llegó antes de lo esperado y todo en perfecto estado."</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mx-auto">
                <div class="testimonial-card p-4 h-100 bg-light rounded-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" class="rounded-circle me-3" width="60" height="60" alt="Cliente satisfecho">
                        <div>
                            <h6 class="mb-0 fw-bold">Laura Martínez</h6>
                            <div class="text-warning small">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">"Compré varios productos y todos son hermosos. La atención personalizada hace toda la diferencia. ¡Volveré a comprar!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5 bg-primary text-white">
    <div class="container py-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3">¡No te pierdas nuestras novedades!</h2>
                <p class="mb-4">Suscríbete a nuestro boletín y recibe ofertas exclusivas y actualizaciones sobre nuevos productos.</p>
                <form class="row g-2 justify-content-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-lg" placeholder="Tu correo electrónico" required>
                            <button class="btn btn-light text-primary px-4" type="submit">Suscribirse</button>
                        </div>
                        <div class="form-text text-white-50 mt-2">Respetamos tu privacidad. No compartiremos tu información.</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Instagram Feed -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="text-primary fw-bold d-block mb-2">Síguenos</span>
                <h2 class="fw-bold mb-3">@EliCrochetOficial</h2>
                <p class="text-muted">Mira nuestras últimas creaciones y proyectos en Instagram.</p>
                <a href="#" class="btn btn-outline-primary">
                    <i class="bi bi-instagram me-2"></i> Seguir en Instagram
                </a>
            </div>
        </div>
        <div class="row g-3">
            @for($i = 1; $i <= 6; $i++)
            <div class="col-lg-2 col-md-4 col-6">
                <div class="instagram-post position-relative overflow-hidden rounded-3">
                    <img src="https://source.unsplash.com/random/300x300/?crochet,amigurumi,handmade,{{ $i }}" 
                         class="img-fluid" 
                         alt="Instagram post {{ $i }}"
                         style="height: 200px; width: 100%; object-fit: cover;">
                    <div class="instagram-overlay d-flex align-items-center justify-content-center">
                        <i class="bi bi-heart-fill text-white fs-4 me-2"></i>
                        <span class="text-white fw-bold">{{ rand(50, 500) }}</span>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- Add some custom styles -->
@push('styles')
<style>
    /* Hero Section Styles */
    /* Optimized Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 120px 0 80px;
        position: relative;
        overflow: hidden;
        will-change: transform;
    }
    
    /* Smooth image loading */
    img {
        transition: opacity 0.5s ease;
        opacity: 0;
    }
    
    img.loaded {
        opacity: 1;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50%;
        height: 100%;
        background: url('https://images.unsplash.com/photo-1618354691373-85154177d6db?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80') no-repeat center center/cover;
        clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%);
    }

    .hero-wave {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
        transform: rotate(180deg);
    }

    .hero-wave svg {
        position: relative;
        display: block;
        width: calc(100% + 1.3px);
        height: 100px;
    }

    .hero-wave .shape-fill {
        fill: #FFFFFF;
    }

    /* Smooth Transitions */
    .category-card,
    .product-card,
    .feature-card,
    .testimonial-card,
    .btn,
    .instagram-post {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        backface-visibility: hidden;
        transform: translateZ(0);
    }

    /* Category Card */
    .category-card {
        border: 1px solid rgba(0,0,0,0.05);
    }

    /* Product Card Styles */
    .product-card {
        overflow: hidden;
    }

    .product-card .product-actions {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        opacity: 0;
    }

    /* Instagram Overlay */
    .instagram-post {
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .instagram-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.4);
        opacity: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        transform: translateY(10px);
    }
    
    .instagram-post:hover .instagram-overlay {
        opacity: 1;
        transform: translateY(0);
    }

    /* Testimonial Card */
    .testimonial-card {
        border: 1px solid rgba(0,0,0,0.05);
    }

    /* Responsive Adjustments */
    @media (max-width: 991.98px) {
        .hero-section {
            padding: 100px 0 60px;
            text-align: center;
        }

        .hero-section::before {
            display: none;
        }

        .hero-image {
            margin-top: 40px;
        }

        .feature-card, .testimonial-card {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 767.98px) {
        .hero-section {
            padding: 80px 0 40px;
        }

        .display-4 {
            font-size: 2.5rem;
        }
    }

    /* Faster Animate.css animations */
    .animate__animated {
        animation-duration: 0.4s !important;
    }

    .animate__fadeInUp {
        animation-timing-function: cubic-bezier(0.25, 0.8, 0.25, 1) !important;
    }

    /* Initial state for elements before animation */
    .feature-card:not(.animate__animated),
    .testimonial-card:not(.animate__animated),
    .product-card:not(.animate__animated),
    .category-card:not(.animate__animated) {
        opacity: 0;
        transform: translateY(30px) translateZ(0);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Enhanced scroll animation with Intersection Observer
        const elements = document.querySelectorAll('.feature-card, .testimonial-card, .product-card, .category-card, .animate-on-scroll');
        
        const observerOptions = {
            threshold: 0.05, // Trigger when only 5% of element is visible
            rootMargin: '0px 0px 200px 0px' // Start animation 200px before element enters viewport
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    // Add staggered delay for smoother animation
                    setTimeout(() => {
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                        
                        // Remove animation classes after animation ends to allow hover effects
                        entry.target.addEventListener('animationend', function() {
                            this.classList.remove('animate__animated', 'animate__fadeInUp');
                            // Ensure element stays visible and in final position
                            this.style.opacity = '1';
                            this.style.transform = 'translateY(0) translateZ(0)';
                        }, { once: true });
                    }, index * 50); // 50ms delay between each element
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        elements.forEach(element => {
            observer.observe(element);
        });

        // Lazy load images
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.onload = () => img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    });
</script>
@endpush
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section id="coleccion" class="py-5 bg-light">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-gradient-purple mb-3">Productos Destacados</h2>
                <p class="text-muted">Creaciones únicas seleccionadas para ti</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @forelse($featuredProducts as $product)
            <div class="col-lg-3 col-md-6">
                <x-product-card :product="$product" />
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">No hay productos destacados por el momento.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

@endsection