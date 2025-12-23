@extends('customer.layout')

@section('title', 'Dashboard Cliente | EliCrochet')

@section('customer_content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0 bg-light-primary">
            <div class="card-body">
                <h4 class="fw-bold mb-1">¡Hola, {{ $user->name }}! 👋</h4>
                <p class="mb-0 text-muted">Bienvenido a tu panel de cliente. Aquí puedes gestionar tus pedidos y datos.</p>
            </div>
        </div>
    </div>

    <!-- Stats or Quick Actions -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-bold f-12">Pedidos Recientes</h6>
                @if($recentOrders->count() > 0)
                    <div class="list-group list-group-flush mt-3">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('customer.orders.show', $order) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="fw-bold">#{{ $order->id }}</span>
                                    <small class="text-muted d-block">{{ $order->created_at->format('d/m/Y') }}</small>
                                </div>
                                @php
                                    $badgeClass = match($order->status) {
                                        'paid', 'completed', 'shipped' => 'bg-success',
                                        'pending_payment', 'quotation' => 'bg-warning text-dark',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    
                                    $statusLabel = match($order->status) {
                                        'quotation' => 'Cotización',
                                        'pending_payment' => 'Pendiente Pago',
                                        'paid' => 'Pagado',
                                        'working' => 'Fabricando',
                                        'ready_to_ship' => 'Listo para Envio',
                                        'shipped' => 'Enviado',
                                        'completed' => 'Completado',
                                        'cancelled' => 'Cancelado',
                                        default => $order->status
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-shopping-cart-off fs-1 text-muted opacity-50 mb-2"></i>
                        <p class="text-muted">No tienes pedidos recientes.</p>
                        <a href="{{ route('shop') }}" class="btn btn-sm btn-outline-primary">Ir a la Tienda</a>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white border-top-0 text-end">
                <a href="{{ route('customer.orders.index') }}" class="text-decoration-none f-14 fw-bold">Ver todos los pedidos <i class="ti ti-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-bold f-12">Datos de Envío</h6>
                <div class="mt-3">
                    @php
                        $address = $user->addresses->first();
                    @endphp
                    @if($address)
                        <p class="mb-1 fw-bold">{{ $address->reference ? '(' . $address->reference . ') ' : '' }}{{ $address->street }}</p>
                        <p class="mb-1">{{ $address->city }} {{ $address->province ? ', ' . $address->province : '' }}</p>
                        <p class="mb-1 text-muted small"><strong>CP:</strong> {{ $address->postal_code }}</p>
                        <p class="mb-0 text-muted"><i class="ti ti-phone me-1"></i> {{ $address->phone ?? $user->phone ?? 'Sin teléfono registrado' }}</p>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-2">No has configurado tu dirección de envío.</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 text-end">
                <a href="{{ route('customer.profile.edit') }}" class="btn btn-outline-primary btn-sm">Editar Datos</a>
            </div>
        </div>
    </div>
</div>
@endsection
