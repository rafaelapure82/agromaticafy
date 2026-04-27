<div class="p-6 bg-white rounded-lg shadow-md max-w-2xl mx-auto mt-10">
    <h2 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-3">
        <i class="fas fa-cash-register text-blue-600"></i> Control de Caja
    </h2>

    @if(!$currentRegister)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <p class="text-blue-700 font-medium">La caja está actualmente CERRADA. Debe abrirla para empezar a vender.</p>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Monto Inicial de Apertura</label>
                <div class="relative mt-1">
                    <span class="absolute left-3 top-2 text-gray-400">$</span>
                    <input wire:model="openingBalance" type="number" step="0.01" class="w-full pl-8 p-3 border rounded-lg focus:ring-blue-500 text-xl font-bold">
                </div>
                @error('openingBalance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button wire:click="openRegister" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white text-xl font-bold rounded-xl shadow-lg transition duration-200">
                ABRIR CAJA
            </button>
        </div>
    @else
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <p class="text-green-700 font-medium">La caja está ABIERTA desde el {{ $currentRegister->opened_at->format('d/m/Y H:i') }}</p>
            <p class="text-sm text-green-600">Monto inicial: {{ config('settings.currency_symbol') }} {{ number_format($currentRegister->opening_balance, 2) }}</p>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Arqueo Final (Monto Real en Caja)</label>
                <div class="relative mt-1">
                    <span class="absolute left-3 top-2 text-gray-400">$</span>
                    <input wire:model="closingBalance" type="number" step="0.01" class="w-full pl-8 p-3 border rounded-lg focus:ring-green-500 text-xl font-bold">
                </div>
                @error('closingBalance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Notas del Turno</label>
                <textarea wire:model="notes" class="w-full mt-1 p-3 border rounded-lg" rows="3" placeholder="Observaciones sobre faltantes, sobrantes o incidencias..."></textarea>
            </div>

            <button wire:click="closeRegister" class="w-full py-4 bg-red-600 hover:bg-red-700 text-white text-xl font-bold rounded-xl shadow-lg transition duration-200">
                CERRAR CAJA Y REALIZAR ARQUEO
            </button>
        </div>
    @endif
</div>
