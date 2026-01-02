@extends('layouts.front-layout')

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
                <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-primary ms-2">Ver detalle de mi orden</a>
            </div>
        </div>
    </div>
</div>
@endsection
