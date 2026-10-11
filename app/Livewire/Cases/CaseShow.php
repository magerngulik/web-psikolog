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

    public function deleteCase()
    {
        if ($this->case->sessions()->count() > 0) {
            $this->dispatch('notify', 
                message: "⚠️ Kasus '{$this->case->title}' tidak dapat dihapus karena sudah memiliki sesi konseling aktif. Batalkan status kasus atau hapus sesi terlebih dahulu.",
                type: 'error'
            );
            return;
        }

        $title = $this->case->title;
        $this->case->delete();

        session()->flash('message', "Kasus '{$title}' berhasil dipindahkan ke Tong Sampah.");
        return redirect()->route('cases.index');
    }

    public function deleteSession($sessionId)
    {
        $session = $this->case->sessions()->findOrFail($sessionId);

        if ($session->is_locked) {
            $this->dispatch('notify', 
                message: "⚠️ Sesi ke-{$session->session_number} sudah TERKUNCI secara final dan tidak dapat dihapus demi kepatuhan rekam medis klinis.",
                type: 'error'
            );
            return;
        }

        $sessionNumber = $session->session_number;
        $session->delete();

        // Refresh sessions relationship on the case
        $this->case->load(['sessions' => function ($query) {
            $query->orderBy('session_number', 'asc');
        }]);

        $this->dispatch('notify', 
            message: "Sesi ke-{$sessionNumber} berhasil dipindahkan ke Tong Sampah.",
            type: 'success'
        );
    }

    public function render()
    {
        return view('livewire.cases.case-show')->layout('components.layouts.app');
    }
}
