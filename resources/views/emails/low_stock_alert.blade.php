<x-mail::message>
# ⚠️ Alerta de Inventario Crítico

El producto **{{ $product->name }}** ha alcanzado su nivel mínimo de stock.

<x-mail::panel>
**Detalles del Producto:**
- **SKU / Código:** {{ $product->barcode }}
- **Stock Actual:** {{ $product->quantity }}
- **Stock Mínimo configurado:** {{ $product->min_stock }}
</x-mail::panel>

Por favor, proceda a realizar un pedido de reabastecimiento para evitar quiebres de inventario.

<x-mail::button :url="route('products.edit', $product)">
Ver Producto en el Sistema
</x-mail::button>

Gracias,<br>
Sistema de Alertas Agromaticafy
</x-mail::message>
