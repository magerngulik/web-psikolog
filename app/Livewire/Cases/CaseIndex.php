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

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function deleteCase($id)
    {
        $case = MedicalCase::withCount('sessions')->findOrFail($id);

        if ($case->sessions_count > 0) {
            $this->dispatch('notify', 
                message: "⚠️ Kasus '{$case->title}' tidak dapat dihapus karena sudah memiliki {$case->sessions_count} sesi konseling aktif. Batalkan status kasus atau hapus sesi terlebih dahulu.",
                type: 'error'
            );
            return;
        }

        $caseTitle = $case->title;
        $case->delete();

        $this->dispatch('notify', 
            message: "Kasus '{$caseTitle}' berhasil dipindahkan ke Tong Sampah.",
            type: 'success'
        );
    }

    public function restoreCase($id)
    {
        $case = MedicalCase::onlyTrashed()->findOrFail($id);
        $case->restore();

        $this->dispatch('notify', 
            message: "Kasus '{$case->title}' berhasil dipulihkan.",
            type: 'success'
        );
    }

    public function forceDeleteCase($id)
    {
        $case = MedicalCase::onlyTrashed()->withCount('sessions')->findOrFail($id);

        if ($case->sessions_count > 0) {
            $this->dispatch('notify', 
                message: "Kasus tidak dapat dihapus permanen karena masih memiliki relasi sesi.",
                type: 'error'
            );
            return;
        }

        $caseTitle = $case->title;
        $case->forceDelete();

        $this->dispatch('notify', 
            message: "Kasus '{$caseTitle}' berhasil dihapus secara permanen.",
            type: 'success'
        );
    }

    public function render()
    {
        $query = $this->statusFilter === 'trashed'
            ? MedicalCase::onlyTrashed()
            : MedicalCase::query();

        $cases = $query
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
            ->when($this->statusFilter && $this->statusFilter !== 'trashed', function ($query) {
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
