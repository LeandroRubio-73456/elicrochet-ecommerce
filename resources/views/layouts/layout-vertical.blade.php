<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->
<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
                <img src="{{ asset('assets/images/Logo.webp') }}" class="img-fluid" alt="EliCrochet" width="50" height="50" style="height: 50px;">
                <span class="ms-2 text-dark fw-bold f-15">Panel Administrativo</span>
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <span>Ventas</span>
                </li>
                <li class="pc-item {{ Route::is('admin.orders.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-clipboard"></i></span>
                        <span class="pc-mtext">Órdenes</span>
                    </a>
                </li>
                <li class="pc-item {{ Route::is('admin.finance.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.finance.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-chart-bar"></i></span>
                        <span class="pc-mtext">Reportes</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <span>Inventario</span>
                </li>
                <li class="pc-item {{ Route::is('admin.products.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.products.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-package"></i></span>
                        <span class="pc-mtext">Productos</span>
                    </a>
                </li>
                <li class="pc-item {{ Route::is('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-tag"></i></span>
                        <span class="pc-mtext">Categorias</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <span>Usuarios</span>
                </li>
                <li class="pc-item {{ Route::is('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                        <span class="pc-mtext">Usuarios</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>

            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <!-- Notifications Bell -->
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0 btn btn-link" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-bell"></i>
                        @if($totalNotifications > 0)
                            <span class="badge bg-danger pc-h-badge dots"><span class="sr-only"></span></span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h5 class="m-0">Notificaciones</h5>
                        </div>
                        <div class="dropdown-body p-0">
                            <div class="list-group list-group-flush border-top-0">
                                @if($pendingQuotesCount > 0)
                                <a href="{{ route('admin.orders.index', ['status' => 'quotation']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avtar avtar-s bg-light-warning">
                                                <i class="ti ti-wand text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Cotizaciones Pendientes ({{ $pendingQuotesCount }})</h6>
                                            <p class="mb-0 text-muted small">
                                                @foreach($pendingQuotes as $q)
                                                    #{{ $q->order_number }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                {{ $pendingQuotesCount > 3 ? '...' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                @endif

                                @if($paidOrdersCount > 0)
                                <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avtar avtar-s bg-light-success">
                                                <i class="ti ti-cash text-success"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Pagos por Procesar ({{ $paidOrdersCount }})</h6>
                                            <p class="mb-0 text-muted small">
                                                @foreach($paidOrders as $p)
                                                    {{ $p->customer_name ?? 'Invitado' }} (#{{ $p->order_number }}){{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                {{ $paidOrdersCount > 3 ? '...' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                @endif

                                @if($lowStockCount > 0)
                                <a href="{{ route('admin.products.index', ['sort' => 'stock', 'direction' => 'asc']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avtar avtar-s bg-light-danger">
                                                <i class="ti ti-package text-danger"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Stock Bajo ({{ $lowStockCount }})</h6>
                                            <p class="mb-0 text-muted small">
                                                @foreach($lowStockProducts as $lp)
                                                    {{ $lp->name }} ({{ $lp->stock }}){{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                {{ $lowStockCount > 3 ? '...' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                @endif

                                @if($workingOrdersCount > 0)
                                <a href="{{ route('admin.orders.index', ['status' => 'working']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex {{ $completedOrdersCount > 0 ? '' : 'border-bottom-0' }}">
                                        <div class="flex-shrink-0">
                                            <div class="avtar avtar-s bg-light-info">
                                                <i class="ti ti-tools text-info"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">En Fabricación ({{ $workingOrdersCount }})</h6>
                                            <p class="mb-0 text-muted small">Monitorizando procesos activos.</p>
                                        </div>
                                    </div>
                                </a>
                                @endif

                                @if($completedOrdersCount > 0)
                                <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex border-bottom-0">
                                        <div class="flex-shrink-0">
                                            <div class="avtar avtar-s bg-light-success">
                                                <i class="ti ti-check text-success"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Recientemente Completados</h6>
                                            <p class="mb-0 text-muted small">
                                                @foreach($completedOrders as $co)
                                                    {{ $co->customer_name ?? 'Invitado' }} (#{{ $co->order_number }}){{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                {{ $completedOrdersCount > 3 ? '...' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                @endif

                                @if($totalNotifications == 0 && $workingOrdersCount == 0 && $completedOrdersCount == 0)
                                <div class="p-3 text-center">
                                    <i class="ti ti-circle-check text-success fs-2 mb-2"></i>
                                    <p class="mb-0">¡Todo al día! No hay pendientes urgentes.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>

                <!-- User Profile -->
                <li class="dropdown pc-h-item header-user-profile">
                    <button class="pc-head-link dropdown-toggle arrow-none me-0 btn btn-link" data-bs-toggle="dropdown"
                         aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false" type="button">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff" alt="User" class="user-avtar">
                        <span>{{ Auth::user()->name }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex mb-1">
                                <div class="flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff" alt="User"
                                        class="user-avtar wid-35">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                    <span>{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-body p-3">
                            <button
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="dropdown-item" type="button">
                                <i class="ti ti-power"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    </div>
</header>
<!-- [ Header ] end -->
