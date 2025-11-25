<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionManager extends Component
{
    // Pagination
    public $perPage = 10;
    public $hasMore = false;

    public $search = '';
    public $selectedTransaction = null;
    public $showModal = false;
    
    // Filters
    public $dateStart = '';
    public $dateEnd = '';
    public $filterBranch = '';

    public function updatingSearch()
    {
        $this->perPage = 10;
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->dateStart = '';
        $this->dateEnd = '';
        $this->filterBranch = '';
        $this->perPage = 10;
    }

    public function showDetails($id)
    {
        $this->selectedTransaction = Transaction::with(['user', 'branch', 'items.product'])->find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTransaction = null;
    }

    public function render()
    {
        $user = Auth::user();
        $query = Transaction::with(['user', 'branch', 'items.product'])
            ->latest();

        // Role-based filtering
        if ($user->hasRole('cashier')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('manager')) {
            $query->where('branch_id', $user->branch_id);
        }
        // Owners see all, but can filter by branch
        elseif ($user->hasRole('owner') && $this->filterBranch) {
            $query->where('branch_id', $this->filterBranch);
        }

        // Date Range Filter
        if ($this->dateStart) {
            $query->whereDate('created_at', '>=', $this->dateStart);
        }
        if ($this->dateEnd) {
            $query->whereDate('created_at', '<=', $this->dateEnd);
        }

        // Search functionality
        if ($this->search) {
            $query->where(function($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($u) {
                      $u->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Check if there are more results
        $total = $query->count();
        $this->hasMore = $total > $this->perPage;

        return view('livewire.admin.transaction-manager', [
            'transactions' => $query->take($this->perPage)->get(),
            'branches' => $user->hasRole('owner') ? \App\Models\Branch::all() : []
        ])->layout('layouts.app');
    }
}
