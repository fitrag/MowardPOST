<?php

namespace App\Livewire\Cashier;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Customer;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ActivityLogger;

class PosInterface extends Component
{
    // public $products = []; // Removed to prevent hydration issues
    public $categories = [];
    public $selectedCategory = null;
    public $search = '';
    
    public $cart = [];
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $cash = 0;
    public $change = 0;
    
    // Customer & Points
    public $selectedCustomerId = null;
    public $customerSearch = '';
    public $usePoints = false;
    public $pointsToUse = 0;
    public $pointsDiscount = 0;
    public $pointsEarned = 0;
    
    public $limit = 12;

    public function mount()
    {
        $this->categories = Category::all();
    }
    
    public function loadMore()
    {
        $this->limit += 12;
    }
    
    private function getProductQuery()
    {
        $user = Auth::user();
        $query = Product::with('category')
            ->where('is_active', true);
            
        // Filter by branch if user is assigned to one
        if ($user->branch_id) {
            $query->whereHas('stocks', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })->with(['stocks' => function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            }]);
        }
        
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }
        
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }
        
        return $query;
    }
    
    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (!$product) return;
        
        // Check stock availability
        $stock = Stock::where('product_id', $productId)
                     ->where('branch_id', Auth::user()->branch_id)
                     ->first();
        
        if (!$stock || $stock->quantity < 1) {
            $this->dispatch('error', 'Product out of stock!');
            return;
        }
        
        // Check if already in cart
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] < $stock->quantity) {
                $this->cart[$productId]['quantity']++;
            } else {
                $this->dispatch('error', 'Not enough stock!');
                return;
            }
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
            ];
        }
        
        $this->calculateTotal();
    }
    
    public function updateQuantity($productId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($productId);
            return;
        }
        
        $stock = Stock::where('product_id', $productId)
                     ->where('branch_id', Auth::user()->branch_id)
                     ->first();
        
        if ($stock && $quantity <= $stock->quantity) {
            $this->cart[$productId]['quantity'] = $quantity;
            $this->calculateTotal();
        } else {
            $this->dispatch('error', 'Not enough stock!');
        }
    }
    
    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }
    
    public function calculateTotal()
    {
        $this->subtotal = 0;
        
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }
        
        $taxRate = \App\Models\Setting::getValue('tax_rate', 10);
        $this->tax = $this->subtotal * ($taxRate / 100);
        
        // Calculate points discount if using points
        $this->pointsDiscount = 0;
        if ($this->usePoints && $this->selectedCustomerId && $this->pointsToUse > 0) {
            $customer = Customer::find($this->selectedCustomerId);
            if ($customer && $customer->total_points >= $this->pointsToUse) {
                // 100 points = Rp 10,000
                $this->pointsDiscount = ($this->pointsToUse / 100) * 10000;
                
                // Max 50% of subtotal + tax
                $maxDiscount = ($this->subtotal + $this->tax) * 0.5;
                $this->pointsDiscount = min($this->pointsDiscount, $maxDiscount);
            } else {
                $this->usePoints = false;
                $this->pointsToUse = 0;
            }
        }
        
        $this->total = $this->subtotal + $this->tax - $this->pointsDiscount;
        
        // Calculate points that will be earned
        $this->calculatePointsEarned();
        
        $this->calculateChange();
    }
    
    public function updatedCash()
    {
        // Remove non-numeric characters if any (though we'll handle this in frontend too)
        if (is_string($this->cash)) {
            $this->cash = (float) preg_replace('/[^0-9]/', '', $this->cash);
        }
        $this->calculateChange();
    }
    
    public function calculateChange()
    {
        $this->change = max(0, $this->cash - $this->total);
    }
    
    // Customer & Points Methods
    public function selectCustomer($customerId)
    {
        $this->selectedCustomerId = $customerId;
        $this->customerSearch = '';
        $this->calculateTotal();
    }
    
    public function clearCustomer()
    {
        $this->selectedCustomerId = null;
        $this->usePoints = false;
        $this->pointsToUse = 0;
        $this->calculateTotal();
    }
    
    public function toggleUsePoints()
    {
        $this->usePoints = !$this->usePoints;
        if (!$this->usePoints) {
            $this->pointsToUse = 0;
        }
        $this->calculateTotal();
    }
    
    public function updatedPointsToUse()
    {
        $this->calculateTotal();
    }
    
    private function calculatePointsEarned()
    {
        if (!$this->selectedCustomerId) {
            $this->pointsEarned = 0;
            return;
        }
        
        $customer = Customer::find($this->selectedCustomerId);
        if (!$customer) {
            $this->pointsEarned = 0;
            return;
        }
        
        // Base points: 1 point per Rp 10,000
        $basePoints = floor($this->total / 10000);
        
        // Apply tier multiplier
        $multiplier = $customer->getPointMultiplier();
        $this->pointsEarned = floor($basePoints * $multiplier);
    }
    
    public function completeTransaction()
    {
        if (empty($this->cart)) {
            $this->dispatch('error', 'Cart is empty!');
            return;
        }
        
        if ($this->cash < $this->total) {
            $this->dispatch('error', 'Insufficient payment!');
            return;
        }
        
        DB::beginTransaction();
        
        try {
            // Create transaction
            $transaction = Transaction::create([
                'branch_id' => Auth::user()->branch_id,
                'user_id' => Auth::id(),
                'customer_id' => $this->selectedCustomerId,
                'points_used' => $this->usePoints ? $this->pointsToUse : 0,
                'points_discount' => $this->pointsDiscount,
                'total' => $this->total,
                'cash' => $this->cash,
                'change' => $this->change,
                'payment_method' => 'cash',
                'status' => 'completed',
            ]);
            
            // Create transaction items and update stock
            foreach ($this->cart as $item) {
                $product = Product::find($item['id']);
                
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'cost' => $product ? $product->cost : 0,
                ]);
                
                // Decrease stock
                $stock = Stock::where('product_id', $item['id'])
                             ->where('branch_id', Auth::user()->branch_id)
                             ->first();
                
                if ($stock) {
                    $stock->quantity -= $item['quantity'];
                    $stock->save();
                }
            }
            
            // Handle customer points
            if ($this->selectedCustomerId) {
                $customer = Customer::find($this->selectedCustomerId);
                if ($customer) {
                    // Redeem points if used
                    if ($this->usePoints && $this->pointsToUse > 0) {
                        $customer->redeemPoints(
                            $this->pointsToUse,
                            'Redeemed for transaction #' . $transaction->id,
                            $transaction->id
                        );
                    }
                    
                    // Add earned points
                    if ($this->pointsEarned > 0) {
                        $customer->addPoints(
                            $this->pointsEarned,
                            'earn',
                            'Earned from transaction #' . $transaction->id,
                            $transaction->id
                        );
                    }
                    
                    // Update customer stats
                    $customer->updateStats($this->total);
                }
            }
            
            DB::commit();
            
            // Log transaction activity
            $itemCount = count($this->cart);
            $itemsList = array_map(function($item) {
                return $item['name'] . ' (x' . $item['quantity'] . ')';
            }, $this->cart);
            
            $branch = \App\Models\Branch::find(Auth::user()->branch_id);
            
            ActivityLogger::log(
                'created',
                auth()->user()->name . ' completed a transaction of Rp ' . number_format($this->total, 0, ',', '.') . 
                ' with ' . $itemCount . ' item(s) at ' . ($branch ? $branch->name : 'Unknown Branch'),
                Transaction::class,
                $transaction->id,
                [
                    'transaction_id' => $transaction->id,
                    'branch' => $branch ? $branch->name : 'Unknown',
                    'items' => $itemsList,
                    'item_count' => $itemCount,
                    'subtotal' => $this->subtotal,
                    'tax' => $this->tax,
                    'total' => $this->total,
                    'cash' => $this->cash,
                    'change' => $this->change,
                ]
            );
            
            $this->dispatch('success', 'Transaction completed successfully!');
            
            // Reset cart
            $this->cart = [];
            $this->cash = 0;
            $this->calculateTotal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', 'Transaction failed: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        $query = $this->getProductQuery();
        $totalCount = $query->count();
        $products = $query->take($this->limit)->get();
        
        // Get customers for search
        $customers = [];
        if ($this->customerSearch) {
            $customers = Customer::where('status', 'active')
                ->where(function($q) {
                    $q->where('name', 'like', '%' . $this->customerSearch . '%')
                      ->orWhere('phone', 'like', '%' . $this->customerSearch . '%')
                      ->orWhere('card_number', 'like', '%' . $this->customerSearch . '%');
                })
                ->limit(5)
                ->get();
        }
        
        $selectedCustomer = $this->selectedCustomerId ? Customer::find($this->selectedCustomerId) : null;

        return view('livewire.cashier.pos-interface', [
            'products' => $products,
            'totalCount' => $totalCount,
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
        ])->layout('layouts.app');
    }
}
