<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger;

class CategoryManager extends Component
{
    public $categories;
    public $name;
    public $slug; // Retaining slug as it's used later
    public $categoryId;
    public $editMode = false;
    public $showModal = false;

    public function render()
    {
        $this->categories = Category::all();
        return view('livewire.admin.category-manager')->layout('layouts.app');
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
        $this->slug = '';
        $this->categoryId = null;
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $this->categoryId,
        ]);

        $category = Category::updateOrCreate(['id' => $this->categoryId], [
            'name' => $this->name,
            'slug' => $this->slug,
        ]);

        // Log activity
        if ($this->categoryId) {
            ActivityLogger::logUpdate(
                Category::class,
                $category->id,
                $category->name
            );
        } else {
            ActivityLogger::logCreate(
                Category::class,
                $category->id,
                $category->name
            );
        }

        $this->dispatch('success', $this->categoryId ? 'Category updated successfully!' : 'Category created successfully!');

        $this->closeModal();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $category = Category::find($id);
        $categoryName = $category->name;
        
        $category->delete();
        
        // Log activity
        ActivityLogger::logDelete(
            Category::class,
            $id,
            $categoryName
        );
        
        $this->dispatch('success', 'Category deleted successfully!');
    }
}
