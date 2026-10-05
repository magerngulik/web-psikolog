<?php

namespace App\Livewire;

use App\Models\SecurityKey;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class LockScreen extends Component
{
    public $pin = '';
    public $errorMessage = '';

    public function addNumber($num)
    {
        if (strlen($this->pin) < 6) {
            $this->pin .= $num;
        }

        if (strlen($this->pin) === 6) {
            $this->verifyPin();
        }
    }

    public function deleteNumber()
    {
        $this->pin = substr($this->pin, 0, -1);
        $this->errorMessage = '';
    }

    public function verifyPin()
    {
        $activeKey = SecurityKey::where('is_active', true)->first();

        if ($activeKey && Hash::check($this->pin, $activeKey->pin_hash)) {
            session()->put('pin_unlocked', true);
            return redirect()->route('dashboard');
        } else {
            $this->errorMessage = 'PIN Security Key salah. Silakan coba lagi.';
            $this->pin = '';
        }
    }

    public function render()
    {
        return view('livewire.lock-screen')->layout('components.layouts.guest');
    }
}
