<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ClientShow extends Component
{
    public Client $client;

    public function mount($id)
    {
        $this->client = Client::with(['cases.sessions'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.clients.client-show')->layout('components.layouts.app');
    }
}
