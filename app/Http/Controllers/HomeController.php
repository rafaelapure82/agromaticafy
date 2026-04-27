<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::with(['items', 'payments'])->get();
        $customers_count = Customer::count();
        $products_count = Product::count();
        
        // Ingresos Totales y Hoy
        $income = $orders->sum(fn($o) => $o->receivedAmount());
        $income_today = $orders->where('created_at', '>=', date('Y-m-d').' 00:00:00')->sum(fn($o) => $o->receivedAmount());

        // Ticket Promedio
        $avg_ticket = $orders->count() > 0 ? $income / $orders->count() : 0;

        // Producto Estrella (Hoy)
        $star_product = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->where('orders.created_at', '>=', date('Y-m-d').' 00:00:00')
            ->groupBy('products.name')
            ->orderBy('total_qty', 'desc')
            ->first();

        // Caja Actual Abierta
        $current_register = CashRegister::where('status', 'open')->first();
        $cash_in_hand = $current_register ? ($current_register->opening_balance + $income_today) : 0;

        // Productos bajo stock
        $low_stock_count = Product::whereColumn('quantity', '<=', 'min_stock')->count();

        return view('home', [
            'orders_count' => $orders->count(),
            'income' => $income,
            'income_today' => $income_today,
            'avg_ticket' => $avg_ticket,
            'star_product' => $star_product->name ?? 'N/A',
            'cash_in_hand' => $cash_in_hand,
            'customers_count' => $customers_count,
            'products_count' => $products_count,
            'low_stock_count' => $low_stock_count
        ]);
    }
}
