<?php

namespace App\Livewire\Activities;

use App\Models\Activity;
use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $typeFilter = 'all';
    public $organization_id = null;
    public $monthFilter;
    public $yearFilter;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'organization_id' => ['except' => null],
    ];

    public function mount()
    {
        $this->organization_id = request()->query('organization_id', $this->organization_id);
        $this->yearFilter = date('Y');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updateStatus($id, $status)
    {
        $activity = Activity::findOrFail($id);
        $activity->update(['status' => $status]);
        session()->flash('message', 'Status kegiatan ' . $activity->activity_code . ' berhasil diubah menjadi ' . ucfirst($status));
    }

    public function updatePaymentStatus($id, $paymentStatus)
    {
        $activity = Activity::findOrFail($id);
        $activity->update(['payment_status' => $paymentStatus]);
        session()->flash('message', 'Status pembayaran ' . $activity->activity_code . ' berhasil diubah.');
    }

    public function delete($id)
    {
        $activity = Activity::findOrFail($id);
        $title = $activity->title;
        $activity->delete();
        session()->flash('message', 'Kegiatan "' . $title . '" berhasil dihapus.');
    }

    public function render()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Vitals
        $eventsThisMonth = Activity::whereMonth('start_date', $currentMonth)
            ->whereYear('start_date', $currentYear)
            ->count();

        $audienceThisMonth = Activity::whereMonth('start_date', $currentMonth)
            ->whereYear('start_date', $currentYear)
            ->sum('estimated_audience');

        $paidRevenueThisMonth = Activity::whereMonth('start_date', $currentMonth)
            ->whereYear('start_date', $currentYear)
            ->where('payment_status', 'paid')
            ->sum('fee');

        $query = Activity::with(['organization', 'attachments'])->latest('start_date');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('activity_code', 'like', '%' . $this->search . '%')
                  ->orWhere('location_venue', 'like', '%' . $this->search . '%')
                  ->orWhere('organizer_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('organization', function ($sub) {
                      $sub->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->typeFilter !== 'all') {
            $query->where('event_type', $this->typeFilter);
        }

        if (!empty($this->organization_id)) {
            $query->where('organization_id', $this->organization_id);
        }

        $activities = $query->paginate(10);
        $organizations = Organization::orderBy('name')->get();

        return view('livewire.activities.activity-index', [
            'activities' => $activities,
            'organizations' => $organizations,
            'eventsThisMonth' => $eventsThisMonth,
            'audienceThisMonth' => $audienceThisMonth,
            'paidRevenueThisMonth' => $paidRevenueThisMonth,
        ])->layout('components.layouts.app');
    }
}

