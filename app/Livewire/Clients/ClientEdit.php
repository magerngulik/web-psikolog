<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ClientEdit extends Component
{
    public Client $client;

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

    public function mount($id)
    {
        $this->client = Client::findOrFail($id);

        $this->full_name = $this->client->full_name;
        $this->nickname = $this->client->nickname;
        $this->gender = $this->client->gender ?? 'female';
        $this->date_of_birth = $this->client->date_of_birth 
            ? \Carbon\Carbon::parse($this->client->date_of_birth)->format('Y-m-d') 
            : null;
        $this->phone_number = $this->client->phone_number;
        $this->email = $this->client->email;
        $this->address = $this->client->address;
        $this->occupation = $this->client->occupation;
        $this->nik = $this->client->nik ?? '';
        $this->last_education = $this->client->last_education ?? '';
        $this->birth_order = $this->client->birth_order;
        $this->total_siblings = $this->client->total_siblings;
        $this->is_disabled = (bool) $this->client->is_disabled;
        $this->disability_description = $this->client->disability_description ?? '';
        $this->emergency_contact_name = $this->client->emergency_contact_name;
        $this->emergency_contact_phone = $this->client->emergency_contact_phone;
        $this->emergency_relation = $this->client->emergency_relation;
    }

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

        $this->client->update($validatedData);

        session()->flash('message', 'Data profil klien berhasil diperbarui!');
        return redirect()->route('clients.show', $this->client->id);
    }

    public function render()
    {
        return view('livewire.clients.client-edit')->layout('components.layouts.app');
    }
}
