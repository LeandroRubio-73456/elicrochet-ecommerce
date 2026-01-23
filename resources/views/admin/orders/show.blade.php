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
        <!-- Detalles de Personalización (Moved to Top) -->
        @if($order->type === 'custom' || $order->items->whereNotNull('custom_order_id')->isNotEmpty())
        <div class="card mb-4 border-primary border-opacity-25 shadow-sm">
            <div class="card-header bg-light-primary">
                <h5 class="card-title mb-0 text-primary"><i class="ti ti-wand me-2"></i>Solicitud de Personalización</h5>
            </div>
            <div class="card-body">
                 @foreach($order->items->whereNotNull('custom_description') as $item)
                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6 class="fw-bold text-dark"><i class="ti ti-file-description me-1"></i>Descripción del Pedido Personalizado #{{ $item->custom_order_id ?? $item->id }}</h6>
                                <div class="p-3 bg-light rounded border">
                                    <p class="mb-0 text-break" style="white-space: pre-line;">{{ $item->custom_description }}</p>
                                </div>
                            </div>
                            
                            @if(!empty($item->custom_specs))
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-dark"><i class="ti ti-ruler me-1"></i>Especificaciones Técnicas</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                        @foreach($item->custom_specs as $key => $val)
                                            <tr>
                                                <td class="text-muted w-50 py-1 ps-0"><i class="ti ti-point me-1 f-10"></i>{{ ucfirst($key) }}:</td>
                                                <td class="fw-medium py-1">{{ $val }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            @if(!empty($item->images))
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-dark"><i class="ti ti-photo me-1"></i>Imágenes de Referencia</h6>
                                <div class="d-flex flex-wrap gap-2 p-2 bg-light rounded border">
                                    @foreach($item->images as $img)
                                         <a href="{{ asset('storage/' . $img) }}" target="_blank" class="d-block" data-bs-toggle="tooltip" title="Ver imagen completa">
                                            <img src="{{ asset('storage/' . $img) }}" 
                                                 alt="Referencia" 
                                                 class="rounded border shadow-sm" 
                                                 width="80" height="80" 
                                                 style="object-fit: cover;">
                                         </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if(!$loop->last) <hr class="my-4"> @endif
                 @endforeach
            </div>
        </div>
        @endif

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
                                                    <span class="text-primary fw-bold">Pedido Personalizado #{{ $item->custom_order_id ?? $order->id }}</span>
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
                                <td colspan="3" class="text-end">Subtotal</td>
                                <td class="text-end">${{ number_format($order->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Envío (Servientrega)</td>
                                <td class="text-end">${{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary fs-5">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
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

            @if(in_array($order->status, ['paid', 'working', 'ready_to_ship']))
                <div class="px-3 pt-3">
                    <a href="{{ route('admin.orders.label', $order) }}" target="_blank" class="btn btn-outline-dark w-100">
                        <i class="ti ti-printer me-2"></i> Generar Etiqueta de Envío
                    </a>
                </div>
            @endif
            
            @if($order->status === 'linked' && $order->parentItem)
                <div class="alert alert-primary m-3 mb-0">
                    <i class="ti ti-link me-1"></i> Esta orden está enlazada a la <strong>Orden Principal #{{ $order->parentItem->order_id }}</strong>. 
                    <a href="{{ route('admin.orders.show', $order->parentItem->order_id) }}" class="fw-bold">Ver orden principal</a>.
                </div>
            @endif

            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="updateStatusForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Estado Actual</label>
                        <select name="status" id="statusSelect" class="form-select @error('status') is-invalid @enderror">
                            @php
                                $statuses = [
                                    'quotation' => 'En Cotización',
                                    'pending_payment' => 'Pendiente de Pago',
                                    'in_cart' => 'En Carrito',
                                    'paid' => 'Pagado',
                                    'working' => 'En Fabricación',
                                    'ready_to_ship' => 'Listo para Envio',
                                    'shipped' => 'Enviado',
                                    'completed' => 'Completado',
                                    'cancelled' => 'Cancelado',
                                    'linked' => 'Enlazado (Solo Lectura)' // Added linked
                                ];
                                $currentLabel = $statuses[$order->status] ?? ucfirst($order->status);

                                $nextStatuses = [];
                                if ($order->status === 'cancelled' || $order->status === 'completed' || $order->status === 'linked') {
                                    // No updates allowed
                                } else {
                                    if ($order->type === 'custom') {
                                        if ($order->status === 'quotation') {
                                            $nextStatuses['pending_payment'] = 'Pendiente de Pago';
                                            $nextStatuses['cancelled'] = 'Cancelar Orden';
                                        } elseif ($order->status === 'in_cart') { // Handle in_cart
                                             $nextStatuses['pending_payment'] = 'Devolver a Pendiente Pago';
                                             $nextStatuses['paid'] = 'Pagado (Manual)';
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

                    {{-- Shipping Guide Upload --}}
                    <div class="mb-3" id="shippingGuideContainer" style="display: none;">
                        <label for="shipping_guide" class="form-label fw-bold text-dark">Subir Guía de Envío (Servientrega)</label>
                        <input type="file" name="shipping_guide" id="shipping_guide" class="form-control" accept="image/*">
                        <small class="text-muted">Sube una foto del comprobante de envío.</small>
                    </div>

                    @if($order->shipping_guide)
                        <div class="mb-3 p-2 border rounded bg-light">
                            <label class="form-label small text-muted text-uppercase fw-bold">Guía de Envío Actual:</label>
                            <a href="{{ asset('storage/' . $order->shipping_guide) }}" target="_blank" class="d-block">
                                <img src="{{ asset('storage/' . $order->shipping_guide) }}" alt="Guía de Envío" class="img-fluid rounded border shadow-sm" style="max-height: 150px;">
                            </a>
                        </div>
                    @endif

                    @if($order->type === 'custom' && $order->status === 'quotation')
                        <div class="mb-3">
                            <label for="total_amount_input" class="form-label fw-bold text-primary">Cotizar Valor Total ($)</label>
                            <input type="number" step="0.01" name="total_amount" id="total_amount_input" class="form-control" value="{{ $order->total_amount > 0 ? $order->total_amount : '' }}">
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

        <!-- Información de Envío -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de Envío</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block uppercase fw-bold">Dirección</small>
                    <span class="fs-6">{{ $order->address->street ?? $order->shipping_address ?? 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block uppercase fw-bold">Ciudad / Provincia</small>
                    <span class="fs-6">{{ $order->address->city ?? $order->shipping_city ?? 'N/A' }} / {{ $order->address->province ?? $order->shipping_province ?? 'N/A' }}</span>
                </div>
                 <div class="mb-3">
                    <small class="text-muted d-block uppercase fw-bold">Código Postal</small>
                    <span class="fs-6">{{ $order->address->postal_code ?? $order->shipping_zip ?? 'N/A' }}</span>
                </div>
                @if($order->shipping_cost > 0)
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Costo de Envío:</span>
                        <span class="badge bg-light-primary text-primary fs-6">${{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                </div>
                @endif
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
        const shippingGuideContainer = document.getElementById('shippingGuideContainer');
        const currentStatus = "{{ $order->status }}";

        // Logic to toggle shipping guide input
        function toggleShippingGuide() {
            if (statusSelect.value === 'shipped') {
                shippingGuideContainer.style.display = 'block';
            } else {
                shippingGuideContainer.style.display = 'none';
            }
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', toggleShippingGuide);
            // Run on load just in case (though default is hidden)
            toggleShippingGuide(); 
        }

        if (updateStatusForm) {
            updateStatusForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const selectedStatus = statusSelect.value;
                
                if (selectedStatus === currentStatus) {
                    // If no change, check if file is selected (for re-uploading)
                    const fileInput = document.getElementById('shipping_guide');
                    if (fileInput && fileInput.files.length > 0) {
                        this.submit();
                        return;
                    }
                    
                    // Otherwise do nothing or submit
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
