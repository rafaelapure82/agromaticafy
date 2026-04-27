<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?: now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?: now()->endOfDay()->format('Y-m-d');

        // Ventas por día para gráfico
        $salesData = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top 10 Productos más vendidos
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->with('product')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        // Resumen de Inventario
        $inventoryValue = Product::sum(DB::raw('price * quantity'));
        $inventoryCostValue = Product::sum(DB::raw('purchase_price * quantity'));
        $lowStockItems = Product::whereColumn('quantity', '<=', 'min_stock')->count();

        // Análisis ABC
        $abcAnalysis = [
            'A' => Product::all()->filter(fn($p) => $p->getAbcCategory() == 'A')->count(),
            'B' => Product::all()->filter(fn($p) => $p->getAbcCategory() == 'B')->count(),
            'C' => Product::all()->filter(fn($p) => $p->getAbcCategory() == 'C')->count(),
        ];

        // Rotación de Inventario (CMV / Inventario Promedio) - Simplificado
        $costOfGoodsSold = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->sum(DB::raw('products.purchase_price * order_items.quantity'));
        
        $inventoryTurnover = $inventoryCostValue > 0 ? ($costOfGoodsSold / $inventoryCostValue) : 0;

        return view('reports.index', compact(
            'salesData', 
            'topProducts', 
            'inventoryValue', 
            'inventoryCostValue',
            'lowStockItems', 
            'abcAnalysis',
            'inventoryTurnover',
            'startDate', 
            'endDate'
        ));
    }
}
