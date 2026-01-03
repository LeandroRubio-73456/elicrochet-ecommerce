@extends('layouts.back-layout')
@section('title', 'Dashboard')

@section('content')
    @include('layouts.breadcrumb', ['item' => 'Home', 'active' => 'Dashboard'])

    <div class="row">
        <!-- KPIs -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Usuarios Totales</h6>
                    <h4 class="mb-0">{{ $totalUsers }} <i class="ti ti-user text-primary float-end opacity-50"></i></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Total Órdenes</h6>
                    <h4 class="mb-0">{{ $totalOrders }} <i class="ti ti-shopping-cart text-warning float-end opacity-50"></i></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Ventas Totales</h6>
                    <h4 class="mb-0">${{ number_format($totalSales, 2, ',', '.') }} <i class="ti ti-currency-dollar text-success float-end opacity-50"></i></h4>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-md-12 col-xl-8">
            <h5 class="mb-3">Órdenes Recientes</h5>
            <div class="card tbl-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>ORDEN #</th>
                                    <th>CLIENTE</th>
                                    <th>ESTADO</th>
                                    <th class="text-end">TOTAL</th>
                                    <th class="text-end">ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}" class="text-muted fw-bold">#{{ $order->id }}</a></td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($order->status) {
                                                'paid', 'completed', 'shipped' => 'text-success',
                                                'pending_payment', 'quotation' => 'text-warning',
                                                'cancelled' => 'text-danger',
                                                default => 'text-primary'
                                            };
                                            $icon = match($order->status) {
                                                 'paid', 'completed', 'shipped' => 'check',
                                                 'pending_payment', 'quotation' => 'clock',
                                                 'cancelled' => 'x',
                                                 default => 'info-circle'
                                            };
                                        @endphp
                                        <span class="d-block"><i class="ti ti-{{ $icon }} {{ $badgeClass }} f-10 m-r-5"></i> {{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td class="text-end">${{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-light-primary"><i class="ti ti-eye"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay órdenes recientes.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-md-12 col-xl-4">
            <h5 class="mb-3">Alerta de Stock Bajo</h5>
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    @forelse($lowStockProducts as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-s rounded-circle text-danger bg-light-danger">
                                    <i class="ti ti-alert-triangle f-18"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $product->name }}</h6>
                                <p class="mb-0 text-muted f-12">Stock actual: <span class="fw-bold text-danger">{{ $product->stock }}</span></p>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <button class="btn btn-sm btn-icon btn-link-secondary"><i class="ti ti-edit"></i></button>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="p-3 text-center text-muted">
                        <i class="ti ti-check text-success fs-1 mb-2"></i>
                        <p class="mb-0">Todo el inventario está saludable.</p>
                    </div>
                    @endforelse
                </div>
                @if($lowStockProducts->count() > 0)
                <div class="card-footer text-center">
                    <a href="{{ route('admin.products.index') }}" class="text-decoration-none small">Ver Inventario Completo</a>
                </div>
                @endif
            </div>
        </div>


        <!-- Recent Reviews -->
        <div class="col-md-12 mt-4">
            <h5 class="mb-3">Reseñas Recientes</h5>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Usuario</th>
                                    <th>Calificación</th>
                                    <th>Comentario</th>
                                    <th class="text-end">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReviews as $review)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($review->product)
                                                @if($review->product->images->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $review->product->images->first()->image_path) }}" class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="ti ti-photo-off text-muted"></i></div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0 f-14">{{ Str::limit($review->product->name, 30) }}</h6>
                                                    <a href="{{ route('product.show', $review->product->slug) }}" target="_blank" class="text-muted f-12"><i class="ti ti-external-link"></i> Ver</a>
                                                </div>
                                            @else
                                                <span class="text-muted">Producto Eliminado</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avtar avtar-xs rounded-circle bg-light-primary text-primary me-2">
                                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                            </div>
                                            {{ $review->user->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="ti ti-star{{ $i <= $review->rating ? '-filled' : '' }} f-12"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td>
                                        @if($review->title)
                                            <div class="fw-bold text-dark mb-1">{{ Str::limit($review->title, 20) }}</div>
                                        @endif
                                        <p class="mb-0 text-muted f-12 text-truncate" style="max-width: 250px;">{{ $review->comment }}</p>
                                    </td>
                                    <td class="text-end text-muted f-12">{{ $review->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-message-off fs-2 mb-2 d-block opacity-50"></i>
                                            No hay reseñas recientes.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
