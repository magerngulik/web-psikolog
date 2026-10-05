<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ClientCreate extends Component
{
    public $full_name;
    public $nickname;
    public $gender = 'female';
    public $date_of_birth;
    public $phone_number;
    public $email;
    public $address;
    public $occupation;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $emergency_relation;

    protected $rules = [
        'full_name' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'required|in:male,female,other',
        'date_of_birth' => 'nullable|date',
        'phone_number' => 'nullable|string|max:30',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        'occupation' => 'nullable|string|max:100',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:30',
        'emergency_relation' => 'nullable|string|max:50',
    ];

    public function save()
    {
        $validatedData = $this->validate();
        
        // Generate Client Code sederhana (contoh: CLI-202610-001)
        $count = Client::count() + 1;
        $validatedData['client_code'] = 'CLI-' . date('Ym') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $client = Client::create($validatedData);

        session()->flash('message', 'Klien berhasil didaftarkan!');
        return redirect()->route('clients.show', $client->id);
    }

    public function render()
    {
        return view('livewire.clients.client-create')->layout('components.layouts.app');
    }
}
