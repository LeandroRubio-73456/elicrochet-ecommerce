@extends('customer.layout')

@section('title', 'Mis Pedidos | EliCrochet')

@section('customer_content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="ti ti-list me-2 text-primary"></i>Historial de Pedidos</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Pedido #</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($order->type === 'custom')
                                    <span class="badge bg-light-info text-info border border-info">Personalizado</span>
                                @elseif($order->type === 'catalog')
                                    <span class="badge bg-light-dark text-dark border">Catálogo</span>
                                @else
                                    <span class="badge bg-light-primary text-primary border border-primary">Stock</span>
                                @endif
                            </td>
                            <td class="fw-bold">
                                @if($order->type === 'custom' && $order->total_amount == 0)
                                    <span class="text-muted f-12">Por Cotizar</span>
                                @else
                                    ${{ number_format($order->total_amount, 2) }}
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($order->status) {
                                        'paid', 'completed', 'shipped' => 'bg-light-success text-success',
                                        'pending_payment', 'quotation' => 'bg-light-warning text-warning',
                                        'working', 'processing' => 'bg-light-info text-info',
                                        'cancelled' => 'bg-light-danger text-danger',
                                        'ready_to_ship' => 'bg-light-primary text-primary',
                                        default => 'bg-light-secondary text-secondary'
                                    };
                                    
                                    $statusLabel = match($order->status) {
                                        'quotation' => 'En Cotización',
                                        'pending_payment' => 'Pendiente Pago',
                                        'paid' => 'Pagado',
                                        'working', 'processing' => 'En Fabricación',
                                        'ready_to_ship' => 'Listo para Envio',
                                        'shipped' => 'Enviado',
                                        'completed' => 'Completado',
                                        'cancelled' => 'Cancelado',
                                        default => ucwords(str_replace('_', ' ', $order->status))
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="opacity-50 mb-2">
                                    <i class="ti ti-shopping-cart-off fs-1"></i>
                                </div>
                                <p>No tienes pedidos registrados.</p>
                                <a href="{{ route('shop') }}" class="btn btn-primary btn-sm">Ir a Comprar</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end p-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
