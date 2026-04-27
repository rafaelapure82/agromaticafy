@extends('layouts.admin')

@section('content-header', 'Tablero de Control Inteligente')

@section('content')
    <div class="container-fluid">
        <!-- Fila 1: Métricas Principales -->
        <div class="row">
            <!-- Ingresos de Hoy -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-success shadow">
                    <div class="inner">
                        <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3>
                        <p>Ventas de Hoy</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    <a href="{{route('orders.index')}}" class="small-box-footer">Ver ventas <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Ticket Promedio -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-info shadow">
                    <div class="inner">
                        <h3>{{config('settings.currency_symbol')}} {{number_format($avg_ticket, 2)}}</h3>
                        <p>Ticket Promedio</p>
                    </div>
                    <div class="icon"><i class="fas fa-receipt"></i></div>
                    <div class="small-box-footer">Métrica de Rendimiento</div>
                </div>
            </div>

            <!-- Caja Real Estimada -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-gradient-primary shadow">
                    <div class="inner">
                        <h3>{{config('settings.currency_symbol')}} {{number_format($cash_in_hand, 2)}}</h3>
                        <p>Efectivo en Caja</p>
                    </div>
                    <div class="icon"><i class="fas fa-vault"></i></div>
                    <a href="{{route('cash.register')}}" class="small-box-footer">Ir a Arqueo <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Alertas de Stock -->
            <div class="col-lg-3 col-6">
                <div class="small-box @if($low_stock_count > 0) bg-gradient-danger @else bg-gradient-gray @endif shadow">
                    <div class="inner">
                        <h3>{{$low_stock_count}}</h3>
                        <p>Stock Crítico</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <a href="{{route('products.index', ['search' => 'low_stock'])}}" class="small-box-footer">Gestionar Inventario <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Fila 2: Análisis de Productos y Clientes -->
        <div class="row">
            <!-- Producto Estrella -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-left-warning">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Producto Estrella (Hoy)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $star_product }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-star fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Clientes -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-purple shadow">
                    <div class="inner">
                        <h3>{{$customers_count}}</h3>
                        <p>Base de Clientes</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <a href="{{route('customers.index')}}" class="small-box-footer">Gestionar CRM <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Total Productos -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-teal shadow">
                    <div class="inner">
                        <h3>{{$products_count}}</h3>
                        <p>Catálogo Total</p>
                    </div>
                    <div class="icon"><i class="fas fa-boxes"></i></div>
                    <a href="{{route('products.index')}}" class="small-box-footer">Ver Catálogo <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Enlace a Reportes Avanzados -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-light border shadow-sm text-center">
                    <p class="mb-0">Para un análisis más profundo de las tendencias de su negocio, visite nuestro
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary ml-2">Módulo de Reportes Avanzados</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
