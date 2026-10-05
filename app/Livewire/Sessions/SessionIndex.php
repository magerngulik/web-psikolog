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

    public function render()
    {
        $sessions = Session::query()
            ->with(['medicalCase.client'])
            ->when($this->search, function ($query) {
                $query->whereHas('medicalCase.client', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('client_code', 'like', '%' . $this->search . '%');
                })->orWhereHas('medicalCase', function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
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
