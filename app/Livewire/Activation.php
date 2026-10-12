<?php

namespace App\Livewire;

use App\Services\LicenseService;
use Livewire\Component;
use Livewire\WithFileUploads;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Activation extends Component
{
    use WithFileUploads;

    public string $machineId = '';
    public string $licenseToken = '';
    public $licenseFile = null;
    public string $whatsAppUrl = '';
    public string $qrSvg = '';
    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(LicenseService $licenseService)
    {
        if ($licenseService->isLicensed()) {
            return redirect()->route('dashboard');
        }

        $this->machineId = $licenseService->getMachineId();
        $this->whatsAppUrl = $licenseService->getWhatsAppLink();
        $this->qrSvg = (string) QrCode::size(140)
            ->color(45, 212, 191) // teal-400
            ->backgroundColor(11, 22, 27) // spruce-950
            ->margin(1)
            ->generate($this->whatsAppUrl);
    }

    public function activate(LicenseService $licenseService)
    {
        $this->errorMessage = '';
        $this->validate([
            'licenseToken' => 'required|string|min:20',
        ], [
            'licenseToken.required' => 'Silakan masukkan Kunci Lisensi aktivasi terlebih dahulu.',
            'licenseToken.min' => 'Format Kunci Lisensi terlalu pendek atau tidak valid.',
        ]);

        $result = $licenseService->activate(trim($this->licenseToken));

        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        session()->flash('message', $result['message']);

        // Redirect to dashboard (or lock-screen)
        return redirect()->route('dashboard');
    }

    public function updatedLicenseFile(LicenseService $licenseService)
    {
        $this->errorMessage = '';

        if (!$this->licenseFile) {
            return;
        }

        try {
            $content = trim(file_get_contents($this->licenseFile->getRealPath()));
            if (!empty($content)) {
                $this->licenseToken = $content;
                $this->activate($licenseService);
            }
        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal membaca file lisensi: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.activation')->layout('components.layouts.guest');
    }
}

