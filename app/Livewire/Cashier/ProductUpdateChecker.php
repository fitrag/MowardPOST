<?php

namespace App\Livewire\Cashier;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\Cache;

class ProductUpdateChecker extends Component
{
    #[Reactive]
    public $lastLoadedAt;
    
    public $hasNewUpdates = false;

    public function checkUpdates()
    {
        $lastUpdated = Cache::get('products_last_updated', 0);
        
        if ($lastUpdated > $this->lastLoadedAt) {
            $this->hasNewUpdates = true;
        } else {
            $this->hasNewUpdates = false;
        }
    }
    
    public function triggerRefresh()
    {
        $this->dispatch('refresh-products');
        $this->hasNewUpdates = false;
    }

    public function render()
    {
        return view('livewire.cashier.product-update-checker');
    }
}
