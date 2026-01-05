@extends('layouts.back-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Reporte Financiero')

@section('content')

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
           <h3 class="fw-bold mb-0">Reporte de Ventas</h3>
           <p class="text-muted">Resumen de ingresos y rendimiento.</p>
        </div>
        <div>
            <a href="{{ route('admin.finance.export') }}" class="btn btn-success">
                <i class="ti ti-file-export me-1"></i> Exportar a Excel (CSV)
            </a>
        </div>
    </div>
</div>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <!-- Total Income -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 bg-light-success p-2 rounded">
                        <i class="ti ti-currency-dollar text-success fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted fw-bold mb-0 small">Ingresos Totales</h6>
                    </div>
                </div>
                <h2 class="fw-bold mb-0">${{ number_format($totalIncome, 2, ',', '.') }}</h2>
                <small class="text-success"><i class="ti ti-trending-up"></i> Histórico acumulado</small>
            </div>
        </div>
    </div>

    <!-- Orders Last 30 Days -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 bg-light-primary p-2 rounded">
                        <i class="ti ti-shopping-cart text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted fw-bold mb-0 small">Pedidos (30 días)</h6>
                    </div>
                </div>
                <h2 class="fw-bold mb-0">{{ $ordersLast30Days }}</h2>
                <small class="text-muted">Órdenes recientes</small>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title fw-bold mb-0">Ventas Mensuales (Últimos 6 Meses)</h5>
    </div>
    <div class="card-body">
        <canvas id="salesChart" style="height: 300px; width: 100%;"></canvas>
    </div>
</div>



<!-- Sales History Table -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title fw-bold mb-0">Detalle de Ventas Completadas</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesHistory as $sale)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $sale->id }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y') }}<br><small class="text-muted">{{ $sale->created_at->format('H:i') }}</small></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avtar avtar-s bg-light-primary text-primary rounded-circle me-3">
                                        {{ strtoupper(substr($sale->customer_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $sale->customer_name }}</h6>
                                        <small class="text-muted text-truncate d-block" style="max-width: 150px;">{{ $sale->customer_email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($sale->status == 'paid')
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($sale->status == 'shipped')
                                    <span class="badge bg-info">Enviado</span>
                                @elseif($sale->status == 'completed')
                                    <span class="badge bg-primary">Completado</span>
                                @elseif($sale->status == 'delivered')
                                    <span class="badge bg-dark">Entregado</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($sale->status) }}</span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">${{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $sale->id) }}" class="btn btn-sm btn-icon btn-light-secondary" data-bs-toggle="tooltip" title="Ver Detalles">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="ti ti-shopping-cart-off fs-1 text-muted mb-3 d-block"></i>
                                    <h6 class="text-muted">No hay ventas registradas aún.</h6>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-end">
        {{ $salesHistory->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/libs/chart.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('salesChart').getContext('2d');
        
        var chartLabels = @json($chartLabels);
        var chartValues = @json($chartValues);

        var salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Ventas ($)',
                    data: chartValues,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4],
                            color: '#f0f0f0'
                        },
                         ticks: {
                            callback: function(value, index, values) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
