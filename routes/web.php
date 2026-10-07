<?php

use App\Livewire\Cases\CaseCreate;
use App\Livewire\Cases\CaseIndex;
use App\Livewire\Cases\CaseShow;
use App\Livewire\Clients\ClientCreate;
use App\Livewire\Clients\ClientEdit;
use App\Livewire\Clients\ClientIndex;
use App\Livewire\Clients\ClientShow;
use App\Livewire\Organizations\OrganizationIndex;
use App\Livewire\Activities\ActivityIndex;
use App\Livewire\Activities\ActivityCreate;
use App\Livewire\Activities\ActivityShow;
use App\Livewire\Activities\ActivityEdit;
use App\Livewire\Dashboard;
use App\Livewire\LockScreen;
use App\Livewire\Reports\AnnualLogbook;
use App\Livewire\Reports\RppVerifier;
use App\Livewire\Sessions\SessionCreate;
use App\Livewire\Sessions\SessionIndex;
use App\Livewire\Sessions\SessionShow;
use App\Livewire\Settings\ProfileSettings;
use App\Livewire\Settings\ReferenceManager;
use App\Livewire\Settings\SecuritySettings;
use Illuminate\Support\Facades\Route;

// Guest Lock Screen & Public Verification
Route::get('/lock-screen', LockScreen::class)->name('lock-screen');
Route::get('/verify-rpp/{token}', RppVerifier::class)->name('rpp.verify');

// Protected Routes
Route::middleware(['pin.protected'])->group(function () {
    // Main Dashboard Route
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard.index');

    // Modul Klien (Modul 3)
    Route::get('/clients', ClientIndex::class)->name('clients.index');
    Route::get('/clients/create', ClientCreate::class)->name('clients.create');
    Route::get('/clients/{id}', ClientShow::class)->name('clients.show');
    Route::get('/clients/{id}/edit', ClientEdit::class)->name('clients.edit');

    // Modul Kasus Medis (Modul 4)
    Route::get('/cases', CaseIndex::class)->name('cases.index');
    Route::get('/cases/create', CaseCreate::class)->name('cases.create');
    Route::get('/cases/{id}', CaseShow::class)->name('cases.show');

    // Modul Sesi Konseling (Modul 5)
    Route::get('/sessions', SessionIndex::class)->name('sessions.index');
    Route::get('/sessions/create', SessionCreate::class)->name('sessions.create');
    Route::get('/sessions/{id}', SessionShow::class)->name('sessions.show');

    // Modul Mitra Organisasi
    Route::get('/organizations', OrganizationIndex::class)->name('organizations.index');

    // Modul Kegiatan & Seminar
    Route::get('/activities', ActivityIndex::class)->name('activities.index');
    Route::get('/activities/create', ActivityCreate::class)->name('activities.create');
    Route::get('/activities/{id}', ActivityShow::class)->name('activities.show');
    Route::get('/activities/{id}/edit', ActivityEdit::class)->name('activities.edit');

    // Modul RPP Generator & Reports (Dialihkan ke Sesi Rekam Medis)
    Route::get('/rpp/{appointmentId}', function ($appointmentId) {
        return redirect()->route('sessions.show', $appointmentId);
    })->name('rpp.edit');

    // Modul Laporan & Logbook Tahunan SKP IPK
    Route::get('/reports/logbook', AnnualLogbook::class)->name('reports.logbook');
    Route::get('/reports/logbook/pdf/{year?}', [AnnualLogbook::class, 'downloadPdf'])->name('reports.logbook.pdf');

    // Modul Settings & Master Data (Modul 6)
    Route::get('/settings/profile', ProfileSettings::class)->name('settings.profile');
    Route::get('/settings/references', ReferenceManager::class)->name('settings.references');
    Route::get('/settings/security', SecuritySettings::class)->name('settings.security');
});
