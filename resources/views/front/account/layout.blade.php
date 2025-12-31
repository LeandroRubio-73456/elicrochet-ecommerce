@extends('layouts.front-layout')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom text-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff"
                             alt="user" class="rounded-circle mb-2" width="60">
                        <h6 class="mb-0 fw-bold">{{ auth()->user()->name }}</h6>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('account.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('account.index') ? 'active' : '' }}">
                            <i class="ti ti-user me-2"></i> Mi Perfil y Datos
                        </a>
                        <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action {{ request()->routeIs('account.orders') ? 'active' : '' }}">
                            <i class="ti ti-shopping-cart me-2"></i> Mis Pedidos
                        </a>
                        <a href="{{ route('custom-order.create') }}" class="list-group-item list-group-item-action text-primary fw-bold">
                            <i class="ti ti-wand me-2"></i> Solicitar Pedido Personalizado
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-grid p-2 border-top">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="ti ti-logout me-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i> {{ session('error') }}
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('account_content')
        </div>
    </div>
</div>
@endsection
