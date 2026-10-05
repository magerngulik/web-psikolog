<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\Session;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $totalClients = Client::count();
        $activeCases = MedicalCase::where('status', 'active')->count();
        $todaySessions = Session::with(['medicalCase.client'])
            ->whereDate('session_date', today())
            ->orderBy('start_time', 'asc')
            ->get();
        $upcomingSessionsCount = Session::whereDate('session_date', '>=', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->count();

        return view('livewire.dashboard', [
            'totalClients' => $totalClients,
            'activeCases' => $activeCases,
            'todaySessions' => $todaySessions,
            'upcomingSessionsCount' => $upcomingSessionsCount,
        ])->layout('components.layouts.app');
    }
}
