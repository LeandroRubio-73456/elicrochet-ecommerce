@extends('layouts.back-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Reporte Financiero')

@section('content')

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
           <h3 class="fw-bold mb-0">Reporte Financiero</h3>
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
    <div class="col-md-4">
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
    <div class="col-md-4">
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

    <!-- Balance / Status -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-uppercase text-muted fw-bold mb-3 small">Balance de Estados</h6>
                <div class="d-flex justify-content-between text-center">
                    <div>
                        <h5 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h5>
                        <small class="text-muted small">Pendientes</small>
                    </div>
                    <div class="vr opacity-10"></div>
                    <div>
                        <h5 class="fw-bold text-info mb-0">{{ $workingCount }}</h5>
                        <small class="text-muted small">En Proceso</small>
                    </div>
                    <div class="vr opacity-10"></div>
                    <div>
                        <h5 class="fw-bold text-success mb-0">{{ $paidCount }}</h5>
                        <small class="text-muted small">Pagados/Completados</small>
                    </div>
                </div>
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
