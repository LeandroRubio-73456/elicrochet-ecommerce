@extends('layouts.front-layout')
@section('content')

<!-- Hero Section - Minimalista y Moderno -->
<section class="hero-modern">
    <div class="container">
        <div class="row align-items-center min-vh-80 py-5">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="hero-content">
                    <span class="badge-custom mb-4">
                        <i class="ti ti-heart"></i>
                        Hecho con amor
                    </span>
                    <h1 class="hero-title mb-2">
                        Tejidos únicos<br>
                        <span class="text-gradient">con alma artesanal</span>
                    </h1>
                    <p class="hero-subtitle mb-4">
                        Cada pieza cuenta una historia. Descubre amigurumis y accesorios tejidos a mano con dedicación y los mejores materiales.
                    </p>
                    <div class="hero-actions">
                        <a href="#featured" class="btn-modern btn-primary">
                            Explorar colección
                            <i class="ti ti-arrow-right"></i>
                        </a>
                        <a href="#categories" class="btn-modern btn-ghost">Ver categorías</a>
                    </div>

                </div>
            </div>
            <div class="col-lg-7">
                <div class="hero-image-container">
                    <div class="hero-image-wrapper">
                        <picture>
                            <source media="(min-width: 451px)" srcset="{{ asset('assets/images/hero-banner.webp') }}" width="780" height="584">
                            <img src="{{ asset('assets/images/hero-banner.webp') }}"
                                 alt="Amigurumis artesanales" 
                                 class="hero-image img-fluid"
                                 loading="eager"
                                 fetchpriority="high">
                        </picture>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section - Grid Minimalista -->
<section id="categories" class="section-padding bg-soft">
    <div class="container">
        <x-section-header label="Categorías" title="Explora por tipo" />
        
        <div class="categories-grid">
            @forelse($categories as $category)
                <x-category-card :category="$category" />
            @empty
            <div class="col-span-full text-center py-5">
                <p class="text-muted">No hay categorías disponibles</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Products - Grid Moderno -->
<section id="featured" class="section-padding bg-white">
    <div class="container">
        <x-section-header label="Destacados" title="Nuestras favoritas" :link="route('shop')" linkText="Ver todo" />

        <div class="products-grid">
            @forelse($featuredProducts as $index => $product)
                <div class="wow fadeInUp" data-wow-delay="{{ $index * 0.1 }}s">
                    <x-product-card :product="$product" />
                </div>
            @empty
            <div class="col-span-full text-center py-5">
                <p class="text-muted">No hay productos destacados</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Custom Orders CTA - Minimalista -->
<section class="section-padding bg-soft">
    <div class="container">
        <div class="cta-card-modern p-5 wow fadeInUp w-100 d-block" data-wow-delay="0.2s">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="d-flex flex-column flex-lg-row align-items-center text-center text-lg-start mb-4 mb-lg-0">
                        <div class="flex-shrink-0 mb-4 mb-lg-0 me-lg-4">
                             <img src="{{ asset('assets/images/PedidoPersonalizado.webp') }}"
                                  alt="Personalización"
                                  class="img-fluid" style="max-height: 120px;">
                        </div>
                        
                        <div class="cta-content flex-grow-1">
                            <span class="cta-badge mb-3">Pedidos personalizados</span>
                            <h2 class="cta-title mb-3">¿Tienes algo en mente?</h2>
                            <p class="cta-text text-black mb-4">
                                Creamos piezas únicas según tu visión. Desde diseños personalizados hasta colores específicos, hacemos realidad tus ideas.
                            </p>
                            <div class="d-flex align-items-center gap-4 flex-wrap justify-content-center justify-content-lg-start">
                                <a href="{{ route('account.custom.create') }}" class="btn-modern btn-primary">
                                    Solicitar ahora
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                                <a href="{{ route('contact') }}" class="btn-modern btn-ghost">Contactar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="{{ asset('assets/images/banner-cotizacion.webp') }}" 
                         alt="Pedido Personalizado" 
                         class="img-fluid"
                         style="object-fit: contain; border-radius: 10px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features - Iconos Minimalistas -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="features-grid">
            <div class="feature-modern">
                <div class="feature-icon">
                    <i class="ti ti-check"></i>
                </div>
                <h3 class="feature-title">Calidad garantizada</h3>
                <p class="feature-text">Materiales premium seleccionados cuidadosamente para cada pieza</p>
            </div>
            <div class="feature-modern">
                <div class="feature-icon">
                    <i class="ti ti-heart"></i>
                </div>
                <h3 class="feature-title">Hecho con amor</h3>
                <p class="feature-text">Cada puntada lleva dedicación y atención a los detalles</p>
            </div>
            <div class="feature-modern">
                <div class="feature-icon">
                    <i class="ti ti-truck"></i>
                </div>
                <h3 class="feature-title">Envío seguro</h3>
                <p class="feature-text">Empaque cuidadoso y seguimiento en cada entrega</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials - Slider Minimalista -->
<section class="section-padding bg-soft">
    <div class="container">
        <x-section-header label="Testimonios" title="Historias de clientes" />
        
        <div class="testimonials-grid">
            <x-testimonial-card
                name="María González" 
                text="La calidad es excepcional. Mi hija adora su amigurumi y duerme con él todas las noches. Definitivamente volveré a comprar."
                image="https://randomuser.me/api/portraits/women/32.jpg"
            />
            <x-testimonial-card
                name="Carlos Rodríguez" 
                text="Excelente servicio y productos hermosos. El pedido personalizado quedó perfecto, superó mis expectativas."
                image="https://randomuser.me/api/portraits/men/45.jpg"
            />
            <x-testimonial-card
                name="Laura Martínez" 
                text="Atención personalizada y rápida. Los productos son hermosos y llegaron bien empaquetados. Recomendado 100%."
                image="https://randomuser.me/api/portraits/women/68.jpg"
            />
        </div>
    </div>
</section>

<!-- Newsletter - Minimalista -->
<section class="newsletter-modern">
    <div class="container">
        <div class="newsletter-content">
            <h2 class="newsletter-title">Mantente al día</h2>
            <p class="newsletter-text">Suscríbete para recibir novedades, ofertas exclusivas y tips de cuidado</p>
            <form class="newsletter-form">
                <input type="email" required>
                <button type="submit" class="btn-modern btn-primary">Suscribirse</button>
            </form>
        </div>
    </div>
</section>

@endsection
