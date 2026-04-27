<div class="p-4 bg-gray-100 min-h-screen">
    <div class="flex flex-col lg:flex-row gap-4">
        <!-- Izquierda: Búsqueda y Productos -->
        <div class="lg:w-2/3 flex flex-col gap-4">
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="relative flex gap-2">
                    <div class="relative flex-grow">
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Buscar producto (Nombre o Código)..." 
                            class="w-full p-4 pl-12 rounded-lg border-2 border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300 text-xl"
                            autofocus
                        >
                        <div class="absolute left-4 top-5 text-gray-400">
                            <i class="fas fa-search fa-lg"></i>
                        </div>
                    </div>
                    <button id="start-scanner" class="bg-gray-800 text-white px-6 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-camera fa-lg"></i>
                    </button>
                </div>

                <div id="interactive" class="viewport mt-4 hidden" style="width: 100%; height: 300px; overflow: hidden; border-radius: 8px;"></div>

                @if(!empty($customers) && strlen($search) >= 2)
                <div class="mt-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <h3 class="text-sm font-bold text-yellow-800 mb-2">Clientes encontrados:</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($customers as $customer)
                        <button 
                            wire:click="$set('customerId', {{ $customer->id }})"
                            class="bg-white px-3 py-1 rounded-full border border-yellow-300 hover:bg-yellow-100 text-sm flex items-center gap-2"
                        >
                            <i class="fas fa-user text-yellow-600"></i>
                            {{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->points }} pts)
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif


                @if(!empty($products))
                <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($products as $product)
                    <div 
                        wire:click="addToCart({{ $product->id }})"
                        class="cursor-pointer bg-blue-50 hover:bg-blue-100 p-4 rounded-lg border border-blue-200 transition duration-200 flex flex-col items-center text-center"
                    >
                        <img src="{{ $product->image_url }}" class="w-20 h-20 object-cover mb-2 rounded shadow-sm">
                        <span class="font-bold text-gray-800">{{ $product->name }}</span>
                        <span class="text-blue-600 font-semibold">{{ config('settings.currency_symbol') }} {{ number_format($product->price, 2) }}</span>
                        <span class="text-xs text-gray-500">Stock: {{ $product->quantity }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Tabla del Carrito -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="p-4">Producto</th>
                            <th class="p-4 text-center">Cant.</th>
                            <th class="p-4 text-right">Precio</th>
                            <th class="p-4 text-right">Subtotal</th>
                            <th class="p-4 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($cart as $id => $item)
                        <tr>
                            <td class="p-4">
                                <div class="font-bold">{{ $item['name'] }}</div>
                                <input 
                                    wire:model.blur="cart.{{ $id }}.notes" 
                                    type="text" 
                                    placeholder="Nota..." 
                                    class="text-xs text-gray-400 border-none focus:ring-0 p-0 w-full bg-transparent"
                                >
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300">-</button>
                                    <span class="w-8 text-center font-bold">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300">+</button>
                                </div>
                            </td>
                            <td class="p-4 text-right">{{ config('settings.currency_symbol') }} {{ number_format($item['price'], 2) }}</td>
                            <td class="p-4 text-right font-bold text-blue-600">{{ config('settings.currency_symbol') }} {{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            <td class="p-4 text-center">
                                <button wire:click="removeFromCart({{ $id }})" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-gray-400 italic">El carrito está vacío. Empiece a escanear productos.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Derecha: Resumen y Pago -->
        <div class="lg:w-1/3 flex flex-col gap-4">
            <div class="bg-white p-6 rounded-lg shadow-md border-t-8 border-blue-600">
                <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-blue-600"></i> Resumen
                </h2>

                <!-- Cliente -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Cliente</label>
                    <select wire:model="customerId" class="w-full mt-1 p-2 border rounded focus:ring-blue-500">
                        <option value="">Cliente Genérico</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Descuento -->
                <div class="mb-4 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo Desc.</label>
                        <select wire:model.live="discountType" class="w-full mt-1 p-2 border rounded">
                            <option value="percentage">% Porcentaje</option>
                            <option value="fixed">$ Monto Fijo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Valor</label>
                        <input wire:model.live="discountValue" type="number" class="w-full mt-1 p-2 border rounded">
                    </div>
                </div>

                <hr class="my-4">

                <div class="flex justify-between text-gray-600 mb-2">
                    <span>Subtotal:</span>
                    <span>{{ config('settings.currency_symbol') }} {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-2xl font-black text-gray-800 mb-6">
                    <span>TOTAL:</span>
                    <span class="text-blue-600">{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</span>
                </div>

                <!-- Pago -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <button 
                            wire:click="$set('paymentMethod', 'cash')"
                            class="p-2 border rounded flex items-center justify-center gap-2 {{ $paymentMethod === 'cash' ? 'bg-blue-600 text-white' : 'bg-white' }}"
                        >
                            <i class="fas fa-money-bill"></i> Efectivo
                        </button>
                        <button 
                            wire:click="$set('paymentMethod', 'card')"
                            class="p-2 border rounded flex items-center justify-center gap-2 {{ $paymentMethod === 'card' ? 'bg-blue-600 text-white' : 'bg-white' }}"
                        >
                            <i class="fas fa-credit-card"></i> Tarjeta
                        </button>
                    </div>

                    @if($paymentMethod === 'cash')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Efectivo Recibido</label>
                        <input wire:model.live="amountReceived" type="number" class="w-full mt-1 p-3 text-2xl font-bold border rounded bg-yellow-50">
                    </div>
                    <div class="flex justify-between items-center text-xl">
                        <span class="text-gray-500">Cambio:</span>
                        <span class="font-bold text-green-600">{{ config('settings.currency_symbol') }} {{ number_format($change, 2) }}</span>
                    </div>
                    @endif
                </div>

                <button 
                    wire:click="submitOrder"
                    class="w-full py-4 bg-green-600 hover:bg-green-700 text-white text-xl font-bold rounded-xl shadow-lg transition duration-200 flex items-center justify-center gap-3"
                >
                    <i class="fas fa-check-circle"></i> COMPLETAR VENTA
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('swal:success', (data) => {
        Swal.fire({
            icon: 'success',
            title: data[0].message,
            showConfirmButton: true,
            confirmButtonText: 'Imprimir Ticket',
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(`/admin/orders/${data[0].order_id}/invoice`, '_blank');
            }
        });
    });

    $wire.on('swal:error', (data) => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data[0].message,
        });
    });

    $wire.on('swal:warning', (data) => {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: data[0].message,
        });
    });

    document.getElementById('start-scanner').addEventListener('click', function() {
        const scannerDiv = document.getElementById('interactive');
        scannerDiv.classList.toggle('hidden');

        if (!scannerDiv.classList.contains('hidden')) {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: scannerDiv,
                    constraints: {
                        facingMode: "environment"
                    }
                },
                decoder: {
                    readers: ["ean_reader", "code_128_reader", "upc_reader"]
                }
            }, function(err) {
                if (err) {
                    console.log(err);
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function(result) {
                const code = result.codeResult.code;
                $wire.set('search', code);
                Quagga.stop();
                scannerDiv.classList.add('hidden');
            });
        } else {
            Quagga.stop();
        }
    });
</script>
@endscript
