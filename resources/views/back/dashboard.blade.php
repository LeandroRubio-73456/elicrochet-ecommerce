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
                    <h4 class="mb-0">{{ $totalUsers }} <i class="ti-user text-primary float-end opacity-50"></i></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Total Órdenes</h6>
                    <h4 class="mb-0">{{ $totalOrders }} <i class="ti-shopping-cart text-warning float-end opacity-50"></i></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Ventas Totales</h6>
                    <h4 class="mb-0">${{ number_format($totalSales, 2, ',', '.') }} <i class="ti-currency-dollar text-success float-end opacity-50"></i></h4>
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
                                                 'paid', 'completed', 'shipped' => 'ti-check',
                                                 'pending_payment', 'quotation' => 'ti-clock',
                                                 'cancelled' => 'ti-x',
                                                 default => 'ti-info-circle'
                                            };
                                        @endphp
                                        <span class="d-block"><i class="ti {{ $icon }} {{ $badgeClass }} f-10 m-r-5"></i> {{ ucfirst($order->status) }}</span>
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
                        <i class="ti ti-check-circle text-success fs-1 mb-2"></i>
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

    </div>
@endsection
