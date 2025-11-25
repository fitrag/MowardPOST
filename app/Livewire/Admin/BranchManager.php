<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use Livewire\Component;
use App\Helpers\ActivityLogger;

class BranchManager extends Component
{
    public $branches;
    public $name, $address, $phone;
    public $branchId;
    public $editMode = false;
    public $showModal = false;

    public function render()
    {
        $this->branches = Branch::all();
        return view('livewire.admin.branch-manager')->layout('layouts.app');
    }

    public function create()
    {
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
        $this->address = '';
        $this->phone = '';
        $this->branchId = null;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'address' => 'nullable',
            'phone' => 'nullable',
        ]);

        $branch = Branch::updateOrCreate(['id' => $this->branchId], [
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
        ]);

        // Log activity
        if ($this->branchId) {
            ActivityLogger::logUpdate(
                Branch::class,
                $branch->id,
                $branch->name
            );
        } else {
            ActivityLogger::logCreate(
                Branch::class,
                $branch->id,
                $branch->name
            );
        }

        $this->dispatch('success', $this->branchId ? 'Branch updated successfully!' : 'Branch created successfully!');

        $this->closeModal();
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        $this->branchId = $id;
        $this->name = $branch->name;
        $this->address = $branch->address;
        $this->phone = $branch->phone;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $branch = Branch::find($id);
        $branchName = $branch->name;
        
        $branch->delete();
        
        // Log activity
        ActivityLogger::logDelete(
            Branch::class,
            $id,
            $branchName
        );
        
        $this->dispatch('success', 'Branch deleted successfully!');
    }
}
