<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;

class SessionShow extends Component
{
    public Session $session;

    public $summary = '';
    public $dynamic_notes = '';
    public $intervention_notes = '';
    public $recommendation = '';
    public $status;
    public $payment_status;
    public $payment_method;
    public $fee;

    public function mount($id)
    {
        $this->session = Session::with(['medicalCase.client'])->findOrFail($id);

        $this->summary = $this->session->summary;
        $this->dynamic_notes = $this->session->dynamic_notes;
        $this->intervention_notes = $this->session->intervention_notes;
        $this->recommendation = $this->session->recommendation;
        $this->status = $this->session->status;
        $this->payment_status = $this->session->payment_status;
        $this->payment_method = $this->session->payment_method;
        $this->fee = $this->session->fee;
    }

    public function saveNotes()
    {
        if ($this->session->is_locked) {
            session()->flash('error', '⚠️ Rekam medis sesi ini sudah TERKUNCI dan tidak dapat diubah lagi.');
            return;
        }

        $this->session->update([
            'summary' => $this->summary,
            'dynamic_notes' => $this->dynamic_notes,
            'intervention_notes' => $this->intervention_notes,
            'recommendation' => $this->recommendation,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'fee' => $this->fee,
        ]);

        session()->flash('message', 'Catatan rekam medis sesi berhasil diperbarui!');
    }

    public function lockSession()
    {
        if ($this->session->is_locked) {
            return;
        }

        $this->session->update([
            'is_locked' => true,
            'locked_at' => now(),
            'status' => 'done',
        ]);

        $this->status = 'done';
        session()->flash('message', '🔒 Sesi konsultasi & Catatan Rekam Medis RESMI DIKUNCI!');
    }

    public function render()
    {
        return view('livewire.sessions.session-show')->layout('components.layouts.app');
    }
}
