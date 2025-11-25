<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::post('/logout', function () {
    // Log logout before logging out
    if (auth()->check()) {
        \App\Helpers\ActivityLogger::logLogout();
    }
    
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('menu.access:dashboard');
    
    // POS Interface
    Route::get('/pos', \App\Livewire\Cashier\PosInterface::class)->name('pos')->middleware('menu.access:pos');
    
    // Management Routes
    Route::get('/branches', \App\Livewire\Admin\BranchManager::class)->name('branches')->middleware('menu.access:branches');
    Route::get('/categories', \App\Livewire\Admin\CategoryManager::class)->name('categories')->middleware('menu.access:categories');
    Route::get('/products', \App\Livewire\Admin\ProductManager::class)->name('products')->middleware('menu.access:products');
    Route::get('/users', \App\Livewire\Admin\UserManager::class)->name('users')->middleware('menu.access:users');
    Route::get('/employees', \App\Livewire\Admin\EmployeeManager::class)->name('employees')->middleware('role:manager');
    Route::get('/stock', \App\Livewire\Admin\StockManager::class)->name('stock')->middleware('menu.access:stock');
    Route::get('/transactions', \App\Livewire\Admin\TransactionManager::class)->name('transactions')->middleware('menu.access:transactions');
    Route::get('/customers', \App\Livewire\Admin\CustomerManager::class)->name('customers')->middleware('menu.access:customers');
    Route::get('/reports', \App\Livewire\Admin\ReportManager::class)->name('reports')->middleware('menu.access:reports');
    Route::get('/activity-logs', \App\Livewire\Admin\ActivityLogManager::class)->name('activity-logs')->middleware('role:owner');
    Route::get('/settings', \App\Livewire\Admin\SettingManager::class)->name('settings')->middleware('menu.access:settings');
});
