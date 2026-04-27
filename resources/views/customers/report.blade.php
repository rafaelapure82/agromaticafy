<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cliente - {{ $customer->first_name }} {{ $customer->last_name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .ficha-tecnica { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .ficha-tecnica td { padding: 8px; border: 1px solid #ddd; }
        .ficha-tecnica .label { font-weight: bold; background-color: #f9f9f9; width: 30%; }
        .historial { width: 100%; border-collapse: collapse; }
        .historial th, .historial td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .historial th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #eee; }
        .badge { padding: 3px 7px; border-radius: 4px; font-size: 11px; }
        .paid { background-color: #d4edda; color: #155724; }
        .partial { background-color: #fff3cd; color: #856404; }
        .unpaid { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Historial de Cliente</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <h3>Ficha Técnica</h3>
    <table class="ficha-tecnica">
        <tr>
            <td class="label">Nombre Completo</td>
            <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
        </tr>
        <tr>
            <td class="label">Documento ID</td>
            <td>{{ $customer->document_id }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td>{{ $customer->email }}</td>
        </tr>
        <tr>
            <td class="label">Teléfono</td>
            <td>{{ $customer->phone }}</td>
        </tr>
        <tr>
            <td class="label">Límite de Crédito</td>
            <td>{{ config('settings.currency_symbol') }} {{ number_format($customer->credit_limit, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Dirección</td>
            <td>{{ $customer->address }}</td>
        </tr>
        <tr>
            <td class="label">Notas</td>
            <td>{{ $customer->notes ?: 'Sin notas' }}</td>
        </tr>
    </table>

    <h3>Historial de Compras</h3>
    <table class="historial">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Recibido</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @php $totalComprado = 0; @endphp
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ config('settings.currency_symbol') }} {{ number_format($order->total(), 2) }}</td>
                <td>{{ config('settings.currency_symbol') }} {{ number_format($order->receivedAmount(), 2) }}</td>
                <td>
                    @if($order->receivedAmount() == 0)
                        No Pagado
                    @elseif($order->receivedAmount() < $order->total())
                        Parcial
                    @else
                        Pagado
                    @endif
                </td>
            </tr>
            @php $totalComprado += $order->total(); @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Histórico:</td>
                <td colspan="3">{{ config('settings.currency_symbol') }} {{ number_format($totalComprado, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
