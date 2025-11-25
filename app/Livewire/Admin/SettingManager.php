<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class SettingManager extends Component
{
    public $business_name;
    public $tax_rate;

    public function mount()
    {
        $this->business_name = Setting::getValue('business_name', 'POS Pro');
        $this->tax_rate = Setting::getValue('tax_rate', 10);
    }

    public function save()
    {
        $this->validate([
            'business_name' => 'required|string|max:255',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        Setting::updateOrCreate(['key' => 'business_name'], ['value' => $this->business_name]);
        Setting::updateOrCreate(['key' => 'tax_rate'], ['value' => $this->tax_rate]);

        $this->dispatch('success', 'Settings updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.setting-manager')->layout('layouts.app');
    }
}
