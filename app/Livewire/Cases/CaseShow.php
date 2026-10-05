<?php

namespace App\Livewire\Cases;

use App\Models\MedicalCase;
use Livewire\Component;

class CaseShow extends Component
{
    public MedicalCase $case;
    public $progressNote = '';

    public function mount($id)
    {
        $this->case = MedicalCase::with(['client', 'sessions' => function ($query) {
            $query->orderBy('session_number', 'asc');
        }])->findOrFail($id);

        $this->progressNote = $this->case->progress_note;
    }

    public function updateProgress()
    {
        $this->case->update([
            'progress_note' => $this->progressNote,
        ]);

        session()->flash('message', 'Catatan perkembangan kasus berhasil diperbarui.');
    }

    public function updateStatus($newStatus)
    {
        $this->case->update([
            'status' => $newStatus,
        ]);

        session()->flash('message', 'Status kasus berhasil diubah menjadi ' . strtoupper($newStatus));
    }

    public function render()
    {
        return view('livewire.cases.case-show')->layout('components.layouts.app');
    }
}
