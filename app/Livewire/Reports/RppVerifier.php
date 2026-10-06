<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\SessionNote;

class RppVerifier extends Component
{
    public $token;
    public $note;
    public $isValid = false;

    public function mount($token)
    {
        $this->token = $token;
        $this->note = SessionNote::with(['appointment.patient', 'appointment.psychologist'])
            ->where('qr_code_token', $token)
            ->first();

        if ($this->note) {
            $this->isValid = true;
        }
    }

    public function render()
    {
        return view('livewire.reports.rpp-verifier')->layout('components.layouts.guest');
    }
}
