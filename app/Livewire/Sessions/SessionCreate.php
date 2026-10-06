<?php

namespace App\Livewire\Sessions;

use App\Models\MedicalCase;
use App\Models\Session;
use Livewire\Component;

class SessionCreate extends Component
{
    public $medical_case_id;
    public $session_number = 1;
    public $session_date;
    public $start_time = '09:00';
    public $end_time = '10:00';
    public $fee = 250000;
    public $status = 'scheduled';
    public $payment_status = 'unpaid';
    public $payment_method = 'cash';
    public $service_modality = 'individual_direct';
    public bool $is_high_risk = false;
    public bool $generates_report = true;

    public function mount()
    {
        $this->medical_case_id = request()->query('case_id', $this->medical_case_id);
        $this->session_date = date('Y-m-d');
        
        if ($this->medical_case_id) {
            $this->calculateSessionNumber();
        }
    }

    public function updatedMedicalCaseId()
    {
        $this->calculateSessionNumber();
    }

    private function calculateSessionNumber()
    {
        $lastSession = Session::where(function ($q) {
            $q->where('medical_case_id', $this->medical_case_id)
              ->orWhere('case_id', $this->medical_case_id);
        })->max('session_number');

        $this->session_number = ($lastSession ?? 0) + 1;
    }

    protected function rules()
    {
        return [
            'medical_case_id' => 'required|exists:cases,id',
            'session_number' => 'required|integer|min:1',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'fee' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,confirmed,in_progress,done,cancelled,no_show,rescheduled',
            'payment_status' => 'required|in:unpaid,paid,waived',
            'payment_method' => 'required|in:cash,transfer,qris',
            'service_modality' => 'required|string|in:individual_direct,individual_virtual,group,phone,chat_text,legal_visum,legal_witness,legal_court_report',
            'is_high_risk' => 'boolean',
            'generates_report' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        // 🛡️ VALIDASI ANTI-OVERLAP WAKTU
        $overlapCount = Session::where('session_date', $this->session_date)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) {
                $query->whereBetween('start_time', [$this->start_time, $this->end_time])
                    ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                    ->orWhere(function ($q) {
                        $q->where('start_time', '<=', $this->start_time)
                          ->where('end_time', '>=', $this->end_time);
                    });
            })->count();

        if ($overlapCount > 0) {
            $this->addError('start_time', '⚠️ WARNING: Terjadi bentrok jadwal dengan sesi konsultasi lain pada tanggal dan jam tersebut!');
            return;
        }

        $session = Session::create([
            'case_id' => $this->medical_case_id,
            'medical_case_id' => $this->medical_case_id,
            'session_number' => $this->session_number,
            'session_date' => $this->session_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'fee' => $this->fee,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'service_modality' => $this->service_modality,
            'is_high_risk' => $this->is_high_risk,
            'generates_report' => $this->generates_report,
            'is_locked' => false,
        ]);

        session()->flash('message', 'Sesi konsultasi berhasil dijadwalkan!');
        return redirect()->route('sessions.show', $session->id);
    }

    public function render()
    {
        $cases = MedicalCase::with('client')
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('livewire.sessions.session-create', [
            'cases' => $cases
        ])->layout('components.layouts.app');
    }
}
