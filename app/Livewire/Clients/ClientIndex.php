<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $genderFilter = '';

    protected $queryString = ['search', 'genderFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteClient($id)
    {
        $client = Client::findOrFail($id);
        $client->delete(); // Soft Delete

        session()->flash('message', 'Data klien berhasil dihapus.');
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                    ->orWhere('client_code', 'like', '%' . $this->search . '%');
            })
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            ->withCount('cases')
            ->latest()
            ->paginate(10);

        return view('livewire.clients.client-index', [
            'clients' => $clients
        ])->layout('components.layouts.app');
    }
}

