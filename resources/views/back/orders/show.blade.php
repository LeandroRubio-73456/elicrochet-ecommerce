@extends('layouts.back-layout')

@section('title', 'Detalle de Orden #' . $order->order_number)

@section('content')
@include('layouts.breadcrumb', ['item' => 'Órdenes', 'active' => 'Detalle de Orden'])

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <!-- Detalles de Productos -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Productos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product && $item->product->images->first())
                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                        @else
                                            <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ti ti-star text-warning"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">
                                                @if($item->product)
                                                    {{ $item->product->name }}
                                                @elseif($item->custom_order_id || $order->type === 'custom')
                                                    {{-- Fallback: If it's a custom order type and no product, it's the custom item --}}
                                                    Pedido Personalizado #{{ $order->id }}
                                                @else
                                                    Producto Eliminado
                                                @endif
                                            </h6>
                                            @if($item->custom_order_id || $order->type === 'custom')
                                                <small class="text-muted">Servicio de Fabricación</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                <td class="text-end fw-bold">${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary fs-5">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información de Envío -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de Envío</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block uppercase font-weight-bold">Dirección</small>
                        <span class="fs-6">{{ $order->address->street ?? $order->shipping_address ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block uppercase font-weight-bold">Ciudad / Provincia</small>
                        <span class="fs-6">{{ $order->address->city ?? $order->shipping_city ?? 'N/A' }}, {{ $order->address->province ?? $order->shipping_province ?? 'N/A' }}</span>
                    </div>
                     <div class="col-md-6 mb-3">
                        <small class="text-muted d-block uppercase font-weight-bold">Código Postal</small>
                        <span class="fs-6">{{ $order->address->postal_code ?? $order->shipping_zip ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Acciones / Estado -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Estado de la Orden</h5>
                @if($order->type === 'custom')
                    <span class="badge bg-light-info text-info border border-info">Personalizado</span>
                @elseif($order->type === 'catalog')
                    <span class="badge bg-light-dark text-dark border">Catálogo</span>
                @else
                    <span class="badge bg-light-primary text-primary border border-primary">Stock</span>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="updateStatusForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Estado Actual</label>
                        <select name="status" id="statusSelect" class="form-select @error('status') is-invalid @enderror">
                            @php
                                $statuses = [
                                    'quotation' => 'En Cotización',
                                    'pending_payment' => 'Pendiente de Pago',
                                    'paid' => 'Pagado',
                                    'working' => 'En Fabricación',
                                    'ready_to_ship' => 'Listo para Envio',
                                    'shipped' => 'Enviado',
                                    'completed' => 'Completado',
                                    'cancelled' => 'Cancelado'
                                ];
                                $currentLabel = $statuses[$order->status] ?? ucfirst($order->status);

                                $nextStatuses = [];
                                if ($order->status === 'cancelled' || $order->status === 'completed') {
                                    // No updates allowed
                                } else {
                                    if ($order->type === 'custom') {
                                        if ($order->status === 'quotation') {
                                            $nextStatuses['pending_payment'] = 'Pendiente de Pago';
                                            $nextStatuses['cancelled'] = 'Cancelar Orden';
                                        } elseif ($order->status === 'pending_payment') {
                                            $nextStatuses['paid'] = 'Pagado (Manual)';
                                            $nextStatuses['cancelled'] = 'Cancelar Orden';
                                        } elseif ($order->status === 'paid') {
                                            $nextStatuses['working'] = 'En Fabricación';
                                            $nextStatuses['cancelled'] = 'Cancelar Orden';
                                        } elseif ($order->status === 'working') {
                                            $nextStatuses['shipped'] = 'Enviado';
                                        } elseif ($order->status === 'ready_to_ship') {
                                            $nextStatuses['shipped'] = 'Enviado';
                                        } elseif ($order->status === 'shipped') {
                                            $nextStatuses['completed'] = 'Completado';
                                        }
                                    } else {
                                        // Standard/Catalog Logic
                                        if ($order->status === 'pending_payment') {
                                            $nextStatuses['paid'] = 'Pagado (Manual)';
                                            $nextStatuses['cancelled'] = 'Cancelar Orden';
                                        } elseif ($order->status === 'paid') {
                                            $nextStatuses['working'] = 'En Fabricación / Proceso';
                                            $nextStatuses['shipped'] = 'Enviado';
                                            $nextStatuses['cancelled'] = 'Cancelar Orden';
                                        } elseif ($order->status === 'working') {
                                            $nextStatuses['shipped'] = 'Enviado';
                                        } elseif ($order->status === 'ready_to_ship') {
                                            $nextStatuses['shipped'] = 'Enviado';
                                        } elseif ($order->status === 'shipped') {
                                            $nextStatuses['completed'] = 'Completado';
                                        }
                                    }
                                }
                            @endphp

                            <option value="{{ $order->status }}" selected>{{ $currentLabel }}</option>
                            @foreach($nextStatuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                         @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($order->type === 'custom' && $order->status === 'quotation')
                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Cotizar Valor Total ($)</label>
                            <input type="number" step="0.01" name="total_amount" class="form-control" value="{{ $order->total_amount > 0 ? $order->total_amount : '' }}" placeholder="0.00">
                            <small class="text-muted">Al cambiar a 'Pendiente de Pago', este será el valor a cobrar.</small>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary w-100" id="btnUpdateStatus">Actualizar Estado</button>
                </form>

                <hr>

                <div class="mt-3">
                    <small class="text-muted d-block">ID de Transacción PayPhone:</small>
                    <span class="field-value text-break">{{ $order->payphone_transaction_id ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        
        @if($order->type === 'custom')
        <div class="card mb-4">
             <div class="card-header">
                <h5 class="card-title mb-0">Detalles Personalización</h5>
            </div>
            <div class="card-body">
                 @php
                    // Assuming the first item has the description for the whole order in current logic,
                    // or listing all items' descriptions.
                    // Ideally custom orders have 1 main item or multiple.
                 @endphp
                 @foreach($order->items as $item)
                    <div class="mb-3 border-bottom pb-2">
                        <p class="fw-bold mb-1">Descripción Item #{{ $loop->iteration }}</p>
                        <p class="text-muted f-14">{{ $item->custom_description ?? 'Sin descripción' }}</p>
                        
                        @if(!empty($item->custom_specs))
                            <div class="mt-2 text-muted f-13">
                                <strong class="d-block mb-1 text-primary">Especificaciones:</strong>
                                <ul class="mb-0 ps-3">
                                @foreach($item->custom_specs as $key => $val)
                                    <li><strong>{{ ucfirst($key) }}:</strong> {{ $val }}</li>
                                @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($item->images))
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($item->images as $img)
                                     <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Solicitud de Personalización" class="rounded border" width="60" height="60" style="object-fit: cover;">
                                     </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                 @endforeach
            </div>
        </div>
        @endif

        <!-- Información del Cliente -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Cliente</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avtar avtar-s bg-light-primary text-primary rounded-circle me-3">
                        <i class="ti ti-user fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $order->customer_name }}</h6>
                        <small class="text-muted">Registrado</small>
                    </div>
                </div>
                <div class="mb-2">
                    <i class="ti ti-mail me-2 text-muted"></i> {{ $order->customer_email }}
                </div>
                 <div class="mb-2">
                    <i class="ti ti-phone me-2 text-muted"></i> {{ $order->customer_phone }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/libs/sweetalert2.all.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateStatusForm = document.getElementById('updateStatusForm');
        const statusSelect = document.getElementById('statusSelect');
        const currentStatus = "{{ $order->status }}";

        if (updateStatusForm) {
            updateStatusForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const selectedStatus = statusSelect.value;
                
                if (selectedStatus === currentStatus) {
                    // If no change, just submit or do nothing
                    this.submit();
                    return;
                }

                let warningText = "El estado del pedido cambiará y se notificará al cliente.";
                let warningTitle = "¿Estás seguro?";
                let icon = "warning";

                // Specific warnings
                if (selectedStatus === 'pending_payment') {
                    warningText = "¡Atención! Al pasar a 'Pendiente de Pago', el precio del pedido personalizado se fijará y no podrás editarlo fácilmente después. ¿Confirmas el valor?";
                    icon = "info";
                } else if (selectedStatus === 'cancelled') {
                    warningText = "Esta acción cancelará el pedido irreversiblemente. ¿Deseas continuar?";
                    icon = "error";
                }

                Swal.fire({
                    title: warningTitle,
                    text: warningText,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cambiar estado',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateStatusForm.submit();
                    }
                });
            });
        }
    });
</script>
@endpush
