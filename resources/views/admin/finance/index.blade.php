@extends('layouts.back-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Reportes y Analítica')

@section('content')

    @include('layouts.breadcrumb', ['item' => 'Dashboard', 'active' => 'Reportes y Analítica'])

    <div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="m-0">Reporte de Negocio</h4>
        </div>
    <!-- Actions/Filters -->
    <div class="row mb-4">
        <div class="col-4 text-md-start d-flex align-items-center">
            <div class="d-inline-flex align-items-center bg-white px-3 py-2 rounded shadow-sm border">
                <div class="avtar avtar-xs bg-light-primary text-primary me-2">
                    <i class="ti ti-calendar f-18"></i>
                </div>
                <div>
                    <span class="d-block text-muted f-10 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Periodo Filtrado</span>
                    <span class="fw-bold text-dark">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        <div class="col-8 text-md-end">
            <form action="{{ route('admin.finance.index') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-md-end gap-2">
            
            <!-- Quick Filters -->
            <div class="btn-group" role="group">
                <a href="{{ route('admin.finance.index', ['period' => 'today']) }}" class="btn btn-outline-secondary {{ $period == 'today' ? 'active' : '' }}">Hoy</a>
                <a href="{{ route('admin.finance.index', ['period' => 'last_week']) }}" class="btn btn-outline-secondary {{ $period == 'last_week' ? 'active' : '' }}">Semana</a>
                <a href="{{ route('admin.finance.index', ['period' => 'this_month']) }}" class="btn btn-outline-secondary {{ $period == 'this_month' ? 'active' : '' }}">Mes</a>
                <a href="{{ route('admin.finance.index', ['period' => 'this_year']) }}" class="btn btn-outline-secondary {{ $period == 'this_year' ? 'active' : '' }}">Este Año</a>
            </div>

            <!-- Custom Range Trigger -->
            <div class="dropdown">
                <button class="btn btn-white border dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                   <i class="ti ti-calendar me-1"></i> PERSONALIZADO
                </button>
                <div class="dropdown-menu p-3" aria-labelledby="dropdownMenuButton1" style="min-width: 300px;">
                    <h6 class="dropdown-header">Rango de fechas</h6>
                    <input type="hidden" name="period" value="custom">
                    <div class="mb-2">
                        <label for="date_from" class="form-label small">Desde</label>
                        <input type="date" id="date_from" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="mb-2">
                        <label for="date_to" class="form-label small">Hasta</label>
                        <input type="date" id="date_to" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Aplicar Filtro</button>
                </div>
            </div>

            <!-- Export -->
            <a href="{{ route('admin.finance.export', request()->all()) }}" class="btn btn-success ms-2">
                <i class="ti ti-file-export me-1"></i> Exportar
            </a>
        </form>
    </div>
</div>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <!-- Total Income -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 bg-light-success p-2 rounded">
                        <i class="ti ti-currency-dollar text-success fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted fw-bold mb-0 small">Ingresos Totales</h6>
                    </div>
                    @if($revenueGrowth != 0)
                        <div class="flex-shrink-0">
                            <span class="badge {{ $revenueGrowth > 0 ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                <i class="ti ti-arrow-{{ $revenueGrowth > 0 ? 'up' : 'down' }} me-1"></i>{{ number_format(abs($revenueGrowth), 1) }}%
                            </span>
                        </div>
                    @endif
                </div>
                <h3 class="fw-bold mb-0">${{ number_format($totalIncome, 2, ',', '.') }}</h3>
                <small class="text-muted">vs periodo anterior</small>
            </div>
        </div>
    </div>

    <!-- Average Ticket -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 bg-light-primary p-2 rounded">
                        <i class="ti ti-receipt-2 text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted fw-bold mb-0 small">Ticket Promedio</h6>
                    </div>
                </div>
                <h3 class="fw-bold mb-0">${{ number_format($avgTicket, 2, ',', '.') }}</h3>
                <small class="text-muted">Promedio por pedido</small>
            </div>
        </div>
    </div>

    <!-- Retention Rate -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 bg-light-info p-2 rounded">
                        <i class="ti ti-user-check text-info fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted fw-bold mb-0 small">Retención</h6>
                    </div>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($retentionRate, 1) }}%</h3>
                <small class="text-muted">Clientes recurrentes</small>
            </div>
        </div>
    </div>

    <!-- Conversion -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 bg-light-warning p-2 rounded">
                        <i class="ti ti-chart-pie text-warning fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted fw-bold mb-0 small">Conversión</h6>
                    </div>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($conversionRate, 1) }}%</h3>
                <small class="text-muted">De cotizaciones a ventas</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <!-- Sales Trend -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Tendencia de Ventas</h5>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Category Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Ventas por Categoría</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="height: 250px; width: 100%;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales by City -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Ventas por Ciudad (Top 5)</h5>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="cityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Type Distribution -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Distribución por Tipo</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="height: 250px; width: 100%;">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rankings Section -->
