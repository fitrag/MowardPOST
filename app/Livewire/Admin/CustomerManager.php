<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\CustomerPoint;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class CustomerManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showCardModal = false;
    public $showPointModal = false;
    public $customerId = null;
    public $selectedCustomer = null;

    // Form fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $date_of_birth = '';
    public $gender = '';
    public $status = 'active';
    public $notes = '';

    // Point adjustment
    public $pointAmount = 0;
    public $pointDescription = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:customers,email',
        'phone' => 'required|string|unique:customers,phone',
        'address' => 'nullable|string',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female,other',
        'status' => 'required|in:active,inactive,blocked',
        'notes' => 'nullable|string',
    ];

    public function updated($propertyName)
    {
        if ($propertyName === 'search') {
            $this->resetPage();
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->date_of_birth = $customer->date_of_birth?->format('Y-m-d');
        $this->gender = $customer->gender;
        $this->status = $customer->status;
        $this->notes = $customer->notes;
        
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->customerId) {
            $this->rules['email'] = 'nullable|email|unique:customers,email,' . $this->customerId;
            $this->rules['phone'] = 'required|string|unique:customers,phone,' . $this->customerId;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->customerId) {
            // Update
            $customer = Customer::findOrFail($this->customerId);
            $customer->update($data);
            
            $this->dispatch('success', message: 'Customer updated successfully!');
        } else {
            // Create
            $data['card_number'] = Customer::generateCardNumber();
            $data['member_since'] = now();
            $data['member_tier'] = 'silver';
            
            $customer = Customer::create($data);
            
            // Give first transaction bonus
            $customer->addPoints(100, 'first_transaction', 'Welcome bonus for new member');
            
            $this->dispatch('success', message: 'Customer created successfully with 100 welcome bonus points!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Customer::findOrFail($id)->delete();
        $this->dispatch('success', message: 'Customer deleted successfully!');
    }

    public function viewCard($id)
    {
        $this->selectedCustomer = Customer::with('pointHistory')->findOrFail($id);
        $this->showCardModal = true;
    }

    public function openPointModal($id)
    {
        $this->selectedCustomer = Customer::findOrFail($id);
        $this->pointAmount = 0;
        $this->pointDescription = '';
        $this->showPointModal = true;
    }

    public function adjustPoints()
    {
        $this->validate([
            'pointAmount' => 'required|integer|not_in:0',
            'pointDescription' => 'required|string|max:255',
        ]);

        if ($this->pointAmount > 0) {
            $this->selectedCustomer->addPoints(
                $this->pointAmount,
                'adjustment',
                $this->pointDescription
            );
        } else {
            $this->selectedCustomer->redeemPoints(
                abs($this->pointAmount),
                $this->pointDescription
            );
        }

        $this->dispatch('success', message: 'Points adjusted successfully!');
        $this->showPointModal = false;
        $this->selectedCustomer = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeCardModal()
    {
        $this->showCardModal = false;
        $this->selectedCustomer = null;
    }

    public function closePointModal()
    {
        $this->showPointModal = false;
        $this->selectedCustomer = null;
    }

    private function resetForm()
    {
        $this->customerId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->date_of_birth = '';
        $this->gender = '';
        $this->status = 'active';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('card_number', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.customer-manager', [
            'customers' => $customers,
        ])->layout('layouts.app');
    }
}
