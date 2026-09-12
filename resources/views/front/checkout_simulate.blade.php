@extends('layouts.front-layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="alert alert-warning text-center mb-4">
                <strong>Modo simulación.</strong> Esta pantalla reemplaza a la pasarela real de PayPhone
                porque el proyecto no tiene credenciales de comercio configuradas. No se realizará ningún cargo.
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 text-white">Pasarela de pago (simulada)</h4>
                </div>

                <div class="card-body text-center py-4">
                    @if ($order)
                        <p class="text-muted mb-1">Orden #{{ $order->id }}</p>
                        <p class="text-muted mb-1">{{ $order->customer_name }}</p>
                    @endif

                    <h2 class="my-4">${{ number_format($amount, 2) }}</h2>

                    <p class="text-muted small">Referencia de transacción: {{ $transactionId }}</p>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a
                            href="{{ route('checkout.callback', ['id' => $fakePaymentId, 'clientTransactionId' => $transactionId, 'simulated_status' => 'Approved']) }}"
                            class="btn btn-success btn-lg"
                        >
                            Aprobar pago
                        </a>

                        <a
                            href="{{ route('checkout.callback', ['id' => $fakePaymentId, 'clientTransactionId' => $transactionId, 'simulated_status' => 'Declined']) }}"
                            class="btn btn-outline-danger btn-lg"
                        >
                            Rechazar pago
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
