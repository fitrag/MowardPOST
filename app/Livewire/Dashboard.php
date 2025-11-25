<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Base query for transactions (filter by branch for managers)
        $transactionQuery = Transaction::query();
        if ($user->hasRole('manager') && $user->branch_id) {
            $transactionQuery->where('branch_id', $user->branch_id);
        }

        // Sales
        $currentMonthSales = (clone $transactionQuery)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total');
        $lastMonthSales = (clone $transactionQuery)->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('total');
        
        $salesGrowth = 0;
        if ($lastMonthSales > 0) {
            $salesGrowth = (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
        } elseif ($currentMonthSales > 0) {
            $salesGrowth = 100;
        }

        // Orders
        $currentMonthOrders = (clone $transactionQuery)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $lastMonthOrders = (clone $transactionQuery)->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $ordersGrowth = 0;
        if ($lastMonthOrders > 0) {
            $ordersGrowth = (($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100;
        } elseif ($currentMonthOrders > 0) {
            $ordersGrowth = 100;
        }

        // Active Products (all products for now, could be filtered by branch stock if needed)
        $activeProducts = Product::where('is_active', true)->count();

        // Low Stock (filter by branch for managers)
        $stockQuery = Stock::where('quantity', '<', 10);
        if ($user->hasRole('manager') && $user->branch_id) {
            $stockQuery->where('branch_id', $user->branch_id);
        }
        $lowStockCount = $stockQuery->count();

        // Recent Transactions (filter by branch for managers)
        $recentTransactionsQuery = Transaction::with('user');
        if ($user->hasRole('manager') && $user->branch_id) {
            $recentTransactionsQuery->where('branch_id', $user->branch_id);
        }
        $recentTransactions = $recentTransactionsQuery->latest()->take(5)->get();

        return view('livewire.dashboard', [
            'currentMonthSales' => $currentMonthSales,
            'salesGrowth' => $salesGrowth,
            'currentMonthOrders' => $currentMonthOrders,
            'ordersGrowth' => $ordersGrowth,
            'activeProducts' => $activeProducts,
            'lowStockCount' => $lowStockCount,
            'recentTransactions' => $recentTransactions,
        ])->layout('layouts.app');
    }
}
