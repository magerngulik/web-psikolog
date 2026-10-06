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
    public $subjective_complaint = '';
    public $subjective_problem = '';
    public $complaint = '';
    public $goal = '';
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
        'subjective_complaint' => 'nullable|string',
        'subjective_problem' => 'nullable|string',
        'complaint' => 'nullable|string',
        'goal' => 'nullable|string',
        'status' => 'required|in:active,on_hold,completed,cancelled',
    ];

    public function save()
    {
        // Sinkronisasi data keluhan utama dan komplain
        if (empty($this->subjective_complaint) && !empty($this->complaint)) {
            $this->subjective_complaint = $this->complaint;
        }
        if (empty($this->complaint) && !empty($this->subjective_complaint)) {
            $this->complaint = $this->subjective_complaint;
        }

        $validatedData = $this->validate();

        $count = MedicalCase::count() + 1;
        $validatedData['case_code'] = 'CAS-' . date('Ym') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $validatedData['start_date'] = now()->toDateString();
        $validatedData['subjective_complaint'] = $this->subjective_complaint;
        $validatedData['subjective_problem'] = $this->subjective_problem;
        $validatedData['complaint'] = $this->complaint ?: $this->subjective_complaint;
        $validatedData['goal'] = $this->goal ?: $this->subjective_problem;

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
