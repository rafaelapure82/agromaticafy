@extends('layouts.admin')

@section('title', 'Invoice #'. $order->id)

@section('css')
<style>
    @media print {
        .no-print { display: none !important; }
        .main-footer { display: none !important; }
        .content-header { display: none !important; }
        body { background-color: #fff !important; }
        .invoice { border: 0 !important; margin: 0 !important; padding: 0 !important; }
    }
    .invoice-title {
        font-weight: 800;
        font-size: 2.5rem;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .invoice-info-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border-left: 5px solid #3498db;
        height: 100%;
    }
    .table thead th {
        background-color: #2c3e50;
        color: #fff;
        border: none;
    }
    .total-section {
        background: #2c3e50;
        color: #fff;
        padding: 20px;
        border-radius: 10px;
    }
    .total-amount {
        font-size: 2rem;
        font-weight: 800;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-body p-5 invoice">
            <!-- Header -->
            <div class="row mb-5">
                <div class="col-sm-6">
                    <h1 class="invoice-title mb-0">{{ config('app.name') }}</h1>
                    <p class="text-muted">Premium POS & Inventory System</p>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <h3 class="text-uppercase text-muted">Invoice</h3>
                    <h4 class="font-weight-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h4>
                    <p class="mb-0">Date: <strong>{{ $order->created_at->format('M d, Y') }}</strong></p>
                </div>
            </div>

            <hr class="my-4">

            <!-- Addresses -->
            <div class="row mb-5">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <div class="invoice-info-box">
                        <h6 class="text-uppercase text-muted font-weight-bold">Billed From</h6>
                        <h5 class="font-weight-bold mb-1">{{ config('app.name') }}</h5>
                        <p class="mb-0"><i class="fas fa-phone mr-2"></i> {{ config('settings.phone', '+1 234 567 890') }}</p>
                        <p class="mb-0"><i class="fas fa-envelope mr-2"></i> {{ config('settings.email', 'contact@pos.com') }}</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="invoice-info-box" style="border-left-color: #e67e22;">
                        <h6 class="text-uppercase text-muted font-weight-bold">Billed To</h6>
                        <h5 class="font-weight-bold mb-1">{{ $order->getCustomerName() }}</h5>
                        @if($order->customer)
                            <p class="mb-0"><i class="fas fa-id-card mr-2"></i> ID: {{ $order->customer->document_id }}</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i> {{ $order->customer->address }}</p>
                            <p class="mb-0"><i class="fas fa-phone mr-2"></i> {{ $order->customer->phone }}</p>

                        @else
                            <p class="text-muted italic">Regular Walking Customer</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive mb-5">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="py-3">Description</th>
                            <th class="text-center py-3">Quantity</th>
                            <th class="text-right py-3">Unit Price</th>
                            <th class="text-right py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-3">
                                <span class="font-weight-bold">{{ $item->product->name }}</span>
                            </td>
                            <td class="text-center py-3">{{ $item->quantity }}</td>
                            <td class="text-right py-3">{{ config('settings.currency_symbol') }} {{ number_format($item->price / $item->quantity, 2) }}</td>
                            <td class="text-right py-3 font-weight-bold">{{ config('settings.currency_symbol') }} {{ number_format($item->price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="total-section">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>{{ config('settings.currency_symbol') }} {{ $order->formattedTotal() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (0%)</span>
                            <span>$ 0.00</span>
                        </div>
                        <hr class="bg-light">
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="h4 mb-0">Total</span>
                            <span class="total-amount">{{ config('settings.currency_symbol') }} {{ $order->formattedTotal() }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 border rounded">
                        <div class="d-flex justify-content-between text-muted small mb-1">
                            <span>Received</span>
                            <span>{{ config('settings.currency_symbol') }} {{ $order->formattedReceivedAmount() }}</span>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold text-success">
                            <span>Change Due</span>
                            <span>{{ config('settings.currency_symbol') }} {{ number_format($order->receivedAmount() - $order->total(), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-center text-muted no-print">
                <p>Thank you for shopping with us! Please come again.</p>
                <div class="btn-group">
                    <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
                        <i class="fas fa-print mr-2"></i> Print Invoice
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-lg ml-2">
                        <i class="fas fa-arrow-left mr-2"></i> Back to POS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto trigger print dialog when page loads
    window.onload = function() {
        // window.print();
    }
</script>


@endsection
