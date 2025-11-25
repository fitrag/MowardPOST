<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ReportManager extends Component
{
    use WithPagination;

    public $dateStart;
    public $dateEnd;
    public $filterBranch = '';
    public $reportType = 'daily'; // daily, monthly
    public $showModal = false;
    public $selectedTransaction = null;

    public function mount()
    {
        // Default to current month
        $this->dateStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function showDetails($transactionId)
    {
        $this->selectedTransaction = Transaction::with(['branch', 'user', 'items.product'])
            ->find($transactionId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTransaction = null;
    }

    public function updated()
    {
        $this->resetPage();
        $this->dispatch('update-chart', data: $this->getSalesChartData($this->buildQuery()));
    }

    protected function buildQuery()
    {
        $user = Auth::user();
        $query = Transaction::query()->where('status', 'completed');

        // Apply filters
        if ($user->hasRole('manager')) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($user->hasRole('owner') && $this->filterBranch) {
            $query->where('branch_id', $this->filterBranch);
        } elseif ($user->hasRole('cashier')) {
             $query->where('branch_id', $user->branch_id);
        }

        if ($this->dateStart) {
            $query->whereDate('created_at', '>=', $this->dateStart);
        }
        if ($this->dateEnd) {
            $query->whereDate('created_at', '<=', $this->dateEnd);
        }

        return $query;
    }

    public function getSummaryStats($query)
    {
        $transactionIds = (clone $query)->pluck('id');
        
        $totalRevenue = $query->sum('total');
        $totalTransactions = $query->count();
        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Calculate Profit
        $totalProfit = TransactionItem::whereIn('transaction_id', $transactionIds)
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->select(DB::raw('SUM((transaction_items.price - COALESCE(transaction_items.cost, products.cost, 0)) * transaction_items.quantity) as profit'))
            ->value('profit') ?? 0;

        $margin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'avg_transaction' => $avgTransaction,
            'total_profit' => $totalProfit,
            'margin' => $margin,
        ];
    }

    public function getSalesChartData($baseQuery)
    {
        $query = clone $baseQuery;
        $transactionIds = $query->pluck('id');
        
        if ($this->reportType === 'daily') {
            $data = TransactionItem::whereIn('transaction_id', $transactionIds)
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_items.product_id', '=', 'products.id')
                ->select(
                    DB::raw('DATE(transactions.created_at) as date'),
                    DB::raw('SUM(transaction_items.price * transaction_items.quantity) as revenue'),
                    DB::raw('SUM((transaction_items.price - COALESCE(transaction_items.cost, products.cost, 0)) * transaction_items.quantity) as profit')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
                'revenue' => $data->pluck('revenue')->toArray(),
                'profit' => $data->pluck('profit')->toArray(),
            ];
        } else {
             $data = TransactionItem::whereIn('transaction_id', $transactionIds)
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_items.product_id', '=', 'products.id')
                ->select(
                    DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m") as month'),
                    DB::raw('SUM(transaction_items.price * transaction_items.quantity) as revenue'),
                    DB::raw('SUM((transaction_items.price - COALESCE(transaction_items.cost, products.cost, 0)) * transaction_items.quantity) as profit')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return [
                'labels' => $data->pluck('month')->map(fn($d) => Carbon::createFromFormat('Y-m', $d)->format('M Y'))->toArray(),
                'revenue' => $data->pluck('revenue')->toArray(),
                'profit' => $data->pluck('profit')->toArray(),
            ];
        }
    }

    public function getTopProducts($baseQuery)
    {
        $transactionIds = (clone $baseQuery)->pluck('id');
        
        return TransactionItem::whereIn('transaction_id', $transactionIds)
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.price * transaction_items.quantity) as total_revenue'),
                DB::raw('SUM((transaction_items.price - COALESCE(transaction_items.cost, products.cost, 0)) * transaction_items.quantity) as total_profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $query = $this->buildQuery();

        $stats = $this->getSummaryStats(clone $query);
        $chartData = $this->getSalesChartData(clone $query);
        $topProducts = $this->getTopProducts(clone $query);
        
        $transactions = (clone $query)
            ->with(['branch', 'user', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.report-manager', [
            'stats' => $stats,
            'chartData' => $chartData,
            'topProducts' => $topProducts,
            'transactions' => $transactions,
            'branches' => Auth::user()->hasRole('owner') ? Branch::all() : [],
        ])->layout('layouts.app');
    }
}
