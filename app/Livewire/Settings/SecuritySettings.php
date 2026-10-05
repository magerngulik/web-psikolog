<?php

namespace App\Livewire\Settings;

use App\Models\SecurityKey;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class SecuritySettings extends Component
{
    public $current_pin = '';
    public $new_pin = '';
    public $new_pin_confirmation = '';

    public function updatePin()
    {
        $this->validate([
            'current_pin' => 'required|digits:6',
            'new_pin' => 'required|digits:6|different:current_pin',
            'new_pin_confirmation' => 'required|same:new_pin',
        ]);

        $activeKey = SecurityKey::where('is_active', true)->first();

        if (!$activeKey || !Hash::check($this->current_pin, $activeKey->pin_hash)) {
            $this->addError('current_pin', 'PIN lama yang Anda masukkan salah.');
            return;
        }

        $activeKey->update([
            'pin_hash' => Hash::make($this->new_pin),
        ]);

        $this->reset(['current_pin', 'new_pin', 'new_pin_confirmation']);
        session()->flash('message', 'PIN Security Key berhasil diperbarui!');
    }

    public function lockNow()
    {
        session()->forget('pin_unlocked');
        return redirect()->route('lock-screen');
    }

    public function render()
    {
        return view('livewire.settings.security-settings')->layout('components.layouts.app');
    }
}
