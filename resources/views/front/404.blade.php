@extends('layouts.front-layout')

@section('title', 'Página No Encontrada | EliCrochet')

@section('content')
<div class="maintenance-block py-5" style="background: linear-gradient(135deg, #fdf6f9 0%, #fcecf4 100%); min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-lg text-center p-5 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="card-body">
                        <div class="error-image-block mb-4">
                            <!-- Placeholder visual friendly -->
                            <i class="ti ti-ghost text-primary" style="font-size: 8rem; opacity: 0.2;"></i>
                        </div>
                        <h1 class="display-1 fw-bold text-primary mb-3">404</h1>
                        <h3 class="mb-3 fw-bold">¡Uy! Página No Encontrada</h3>
                        <p class="text-muted mb-4">
                            Parece que el hilo se nos ha enredado. La página que buscas no existe, ha sido movida o nunca existió.
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-primary shadow-lg px-4 py-2">
                            <i class="ti ti-home me-2"></i> Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
