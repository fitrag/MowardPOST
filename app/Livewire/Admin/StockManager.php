<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Branch;
use App\Models\Stock;
use Livewire\Component;
use App\Helpers\ActivityLogger;

class StockManager extends Component
{
    public $branches;
    public $selectedBranchId;
    public $stocks = [];
    public $quantities = [];
    public $search = '';
    public $perPage = 20;
    public $hasMore = false;

    public function mount()
    {
        $this->branches = Branch::all();
        $this->selectedBranchId = $this->branches->first()->id ?? null;
        $this->loadStocks();
    }

    public function updatedSelectedBranchId()
    {
        $this->perPage = 20; // Reset pagination
        $this->loadStocks();
    }

    public function updatedSearch()
    {
        $this->perPage = 20; // Reset pagination when searching
        $this->loadStocks();
    }

    public function loadStocks()
    {
        if ($this->selectedBranchId) {
            $query = Stock::with('product')
                ->where('branch_id', $this->selectedBranchId);
            
            // Apply search filter
            if ($this->search) {
                $query->whereHas('product', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            }
            
            // Get total count for hasMore check
            $totalCount = $query->count();
            
            // Apply pagination
            $this->stocks = $query->take($this->perPage)->get();
            
            // Check if there are more items
            $this->hasMore = $totalCount > $this->perPage;
            
            // If no stocks exist, create empty stock records for all products
            if ($this->stocks->isEmpty() && !$this->search) {
                $products = Product::all();
                foreach ($products as $product) {
                    Stock::create([
                        'branch_id' => $this->selectedBranchId,
                        'product_id' => $product->id,
                        'quantity' => 0
                    ]);
                }
                // Reload stocks after creating
                $this->loadStocks();
            }
        }
    }

    public function loadMore()
    {
        $this->perPage += 20;
        $this->loadStocks();
    }

    public function updateStock($stockId)
    {
        // Get quantity from the quantities array
        $quantity = $this->quantities[$stockId] ?? null;
        
        // Validate the quantity
        if ($quantity === null || !is_numeric($quantity) || $quantity < 0) {
            $this->dispatch('error', 'Please enter a valid quantity (0 or greater).');
            return;
        }

        $stock = Stock::findOrFail($stockId);
        $oldQuantity = $stock->quantity;
        $stock->update(['quantity' => (int)$quantity]);

        // Log activity
        ActivityLogger::log(
            'updated',
            auth()->user()->name . ' updated stock for ' . $stock->product->name . 
            ' at ' . $stock->branch->name . 
            ' from ' . $oldQuantity . ' to ' . $quantity,
            Stock::class,
            $stock->id,
            [
                'product' => $stock->product->name,
                'branch' => $stock->branch->name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => (int)$quantity
            ]
        );

        $this->dispatch('success', 'Stock updated successfully!');
        $this->loadStocks();
    }

    public function render()
    {
        return view('livewire.admin.stock-manager')->layout('layouts.app');
    }
}
