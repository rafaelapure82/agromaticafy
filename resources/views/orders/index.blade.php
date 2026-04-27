@extends('layouts.admin')

@section('title', 'Lista de Pedidos')
@section('content-header', 'Lista de Pedidos')
@section('content-actions')
    <a href="{{route('cart.index')}}" class="btn btn-success">Abrir POS</a>
@endsection

@section('content')
<div class="card"><!-- Log on to codeastro.com for more projects -->
    <div class="card-body">
        <div class="row">
            <!-- <div class="col-md-3"></div> -->
            <div class="col-md-12">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <hr>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Recibido</th>
                    <th>Estado</th>
                    <th>Restante</th>
                    <th>Creado el</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>{{$order->id}}</td>
                    <td>{{$order->getCustomerName()}}</td>
                    <td>{{ config('settings.currency_symbol') }} {{$order->formattedTotal()}}</td>
                    <td>{{ config('settings.currency_symbol') }} {{$order->formattedReceivedAmount()}}</td>
                    <td>
                        @if($order->receivedAmount() == 0)
                            <span class="badge badge-danger">No Pagado</span>
                        @elseif($order->receivedAmount() < $order->total())
                            <span class="badge badge-warning">Parcial</span>
                        @elseif($order->receivedAmount() == $order->total())
                            <span class="badge badge-success">Pagado</span>
                        @elseif($order->receivedAmount() > $order->total())
                            <span class="badge badge-info">Cambio</span>
                        @endif
                    </td>
                    <td>{{config('settings.currency_symbol')}} {{number_format($order->total() - $order->receivedAmount(), 2)}}</td>
                    <td>{{$order->created_at}}</td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm"><i class="fas fa-file-invoice"></i> Ver Factura</a>
                        <button onclick="window.open('{{ route('orders.show', $order) }}', '_blank').print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Reimprimir</button>
                    </td>
                </tr>

                @endforeach
            </tbody>
            <tfoot><!-- Log on to codeastro.com for more projects -->
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>

                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        {{ $orders->render() }}
    </div>
</div><!-- Log on to codeastro.com for more projects -->
@endsection

