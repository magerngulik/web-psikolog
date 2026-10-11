<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;
use Livewire\WithPagination;

class SessionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';

    protected $queryString = ['search', 'statusFilter', 'dateFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function deleteSession($id)
    {
        $session = Session::findOrFail($id);

        if ($session->is_locked) {
            $this->dispatch('notify', 
                message: "⚠️ Sesi ke-{$session->session_number} sudah TERKUNCI secara final dan tidak dapat dihapus demi kepatuhan rekam medis klinis.",
                type: 'error'
            );
            return;
        }

        $sessionNumber = $session->session_number;
        $session->delete();

        $this->dispatch('notify', 
            message: "Sesi ke-{$sessionNumber} berhasil dipindahkan ke Tong Sampah.",
            type: 'success'
        );
    }

    public function restoreSession($id)
    {
        $session = Session::onlyTrashed()->findOrFail($id);
        $session->restore();

        $this->dispatch('notify', 
            message: "Sesi ke-{$session->session_number} berhasil dipulihkan.",
            type: 'success'
        );
    }

    public function forceDeleteSession($id)
    {
        $session = Session::onlyTrashed()->findOrFail($id);
        $sessionNumber = $session->session_number;

        // Delete associated note if exists
        $session->sessionNote()->delete();
        $session->forceDelete();

        $this->dispatch('notify', 
            message: "Sesi ke-{$sessionNumber} berhasil dihapus secara permanen.",
            type: 'success'
        );
    }

    public function render()
    {
        $query = $this->statusFilter === 'trashed'
            ? Session::onlyTrashed()
            : Session::query();

        $sessions = $query
            ->with(['medicalCase.client'])
            ->when($this->search, function ($query) {
                $query->whereHas('medicalCase.client', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('client_code', 'like', '%' . $this->search . '%');
                })->orWhereHas('medicalCase', function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter && $this->statusFilter !== 'trashed', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('session_date', $this->dateFilter);
            })
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('livewire.sessions.session-index', [
            'sessions' => $sessions
        ])->layout('components.layouts.app');
    }
}
