@extends('layouts.front-layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 text-white">Resumen de Pago</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Orden #{{ $order->id }}</h5>
                    <p class="text-muted">Tipo: {{ $order->type === 'custom' ? 'Pedido Personalizado' : 'Orden Estándar' }}</p>
                    
                    <div class="table-responsive my-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Concepto</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total de la Orden</td>
                                    <td class="text-end fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        Estás a punto de proceder al pago de tu pedido. Serás redirigido a la plataforma segura de PayPhone.
                    </div>

                    <form action="{{ route('checkout.pay_existing', $order->id) }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="ti ti-credit-card me-2"></i> Pagar ${{ number_format($order->total_amount, 2) }}
                            </button>
                            <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary">
                                Cancelar y Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
