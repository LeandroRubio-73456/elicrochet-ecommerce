<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('Logo.png') }}" alt="EliCrochet" height="60">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="ti-menu fs-3"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('home') ? 'active fw-bold text-gradient-purple' : '' }}" href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('shop') || Route::is('category.show') ? 'active fw-bold text-gradient-purple' : '' }}" href="{{ route('shop') }}">Tienda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('contact') ? 'active fw-bold text-gradient-purple' : '' }}" href="{{ route('contact') }}">Contacto</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                @auth
                    <a href="{{ route('cart') }}" class="btn btn-icon btn-light position-relative" id="navbar-cart-btn">
                        <i class="ti-shopping-cart fs-5"></i>
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('cart.login-required') }}" class="btn btn-icon btn-light">
                        <i class="ti-shopping-cart fs-5"></i>
                    </a>
                @endauth
                
                @auth
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=C16244&color=fff" alt="User" class="rounded-circle me-2" width="32" height="32">
                            <span class="d-none d-lg-inline text-dark">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userMenu">
                            @if(Auth::user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="ti ti-dashboard me-2"></i> Panel Admin</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="ti ti-user me-2"></i> Mi Cuenta</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="ti ti-logout me-2"></i> Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm text-white">Registrarse</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
