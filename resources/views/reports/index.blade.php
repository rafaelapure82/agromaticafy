@extends('layouts.admin')

@section('title', 'Reportes de Inventario Inteligente')
@section('content-header', 'Analítica de Inventario y Ventas')

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="card mb-4 shadow-sm border-left-primary">
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row items-center">
                <div class="col-md-4">
                    <label>Desde:</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label>Hasta:</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 mt-4">
                    <button type="submit" class="btn btn-primary px-4">Filtrar Analítica</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Smart KPIs -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-gradient-info shadow">
                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valor Venta Stock</span>
                    <span class="info-box-number">{{ config('settings.currency_symbol') }} {{ number_format($inventoryValue, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-gradient-purple shadow">
                <span class="info-box-icon"><i class="fas fa-tags"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Costo Total Stock</span>
                    <span class="info-box-number">{{ config('settings.currency_symbol') }} {{ number_format($inventoryCostValue, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-gradient-success shadow">
                <span class="info-box-icon"><i class="fas fa-sync"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rotación (Turnover)</span>
                    <span class="info-box-number">{{ number_format($inventoryTurnover, 2) }}x</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-gradient-warning shadow text-white">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Alertas de Stock</span>
                    <span class="info-box-number text-white">{{ $lowStockItems }} Críticos</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico ABC Analysis -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold">Análisis ABC (Pareto)</h3>
                </div>
                <div class="card-body">
                    <canvas id="abcChart" style="height: 250px;"></canvas>
                    <div class="mt-3 text-sm text-muted">
                        <p><strong>Clase A:</strong> 80% del valor/ventas.</p>
                        <p><strong>Clase B:</strong> 15% del valor/ventas.</p>
                        <p><strong>Clase C:</strong> 5% del valor/ventas.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tendencia -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold">Desempeño de Ventas</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Top Productos -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Ranking de Rentabilidad (Top 10)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Producto</th>
                                <th>Unidades</th>
                                <th>Ingresos</th>
                                <th>Contribución</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->total_qty }}</td>
                                <td>{{ config('settings.currency_symbol') }} {{ number_format($item->total_revenue, 2) }}</td>
                                <td>{{ number_format(($item->total_revenue / ($inventoryValue ?: 1)) * 100, 2) }}%</td>
                                <td>
                                    @php $abc = $item->product->getAbcCategory(); @endphp
                                    <span class="badge {{ $abc == 'A' ? 'badge-success' : ($abc == 'B' ? 'badge-warning' : 'badge-secondary') }}">
                                        Clase {{ $abc }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Gráfico de Ventas
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesData->pluck('date')) !!},
            datasets: [{
                label: 'Ingresos Diarios',
                data: {!! json_encode($salesData->pluck('total')) !!},
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Gráfico ABC
    const abcCtx = document.getElementById('abcChart').getContext('2d');
    new Chart(abcCtx, {
        type: 'doughnut',
        data: {
            labels: ['Clase A', 'Clase B', 'Clase C'],
            datasets: [{
                data: [{{ $abcAnalysis['A'] }}, {{ $abcAnalysis['B'] }}, {{ $abcAnalysis['C'] }}],
                backgroundColor: ['#28a745', '#ffc107', '#6c757d']
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%'
        }
    });
</script>
@endsection
