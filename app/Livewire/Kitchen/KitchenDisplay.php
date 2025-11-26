<?php

namespace App\Livewire\Kitchen;

use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class KitchenDisplay extends Component
{
    public function getOrdersProperty()
    {
        $user = Auth::user();
        $query = Transaction::with(['items.product', 'user'])
            ->whereNotNull('kitchen_status')
            ->where('kitchen_status', '!=', 'served')
            ->orderBy('created_at', 'asc');

        if ($user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        return $query->get();
    }

    public function updateStatus($transactionId, $status)
    {
        $transaction = Transaction::find($transactionId);
        if ($transaction) {
            $transaction->update(['kitchen_status' => $status]);
        }
    }

    public function render()
    {
        return view('livewire.kitchen.kitchen-display', [
            'orders' => $this->orders
        ])->layout('layouts.app');
    }
}
