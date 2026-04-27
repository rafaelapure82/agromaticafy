<?php

namespace App\Livewire;

use App\Models\CashRegister;
use Livewire\Component;

class CashRegisterManager extends Component
{
    public $openingBalance = 0;
    public $closingBalance = 0;
    public $notes = '';
    public $currentRegister = null;

    public function mount()
    {
        $this->currentRegister = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
    }

    public function render()
    {
        return view('livewire.cash-register-manager');
    }

    public function openRegister()
    {
        $this->validate([
            'openingBalance' => 'required|numeric|min:0',
        ]);

        $this->currentRegister = CashRegister::create([
            'user_id' => auth()->id(),
            'opening_balance' => (float) $this->openingBalance,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        $this->dispatch('swal:success', ['message' => 'Caja abierta exitosamente']);
    }

    public function closeRegister()
    {
        $this->validate([
            'closingBalance' => 'required|numeric|min:0',
        ]);

        // Calcular ventas del turno (simulado por ahora o sumando órdenes)
        $totalSales = \App\Models\Order::where('user_id', auth()->id())
            ->where('created_at', '>=', $this->currentRegister->opened_at)
            ->get()
            ->sum(fn($o) => $o->total());

        $this->currentRegister->update([
            'closing_balance' => (float) $this->closingBalance,
            'total_sales' => (float) $totalSales,
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $this->notes,
        ]);

        $this->currentRegister = null;
        $this->dispatch('swal:success', ['message' => 'Caja cerrada y arqueo completado']);
    }
}