<div class="row">
    <!-- Top Products -->
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0">Productos Más Vendidos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end pe-4">Ganancia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $prod)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avtar avtar-s bg-light-primary text-primary rounded-circle me-2">
                                                <i class="ti ti-package"></i>
                                            </div>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $prod->name }}">
                                                {{ $prod->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold">{{ $prod->total_qty }}</td>
                                    <td class="text-end pe-4 text-success fw-bold">${{ number_format($prod->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Sin datos suficientes</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- VIP Clients -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0">Mejores Clientes</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Cliente</th>
                                <th class="text-center">Pedidos</th>
                                <th class="text-end pe-4">Total Gastado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vipClients as $client)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avtar avtar-s bg-light-secondary text-secondary rounded-circle me-2">
                                                {{ strtoupper(substr($client->customer_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $client->customer_name }}</h6>
                                                <small class="text-muted d-block text-truncate" style="max-width: 150px;">{{ $client->customer_email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $client->orders_count }}</td>
                                    <td class="text-end pe-4 text-primary fw-bold">${{ number_format($client->total_spent, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Sin datos suficientes</td>
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

@push('scripts')
<script src="{{ asset('assets/js/libs/chart.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Sales Trend Chart ---
        var ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
        var trendLabels = @json($trendLabels);
        var trendValues = @json($trendValues);

        // Gradient for Line Chart
        var gradientTrend = ctxTrend.createLinearGradient(0, 0, 0, 400);
        gradientTrend.addColorStop(0, 'rgba(70, 128, 255, 0.5)'); // Primary color opaque
        gradientTrend.addColorStop(1, 'rgba(70, 128, 255, 0.05)'); // Fade out

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Ventas ($)',
                    data: trendValues,
                    borderColor: '#4680ff',
                    backgroundColor: gradientTrend,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f0f0f0' },
                        ticks: {
                            callback: function(value) { return '$' + value; }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // --- Category Donut Chart ---
        var ctxCat = document.getElementById('categoryChart').getContext('2d');
        var catLabels = @json($catLabels);
        var catValues = @json($catValues);

        // Colors Palette
        var backgroundColors = [
            '#4680ff', '#2ca87f', '#e58a00', '#dc2626', '#3ec9d6', '#7267EF'
        ];

        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: backgroundColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    }
                },
                cutout: '65%',
            }
        });

        // --- Sales by City Chart ---
        var ctxCity = document.getElementById('cityChart').getContext('2d');
        var cityLabels = {!! json_encode($salesByCity->pluck('shipping_city')) !!};
        var cityValues = {!! json_encode($salesByCity->pluck('total')) !!};

        new Chart(ctxCity, {
            type: 'bar',
            data: {
                labels: cityLabels,
                datasets: [{
                    label: 'Ventas ($)',
                    data: cityValues,
                    backgroundColor: '#3ec9d6',
                    borderRadius: 5,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { display: false } },
                    y: { grid: { display: false } }
                }
            }
        });

        // --- Order Type Chart ---
        var ctxType = document.getElementById('typeChart').getContext('2d');
        var typeLabels = {!! json_encode($salesByType->pluck('type')->map(fn($t) => ucfirst($t))) !!};
        var typeValues = {!! json_encode($salesByType->pluck('total')) !!};

        new Chart(ctxType, {
            type: 'pie',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeValues,
                    backgroundColor: ['#7267EF', '#4680ff'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 8 }
                    }
                }
            }
        });
    });
</script>
@endpush
