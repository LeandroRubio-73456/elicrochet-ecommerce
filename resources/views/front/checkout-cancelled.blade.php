@extends('layouts.front-layout')

@section('content')
<div class="container py-5 text-center">
    <div class="card border-0 shadow-sm py-5">
        <div class="card-body">
            <i class="ti ti-x-circle text-danger" style="font-size: 4rem;"></i>
            <h1 class="mt-4">Pago Cancelado</h1>
            <p class="lead">El proceso de pago ha sido cancelado o interrumpido.</p>
            <p>No se ha realizado ningún cargo a tu cuenta. Puedes intentar realizar el pago nuevamente.</p>
            
            <div class="mt-5">
                <a href="{{ route('cart') }}" class="btn btn-primary">Volver al carrito</a>
                <a href="{{ route('home') }}" class="btn btn-outline-primary ms-2">Ir al inicio</a>
            </div>
        </div>
    </div>
</div>
@endsection@extends('layouts.front-layout')

@section('content')
<div class="container py-5 text-center">
    <div class="card border-0 shadow-sm py-5">
        <div class="card-body">
            <i class="ti ti-circle-check text-success" style="font-size: 4rem;"></i>
            <h1 class="mt-4">¡Pago Completado!</h1>
            <p class="lead">Gracias por tu compra en EliCrochet.</p>
            <p>Tu orden #{{ $order->id }} ha sido procesada exitosamente.</p>
            
            <div class="mt-5">
                <a href="{{ route('home') }}" class="btn btn-primary">Volver a la tienda</a>
                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary ms-2">Ver detalle de mi orden</a>
            </div>
        </div>
    </div>
</div>
@endsection