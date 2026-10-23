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
    public $nik = '';
    public $last_education = '';
    public $birth_order = null;
    public $total_siblings = null;
    public bool $is_disabled = false;
    public $disability_description = '';
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
        'nik' => 'nullable|string|max:30',
        'last_education' => 'nullable|string|max:100',
        'birth_order' => 'nullable|integer|min:1|max:50',
        'total_siblings' => 'nullable|integer|min:1|max:50',
        'is_disabled' => 'boolean',
        'disability_description' => 'nullable|string|max:255',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:30',
        'emergency_relation' => 'nullable|string|max:50',
    ];

    public function save()
    {
        if ($this->birth_order === '') $this->birth_order = null;
        if ($this->total_siblings === '') $this->total_siblings = null;

        $validatedData = $this->validate();
        
        $validatedData['nik'] = !empty(trim((string) $this->nik)) ? trim((string) $this->nik) : null;
        $validatedData['last_education'] = !empty(trim((string) $this->last_education)) ? trim((string) $this->last_education) : null;
        $validatedData['birth_order'] = !empty($this->birth_order) ? (int) $this->birth_order : null;
        $validatedData['total_siblings'] = !empty($this->total_siblings) ? (int) $this->total_siblings : null;
        $validatedData['disability_description'] = ($this->is_disabled && !empty(trim((string) $this->disability_description))) 
            ? trim((string) $this->disability_description) 
            : null;

        $client = Client::create($validatedData);

        session()->flash('message', 'Klien berhasil didaftarkan!');
        return redirect()->route('clients.show', $client->id);
    }

    public function render()
    {
        return view('livewire.clients.client-create')->layout('components.layouts.app');
    }
}
