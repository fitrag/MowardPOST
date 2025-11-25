<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;

class ProductManager extends Component
{
    use WithFileUploads;

    public $products, $categories, $branches;
    public $name, $sku, $price, $cost, $image, $category_id, $is_active = true;
    public $selectedBranches = [];
    public $branchStocks = [];
    public $productId;
    public $editMode = false;
    public $showModal = false;
    public $oldImage;
    public $search = '';
    public $perPage = 20;
    public $hasMore = false;

    public function render()
    {
        $query = Product::with('category');
        
        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }
        
        // Get total count for hasMore check
        $totalCount = $query->count();
        
        // Apply pagination
        $this->products = $query->take($this->perPage)->get();
        
        // Check if there are more items
        $this->hasMore = $totalCount > $this->perPage;
        
        $this->categories = Category::all();
        
        // Filter branches based on user role
        // Cashiers can only select their own branch
        if (auth()->user()->hasRole('cashier')) {
            $this->branches = \App\Models\Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            // Owners and managers can see all branches
            $this->branches = \App\Models\Branch::all();
        }
        
        return view('livewire.admin.product-manager')->layout('layouts.app');
    }

    public function updatedSearch()
    {
        $this->perPage = 20; // Reset pagination when searching
    }

    public function loadMore()
    {
        $this->perPage += 20;
    }

    public function create()
    {
        // Check create permission
        if (!auth()->user()->canCreateProduct()) {
            $this->dispatch('error', 'You do not have permission to create products.');
            return;
        }

        $this->resetInputFields();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->sku = '';
        $this->price = '';
        $this->cost = '';
        $this->image = null;
        $this->oldImage = null;
        $this->category_id = '';
        $this->is_active = true;
        $this->selectedBranches = [];
        $this->branchStocks = [];
        $this->productId = null;
    }

    public function save()
    {
        // Check permissions
        if ($this->editMode && !auth()->user()->canUpdateProduct()) {
            $this->dispatch('error', 'You do not have permission to update products.');
            return;
        }

        if (!$this->editMode && !auth()->user()->canCreateProduct()) {
            $this->dispatch('error', 'You do not have permission to create products.');
            return;
        }
        $this->validate([
            'name' => 'required',
            'sku' => 'nullable|unique:products,sku,' . $this->productId,
            'price' => 'required|numeric',
            'cost' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $imagePath = $this->oldImage;
        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        // Clean formatted price inputs (remove dots for thousand separator)
        $cleanPrice = str_replace('.', '', $this->price);
        $cleanCost = $this->cost ? str_replace('.', '', $this->cost) : null;

        // Auto-generate SKU if empty
        if (empty($this->sku)) {
            $this->sku = 'SKU-' . strtoupper(uniqid());
        }

        // Get old values for comparison if editing
        $oldValues = [];
        if ($this->productId) {
            $oldProduct = Product::find($this->productId);
            $oldValues = [
                'name' => $oldProduct->name,
                'sku' => $oldProduct->sku,
                'price' => $oldProduct->price,
                'cost' => $oldProduct->cost,
                'category_id' => $oldProduct->category_id,
                'is_active' => $oldProduct->is_active,
            ];
        }

        $product = Product::updateOrCreate(['id' => $this->productId], [
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $cleanPrice,
            'cost' => $cleanCost,
            'image' => $imagePath,
            'category_id' => $this->category_id ?: null,
            'is_active' => $this->is_active,
        ]);

        // Delete old image if new one uploaded
        if ($this->image && $this->oldImage && $this->oldImage !== $imagePath) {
            Storage::disk('public')->delete($this->oldImage);
        }

        // If editing, delete stock for unchecked branches
        if ($this->productId) {
            $existingStocks = \App\Models\Stock::where('product_id', $product->id)
                ->pluck('branch_id')
                ->toArray();
            
            $branchesToDelete = array_diff($existingStocks, $this->selectedBranches);
            
            if (!empty($branchesToDelete)) {
                \App\Models\Stock::where('product_id', $product->id)
                    ->whereIn('branch_id', $branchesToDelete)
                    ->delete();
            }
        }

        // Create or update stock for selected branches
        foreach ($this->selectedBranches as $branchId) {
            $quantity = $this->branchStocks[$branchId] ?? 0;
            
            // Get old stock quantity if exists
            $oldStock = \App\Models\Stock::where('product_id', $product->id)
                ->where('branch_id', $branchId)
                ->first();
            $oldQuantity = $oldStock ? $oldStock->quantity : 0;
            
            $stock = \App\Models\Stock::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'branch_id' => $branchId,
                ],
                [
                    'quantity' => $quantity,
                ]
            );
            
            // Log stock change if quantity changed
            if ($oldQuantity != $quantity) {
                $branch = \App\Models\Branch::find($branchId);
                ActivityLogger::log(
                    'updated',
                    auth()->user()->name . ' updated stock for ' . $product->name . 
                    ' at ' . $branch->name . 
                    ' from ' . $oldQuantity . ' to ' . $quantity,
                    \App\Models\Stock::class,
                    $stock->id,
                    [
                        'product' => $product->name,
                        'branch' => $branch->name,
                        'old_quantity' => $oldQuantity,
                        'new_quantity' => (int)$quantity
                    ]
                );
            }
        }

        // Log activity
        if ($this->productId) {
            // Track what changed
            $changes = [];
            $newValues = [
                'name' => $this->name,
                'sku' => $this->sku,
                'price' => $cleanPrice,
                'cost' => $cleanCost,
                'category_id' => $this->category_id ?: null,
                'is_active' => $this->is_active,
            ];

            foreach ($newValues as $field => $newValue) {
                if (isset($oldValues[$field]) && $oldValues[$field] != $newValue) {
                    $changes[$field] = [
                        'old' => $oldValues[$field],
                        'new' => $newValue
                    ];
                }
            }

            ActivityLogger::logUpdate(
                Product::class,
                $product->id,
                $product->name
            );

            // Log detailed changes if any
            if (!empty($changes)) {
                ActivityLogger::log(
                    'updated',
                    auth()->user()->name . ' updated product ' . $product->name . ' details',
                    Product::class,
                    $product->id,
                    ['changes' => $changes]
                );
            }
        } else {
            ActivityLogger::logCreate(
                Product::class,
                $product->id,
                $product->name
            );
        }

        $this->dispatch('success', $this->productId ? 'Product updated successfully!' : 'Product created successfully!');

        $this->closeModal();
    }

    public function edit($id)
    {
        // Check update permission
        if (!auth()->user()->canUpdateProduct()) {
            $this->dispatch('error', 'You do not have permission to edit products.');
            return;
        }

        $product = Product::with('stocks')->findOrFail($id);
        $this->productId = $id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        // Format price with thousand separator for display
        $this->price = number_format($product->price, 0, '', '.');
        $this->cost = $product->cost ? number_format($product->cost, 0, '', '.') : null;
        $this->category_id = $product->category_id;
        $this->is_active = $product->is_active;
        $this->oldImage = $product->image;
        
        // Load existing stock data
        $stocks = \App\Models\Stock::where('product_id', $id)->get();
        $this->selectedBranches = $stocks->pluck('branch_id')->toArray();
        $this->branchStocks = $stocks->pluck('quantity', 'branch_id')->toArray();
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        // Check delete permission
        if (!auth()->user()->canDeleteProduct()) {
            $this->dispatch('error', 'You do not have permission to delete products.');
            return;
        }

        $product = Product::find($id);
        $productName = $product->name;
        
        $product->delete();
        
        // Log activity
        ActivityLogger::logDelete(
            Product::class,
            $id,
            $productName
        );
        
        $this->dispatch('success', 'Product deleted successfully!');
    }
}
