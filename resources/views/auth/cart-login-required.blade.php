@extends('layouts.front-layout')

@section('title', 'Inicia sesión | EliCrochet')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="ti ti-shopping-cart text-muted mb-4" style="font-size: 4rem;"></i>
                    <h3 class="mb-3">¡Inicia sesión para continuar!</h3>
                    <p class="text-muted mb-4">
                        Para agregar productos a tu carrito y disfrutar de todas las ventajas, 
                        necesitas tener una cuenta en EliCrochet.
                    </p>
                    <div class="d-grid gap-3 col-md-8 mx-auto">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="ti ti-login me-2"></i> Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                            <i class="ti ti-user-plus me-2"></i> Crear Cuenta
                        </a>
                        <a href="{{ route('shop') }}" class="btn btn-link text-muted">
                            Seguir explorando productos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
