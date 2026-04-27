<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class PosScanner extends Component
{
    use WithPagination;

    public $search = '';
    public $cart = [];
    public $customerId = null;
    public $discountType = 'percentage'; // 'percentage' or 'fixed'
    public $discountValue = 0;
    public $paymentMethod = 'cash';
    public $amountReceived = 0;
    public $notes = '';

    protected $listeners = ['scanBarcode' => 'addToCartByBarcode'];

    public function render()
    {
        $customers = [];
        if (strlen($this->search) >= 2) {
            $products = Product::where('status', 1)
                ->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%');
                })
                ->limit(10)
                ->get();
            
            $customers = Customer::where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('phone', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->get();
        }

        return view('livewire.pos-scanner', [
            'products' => $products,
            'customers' => $customers,
            'subtotal' => $this->calculateSubtotal(),
            'total' => $this->calculateTotal(),
            'change' => $this->calculateChange(),
        ]);
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product || $product->quantity <= 0) {
            $this->dispatch('swal:error', ['message' => 'Producto sin stock']);
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'notes' => ''
            ];
        }
        $this->search = '';
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    public function updateQuantity($productId, $qty)
    {
        if ($qty <= 0) {
            $this->removeFromCart($productId);
            return;
        }
        $product = Product::find($productId);
        if ($qty > $product->quantity) {
            $this->dispatch('swal:warning', ['message' => 'Stock insuficiente']);
            $this->cart[$productId]['quantity'] = $product->quantity;
        } else {
            $this->cart[$productId]['quantity'] = $qty;
        }
    }

    public function calculateSubtotal()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function calculateTotal()
    {
        $subtotal = (float) $this->calculateSubtotal();
        $discountValue = (float) $this->discountValue;
        
        if ($this->discountType === 'percentage') {
            return $subtotal * (1 - ($discountValue / 100));
        }
        return max(0, $subtotal - $discountValue);
    }

    public function calculateChange()
    {
        return max(0, (float) $this->amountReceived - (float) $this->calculateTotal());
    }

    public function submitOrder()
    {
        if (empty($this->cart)) {
            $this->dispatch('swal:error', ['message' => 'El carrito está vacío']);
            return;
        }

        $order = Order::create([
            'customer_id' => $this->customerId,
            'user_id' => auth()->id(),
        ]);

        foreach ($this->cart as $item) {
            $order->items()->create([
                'price' => $item['price'] * $item['quantity'],
                'quantity' => $item['quantity'],
                'product_id' => $item['id'],
            ]);

            $product = Product::find($item['id']);
            $product->decrement('quantity', $item['quantity']);
            $product->checkStockAlert();

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'quantity' => -$item['quantity'],
                'type' => 'sale',
                'description' => 'Venta POS #' . $order->id . ($item['notes'] ? ' - ' . $item['notes'] : '')
            ]);
        }

        $order->payments()->create([
            'amount' => $this->calculateTotal(),
            'user_id' => auth()->id(),
        ]);

        if ($this->customerId) {
            $customer = Customer::find($this->customerId);
            $customer->addPoints($this->calculateTotal());
        }

        $this->cart = [];
        $this->customerId = null;
        $this->amountReceived = 0;
        $this->dispatch('swal:success', ['message' => 'Venta completada', 'order_id' => $order->id]);
    }
}
