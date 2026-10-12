<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Illuminate\Console\Command;

class GenerateLicenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'license:generate 
                            {machine_id : Machine ID unik dari laptop klien (misal: WP-A4B0-9F44-4888-BF8B)}
                            {--client= : Nama lengkap psikolog / pemilik lisensi}
                            {--type=lifetime : Tipe lisensi (lifetime)}';

    /**
     * The console command description.
     */
    protected $description = 'Menghasilkan Kunci Lisensi offline Ed25519 untuk laptop klien tertentu';

    public function handle(LicenseService $licenseService): int
    {
        $machineId = strtoupper(trim($this->argument('machine_id')));
        $clientName = trim($this->option('client') ?? '');
        $type = strtolower(trim($this->option('type') ?? 'lifetime'));

        if (empty($clientName)) {
            $clientName = $this->ask('Masukkan nama pemilik lisensi (Psikolog/Klinik)', 'Psikolog Praktik Mandiri');
        }

        try {
            $token = $licenseService->generateLicenseToken($machineId, $clientName, $type);

            $this->newLine();
            $this->line('<fg=cyan>======================================================================</>');
            $this->line('<fg=cyan>           WEB PSIKOLOG — GENERATOR KUNCI LISENSI RESMI               </>');
            $this->line('<fg=cyan>======================================================================</>');
            $this->line(" <fg=gray>Nama Klien  :</> <fg=white;options=bold>{$clientName}</>");
            $this->line(" <fg=gray>Machine ID  :</> <fg=yellow;options=bold>{$machineId}</>");
            $this->line(" <fg=gray>Tipe Lisensi:</> <fg=green;options=bold>" . strtoupper($type) . " (Beli Putus)</>");
            $this->line(" <fg=gray>Tgl Rilis   :</> <fg=white>" . now()->translatedFormat('d F Y') . "</>");
            $this->line('<fg=cyan>----------------------------------------------------------------------</>');
            $this->line('<fg=yellow>KUNCI LISENSI AKTIVASI (Kirimkan kode ini ke WhatsApp Klien):</>');
            $this->newLine();
            $this->line("<fg=green;options=bold>{$token}</>");
            $this->newLine();
            $this->line('<fg=cyan>======================================================================</>');
            $this->info("✓ Kunci lisensi Ed25519 berhasil di-generate secara matematis.");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal membuat lisensi: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
