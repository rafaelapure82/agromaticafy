@extends('layouts.admin')

@section('title', 'Historial de Cliente')
@section('content-header', 'Ficha Técnica y Historial de Compras')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="{{ $customer->getAvatarUrl() }}" alt="User profile picture">
                </div>
                <h3 class="profile-username text-center">{{ $customer->first_name }} {{ $customer->last_name }}</h3>
                <p class="text-muted text-center">{{ $customer->document_id }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right">{{ $customer->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Teléfono</b> <a class="float-right">{{ $customer->phone }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Límite de Crédito</b> <a class="float-right">{{ config('settings.currency_symbol') }} {{ number_format($customer->credit_limit, 2) }}</a>
                    </li>
                </ul>

                <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección</strong>
                <p class="text-muted">{{ $customer->address }}</p>
                <hr>
                <strong><i class="fas fa-file-alt mr-1"></i> Notas / Ficha Técnica</strong>
                <p class="text-muted">{{ $customer->notes ?: 'Sin notas' }}</p>

                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-block"><b>Editar Ficha</b></a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title">Historial de Compras</h3>
                <div class="card-tools">
                    <a href="{{ route('customers.report', $customer) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> Generar Reporte
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Recibido</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ config('settings.currency_symbol') }} {{ number_format($order->total(), 2) }}</td>
                            <td>{{ config('settings.currency_symbol') }} {{ number_format($order->receivedAmount(), 2) }}</td>
                            <td>
                                @if($order->receivedAmount() == 0)
                                    <span class="badge badge-danger">No Pagado</span>
                                @elseif($order->receivedAmount() < $order->total())
                                    <span class="badge badge-warning">Parcial</span>
                                @elseif($order->receivedAmount() == $order->total())
                                    <span class="badge badge-success">Pagado</span>
                                @else
                                    <span class="badge badge-info">Cambio</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
