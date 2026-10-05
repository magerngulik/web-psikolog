<?php

namespace App\Livewire\Cases;

use App\Models\MedicalCase;
use Livewire\Component;
use Livewire\WithPagination;

class CaseIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';

    protected $queryString = ['search', 'statusFilter', 'categoryFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $cases = MedicalCase::query()
            ->with(['client'])
            ->withCount('sessions')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('complaint', 'like', '%' . $this->search . '%')
                    ->orWhereHas('client', function ($q) {
                        $q->where('full_name', 'like', '%' . $this->search . '%')
                          ->orWhere('client_code', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.cases.case-index', [
            'cases' => $cases
        ])->layout('components.layouts.app');
    }
}
