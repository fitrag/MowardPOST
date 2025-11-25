<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Login extends Component
{
    public $email = '';
    public $password = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();

            // Log the login activity
            ActivityLogger::logLogin();

            // Redirect cashiers directly to POS
            if (Auth::user()->hasRole('cashier')) {
                return redirect()->intended(route('pos'));
            }

            return redirect()->intended(route('dashboard'));
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        $businessName = \App\Models\Setting::getValue('business_name', 'POS Pro');
        return view('livewire.auth.login', compact('businessName'))->layout('layouts.guest');
    }
}
