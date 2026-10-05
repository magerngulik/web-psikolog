<?php

namespace App\Livewire\Cases;

use App\Models\Client;
use App\Models\MedicalCase;
use Livewire\Component;

class CaseCreate extends Component
{
    public $client_id;
    public $title;
    public $category = 'Anxiety';
    public $complaint;
    public $goal;
    public $status = 'active';

    public function mount()
    {
        // Terima client_id via query string jika ada (misal dari tombol halaman profil klien)
        $this->client_id = request()->query('client_id', $this->client_id);
    }

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'title' => 'required|string|max:255',
        'category' => 'required|string|max:100',
        'complaint' => 'nullable|string',
        'goal' => 'nullable|string',
        'status' => 'required|in:active,on_hold,completed,cancelled',
    ];

    public function save()
    {
        $validatedData = $this->validate();

        $count = MedicalCase::count() + 1;
        $validatedData['case_code'] = 'CAS-' . date('Ym') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $validatedData['start_date'] = now()->toDateString();

        $case = MedicalCase::create($validatedData);

        session()->flash('message', 'Kasus medis baru berhasil didaftarkan!');
        return redirect()->route('cases.show', $case->id);
    }

    public function render()
    {
        $clients = Client::latest()->get(['id', 'full_name', 'client_code']);

        return view('livewire.cases.case-create', [
            'clients' => $clients
        ])->layout('components.layouts.app');
    }
}
