@extends('customer.layout')

@section('title', 'Detalle de Pedido #' . $order->order_number)

@section('customer_content')
<div class="row">
    <div class="col-12 mb-3">
        <a href="{{ route('customer.orders.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
            <i class="ti ti-arrow-left me-1"></i> Volver a mis pedidos
        </a>
        <div class="d-flex justify-content-between align-items-center">
            <h3>Pedido #{{ $order->order_number }}</h3>
            <!-- Actions -->
            <div class="d-flex gap-2">
                @if($order->canTransitionTo('cancelled'))
                    <form action="{{ route('customer.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar este pedido?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="ti ti-x me-1"></i> Cancelar Pedido
                        </button>
                    </form>
                @endif

                @if($order->canTransitionTo('completed'))
                    <form action="{{ route('customer.orders.confirm', $order) }}" method="POST" onsubmit="return confirm('¿Confirmas que has recibido el pedido satisfactoriamente?');">
                        @csrf
                        <button type="submit" class="btn btn-success text-white">
                            <i class="ti ti-check me-1"></i> Confirmar Recepción
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Info -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Items del Pedido</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end pe-4">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4 py-3">
                                        {{-- LOGIC: Verify if it's a Stock Product or Custom Item --}}
                                        @if($item->product)
                                            <div class="d-flex align-items-center">
                                                @if($item->product->images->count() > 0)
                                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                         class="rounded me-3 border" width="50" height="50" style="object-fit: cover;" alt="{{ $item->product->name }}">
                                                @else
                                                     <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center border" style="width:50px; height:50px;">
                                                         <i class="ti ti-photo-off text-muted"></i>
                                                     </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                    @if($item->product->category)
                                                        <small class="text-muted">{{ $item->product->category->name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            {{-- Custom Order Item (No Product) --}}
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-primary">Pedido Personalizado</span>
                                                <small class="text-muted">{{ Str::limit($item->custom_description ?? 'Servicio de Fabricación', 100) }}</small>
                                                
                                                @if(!empty($item->custom_specs))
                                                    <div class="mt-2 text-muted f-13 small">
                                                        <ul class="mb-0 ps-3">
                                                        @foreach($item->custom_specs as $key => $val)
                                                            <li><strong class="text-secondary">{{ ucfirst($key) }}:</strong> {{ $val }}</li>
                                                        @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

                                                @if($item->images)
                                                    <div class="mt-2 text-primary f-12">
                                                        <i class="ti ti-photo"></i> Imágenes adjuntas
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end pe-4 fw-bold">
                                        @if($item->price == 0 && $order->total_amount == 0)
                                            --
                                        @else
                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="2" class="text-end fw-bold pt-3">Total:</td>
                                <td class="text-end fw-bold pe-4 pt-3 fs-5">
                                    @if($order->total_amount == 0 && $order->status === 'quotation')
                                        <span class="badge bg-warning text-dark">Pendiente Cotización</span>
                                    @else
                                        ${{ number_format($order->total_amount, 2) }}
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="text-uppercase text-muted fw-bold f-12 mb-3">Estado del Pedido</h6>
                @php
                    $badgeClass = match($order->status) {
                        'paid', 'completed', 'shipped' => 'bg-success',
                        'pending_payment', 'quotation' => 'bg-warning text-dark',
                        'working' => 'bg-info',
                        'cancelled' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    $statusLabel = match($order->status) {
                        'quotation' => 'En Cotización',
                        'pending_payment' => 'Pendiente de Pago',
                        'paid' => 'Pagado',
                        'working' => 'En Fabricación',
                        'ready_to_ship' => 'Listo para Envio',
                        'shipped' => 'Enviado',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                        default => $order->status
                    };
                    
                    $statusDesc = match($order->status) {
                        'quotation' => 'Estamos calculando el costo de tu pedido personalizado.',
                        'pending_payment' => 'Tu pedido ha sido creado/cotizado. Realiza el pago para continuar.',
                        'paid' => 'Hemos recibido tu pago. Pronto iniciaremos el proceso.',
                        'working' => 'Estamos trabajando en tu pedido (Corte y Confección).',
                        'ready_to_ship' => 'Tu pedido está listo y empacado.',
                        'shipped' => 'Tu pedido ha sido enviado a la dirección proporcionada.',
                        'completed' => 'Has confirmado la recepción. ¡Gracias!',
                        'cancelled' => 'Este pedido ha sido cancelado.',
                        default => ''
                    };
                @endphp
                <div class="text-center py-3">
                    <span class="badge {{ $badgeClass }} fs-6 mb-3 px-3 py-2">{{ $statusLabel }}</span>
                    <p class="text-muted small mb-0">{{ $statusDesc }}</p>
                </div>

                @if($order->status === 'pending_payment')
                     <div class="d-grid mt-3">
                         <form action="{{ route('customer.orders.add_to_cart', $order) }}" method="POST">
                             @csrf
                             <button type="submit" class="btn btn-primary w-100">
                                 <i class="ti ti-shopping-cart-plus me-2"></i> Agregar al Carrito para Pagar
                             </button>
                         </form>
                     </div>
                @endif
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-uppercase text-muted fw-bold f-12 mb-3">Dirección de Envío</h6>
                <p class="mb-1 fw-bold">{{ $order->customer_name }}</p>
                <p class="mb-1">{{ $order->shipping_address }}</p>
                <p class="mb-1">{{ $order->shipping_city }} {{ $order->shipping_zip ? '- ' . $order->shipping_zip : '' }}</p>
                <p class="mb-0 text-muted"><i class="ti ti-phone me-1"></i> {{ $order->customer_phone ?? $order->shipping_phone }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
