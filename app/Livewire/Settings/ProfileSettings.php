<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;

class ProfileSettings extends Component
{
    public $user_id;
    public $name = '';
    public $title_prefix = '';
    public $title_suffix = 'S.Psi., M.Psi., Psikolog';
    public $sipa_number = '503/123-SIPA/2026';
    public $practice_name = 'MANDIRI';
    public $practice_city = 'Selatpanjang';
    public $practice_address = '';
    public $phone = '';
    public $email = 'admin@example.com';

    public function mount()
    {
        $user = User::first();

        if ($user) {
            $this->user_id = $user->id;
            $this->name = $user->name ?? '';
            $this->title_prefix = $user->title_prefix ?? '';
            $this->title_suffix = $user->title_suffix ?? 'S.Psi., M.Psi., Psikolog';
            $this->sipa_number = $user->sipa_number ?? '503/123-SIPA/2026';
            $this->practice_name = $user->practice_name ?? 'MANDIRI';
            $this->practice_city = $user->practice_city ?? 'Selatpanjang';
            $this->practice_address = $user->practice_address ?? '';
            $this->phone = $user->phone ?? '';
            $this->email = $user->email ?? 'admin@example.com';
        } else {
            $this->name = 'Basirah';
        }
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'title_prefix' => 'nullable|string|max:50',
        'title_suffix' => 'nullable|string|max:100',
        'sipa_number' => 'required|string|max:100',
        'practice_name' => 'required|string|max:255',
        'practice_city' => 'required|string|max:100',
        'practice_address' => 'nullable|string',
        'phone' => 'nullable|string|max:30',
        'email' => 'required|email|max:255',
    ];

    public function save()
    {
        $this->validate();

        $user = User::first();

        if ($user) {
            $user->update([
                'name' => $this->name,
                'title_prefix' => $this->title_prefix,
                'title_suffix' => $this->title_suffix,
                'sipa_number' => $this->sipa_number,
                'practice_name' => $this->practice_name,
                'practice_city' => $this->practice_city,
                'practice_address' => $this->practice_address,
                'phone' => $this->phone,
                'email' => $this->email,
            ]);
        } else {
            User::create([
                'name' => $this->name,
                'title_prefix' => $this->title_prefix,
                'title_suffix' => $this->title_suffix,
                'sipa_number' => $this->sipa_number,
                'practice_name' => $this->practice_name,
                'practice_city' => $this->practice_city,
                'practice_address' => $this->practice_address,
                'phone' => $this->phone,
                'email' => $this->email,
                'password' => bcrypt('password'),
            ]);
        }

        session()->flash('message', 'Profil psikolog & data tempat praktik berhasil diperbarui!');
    }

    public function getFormattedNameProperty(): string
    {
        $prefix = $this->title_prefix ? trim($this->title_prefix) . ' ' : '';
        $suffix = $this->title_suffix ? ', ' . trim($this->title_suffix) : '';
        return $prefix . ($this->name ?: 'Nama Psikolog') . $suffix;
    }

    public function render()
    {
        return view('livewire.settings.profile-settings')->layout('components.layouts.app');
    }
}

