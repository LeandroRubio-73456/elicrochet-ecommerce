<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('Logo.webp') }}" alt="EliCrochet" width="55" height="58" class="d-inline-block align-middle" fetchpriority="high" decoding="async">
        </a>
        
        <button class="navbar-toggler border-0 shadow-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-label="Toggle navigation">
            <i class="ti ti-menu-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4 gap-2">
                <li class="nav-item">
                    <a class="nav-link-modern {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-modern {{ Route::is('shop') || Route::is('category.show') ? 'active' : '' }}" href="{{ route('shop') }}">
                        Tienda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-modern {{ Route::is('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                        Contacto
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <!-- Cart Button -->
                @auth
                    <a href="{{ route('cart') }}" class="btn-icon-modern position-relative" id="navbar-cart-btn">
                        <i class="ti ti-shopping-cart"></i>
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('cart.login-required') }}" class="btn-icon-modern">
                        <i class="ti ti-shopping-cart"></i>
                    </a>
                @endauth

                <!-- User Menu -->
                @auth
                    <div class="dropdown">
                        <button class="btn-user-modern dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=C16244&color=fff"
                                 alt="{{ Auth::user()->name }}"
                                 width="32"
                                 height="32" 
                                 class="rounded-circle">
                            <span class="d-none d-lg-inline ms-2">{{ Str::limit(Auth::user()->name, 12) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-modern shadow border-0 mt-2" aria-labelledby="userMenu">
                            @if(Auth::user()->isAdmin())
                                <li>
                                    <a class="dropdown-item dropdown-item-modern" href="{{ route('admin.dashboard') }}">
                                        <i class="ti ti-layout-dashboard me-2 fs-5"></i>
                                        Panel Admin
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item dropdown-item-modern" href="{{ route('customer.dashboard') }}">
                                        <i class="ti ti-user me-2"></i>
                                        Mi Cuenta
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider my-2"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item dropdown-item-modern text-danger">
                                        <i class="ti ti-logout me-2"></i>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                        Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<style>
    /* Navbar moderno y minimalista */
    .navbar {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95) !important;
        transition: all 0.3s ease;
        padding: 0.75rem 0;
    }

    .navbar.scrolled {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    /* Links modernos con efecto underline */
    .nav-link-modern {
        position: relative;
        padding: 0.5rem 0 !important;
        color: #1a1a1a;
        font-weight: 500;
        font-size: 0.9375rem;
        transition: color 0.3s ease;
        text-decoration: none;
    }

    .nav-link-modern::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--color-primary, #C16244);
        transition: width 0.3s ease;
    }

    .nav-link-modern:hover {
        color: var(--color-primary, #C16244);
    }

    .nav-link-modern:hover::after {
        width: 100%;
    }

    .nav-link-modern.active {
        color: var(--color-primary, #C16244);
    }

    .nav-link-modern.active::after {
        width: 100%;
    }

    /* Botón de icono minimalista */
    .btn-icon-modern {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #1a1a1a;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-icon-modern:hover {
        background: #f9fafb;
        border-color: var(--color-primary, #C16244);
        color: var(--color-primary, #C16244);
        transform: translateY(-2px);
    }

    /* Botón de usuario moderno */
    .btn-user-modern {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: white;
        color: #1a1a1a;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-user-modern:hover {
        border-color: var(--color-primary, #C16244);
        background: #f9fafb;
    }

    .btn-user-modern::after {
        margin-left: 0.5rem;
    }

    /* Dropdown moderno */
    .dropdown-modern {
        border-radius: 12px;
        padding: 0.5rem;
        min-width: 200px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .dropdown-item-modern {
        display: flex;
        align-items: center;
        padding: 0.75rem 0.875rem;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .dropdown-item-modern:hover {
        background: #f9fafb;
        color: var(--color-primary, #C16244);
        transform: translateX(4px);
    }

    .dropdown-item-modern.text-danger:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .dropdown-item-modern svg {
        flex-shrink: 0;
    }

    /* Botones de auth modernos */
    .btn-outline-primary.rounded-pill {
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary.rounded-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.2);
    }

    .btn-primary.rounded-pill {
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary.rounded-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3);
    }

    /* Mobile responsive */
    @media (max-width: 991px) {
        .navbar-nav {
            padding: 1rem 0;
        }

        .nav-link-modern {
            padding: 0.75rem 0 !important;
        }

        .navbar .d-flex {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
    }

    /* Scroll behavior */
    @media (min-width: 992px) {
        .navbar {
            padding: 1rem 0;
        }
    }
</style>

@push('scripts')
<script>
    // Efecto scroll en navbar
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>
@endpush