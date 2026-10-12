<?php

namespace App\Services;

use App\Models\AppLicense;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class LicenseService
{
    protected static ?string $cachedMachineId = null;

    /**
     * Get unique hardware-based Machine ID formatted as WP-XXXX-XXXX-XXXX-XXXX
     */
    public function getMachineId(): string
    {
        if (static::$cachedMachineId !== null) {
            return static::$cachedMachineId;
        }

        $rawUuid = $this->detectRawHardwareUuid();

        // Fallback to local machine secret if OS UUID cannot be read
        if (empty($rawUuid)) {
            $rawUuid = $this->getOrCreateFallbackMachineIdentity();
        }

        $hash = strtoupper(hash('sha256', $rawUuid . 'SANCTUARY_HARDWARE_VAULT_2026'));

        static::$cachedMachineId = 'WP-' . substr($hash, 0, 4) . '-' . substr($hash, 4, 4) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4);

        return static::$cachedMachineId;
    }

    /**
     * Override machine ID (useful for automated testing)
     */
    public static function setFakeMachineId(?string $fakeMachineId): void
    {
        static::$cachedMachineId = $fakeMachineId;
    }

    /**
     * Detect raw hardware UUID from OS
     */
    protected function detectRawHardwareUuid(): string
    {
        try {
            $os = PHP_OS_FAMILY;

            if ($os === 'Darwin') {
                // macOS: query I/O Registry for IOPlatformUUID
                $uuid = trim((string) @shell_exec('ioreg -rd1 -c IOPlatformExpertDevice | awk \'/IOPlatformUUID/ { split($0, line, "\""); printf("%s\n", line[4]); }\''));
                if (!empty($uuid)) {
                    return $uuid;
                }

                // Fallback: system_profiler
                $uuid = trim((string) @shell_exec('system_profiler SPHardwareDataType 2>/dev/null | awk \'/Hardware UUID/ {print $3}\''));
                if (!empty($uuid)) {
                    return $uuid;
                }
            } elseif ($os === 'Windows') {
                // Windows: PowerShell CIM instance UUID
                $uuid = trim((string) @shell_exec('powershell -NoProfile -Command "(Get-CimInstance -Class Win32_ComputerSystemProduct).UUID" 2>nul'));
                if (!empty($uuid) && strlen($uuid) > 10) {
                    return $uuid;
                }

                // Fallback: WMIC
                $uuid = trim((string) @shell_exec('wmic csproduct get uuid 2>nul'));
                $lines = array_filter(array_map('trim', explode("\n", $uuid)));
                foreach ($lines as $line) {
                    if (strtoupper($line) !== 'UUID' && strlen($line) > 10) {
                        return $line;
                    }
                }

                // Fallback: MachineGuid registry
                $reg = trim((string) @shell_exec('reg query "HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Cryptography" /v MachineGuid 2>nul'));
                if (preg_match('/MachineGuid\s+REG_SZ\s+([A-Fa-f0-9\-]+)/', $reg, $m)) {
                    return $m[1];
                }
            } elseif ($os === 'Linux') {
                if (file_exists('/etc/machine-id')) {
                    $uuid = trim((string) @file_get_contents('/etc/machine-id'));
                    if (!empty($uuid)) {
                        return $uuid;
                    }
                }
                if (file_exists('/var/lib/dbus/machine-id')) {
                    $uuid = trim((string) @file_get_contents('/var/lib/dbus/machine-id'));
                    if (!empty($uuid)) {
                        return $uuid;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently fall through to fallback identity
        }

        return '';
    }

    /**
     * Fallback identity stored securely in local storage
     */
    protected function getOrCreateFallbackMachineIdentity(): string
    {
        $path = storage_path('app/.machine_identity');

        if (File::exists($path)) {
            $key = trim((string) File::get($path));
            if (!empty($key)) {
                return $key;
            }
        }

        $newKey = Str::uuid()->toString();
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $newKey);

        return $newKey;
    }

    /**
     * Generate an Ed25519-signed license token (Developer side)
     */
    public function generateLicenseToken(string $machineId, string $clientName, string $type = 'lifetime'): string
    {
        $privateKeyBase64 = config('license.private_key');

        if (empty($privateKeyBase64)) {
            throw new RuntimeException('Private Key lisensi belum diatur di .env (APP_LICENSE_PRIVATE_KEY). Generator tidak dapat dijalankan tanpa kunci privat.');
        }

        $secretKey = base64_decode($privateKeyBase64);
        if (strlen($secretKey) !== SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
            throw new RuntimeException('Panjang Secret Key tidak valid (harus 64 bytes Ed25519).');
        }

        $machineId = strtoupper(trim($machineId));
        $clientName = trim($clientName);
        $type = strtolower(trim($type));

        $payload = $machineId . '|' . strtoupper($clientName) . '|' . strtoupper($type);
        $signature = sodium_crypto_sign_detached($payload, $secretKey);

        $package = [
            'm' => $machineId,
            'c' => $clientName,
            't' => $type,
            'i' => now()->toDateString(),
            's' => base64_encode($signature),
        ];

        return 'WP-LIC-' . rtrim(strtr(base64_encode(json_encode($package)), '+/', '-_'), '=');
    }

    /**
     * Verify and parse a license token
     */
    public function verifyAndParseToken(string $token): array
    {
        $cleanedToken = trim($token);

        if (str_starts_with($cleanedToken, 'WP-LIC-')) {
            $cleanedToken = substr($cleanedToken, 7);
        }

        $jsonString = base64_decode(strtr($cleanedToken, '-_', '+/'), true);
        if ($jsonString === false) {
            return [
                'valid' => false,
                'error' => 'Format Kunci Lisensi rusak atau tidak valid.',
                'data' => null,
            ];
        }

        $decoded = json_decode($jsonString, true);
        if (!is_array($decoded) || !isset($decoded['m'], $decoded['c'], $decoded['s'])) {
            return [
                'valid' => false,
                'error' => 'Struktur Kunci Lisensi tidak dikenali.',
                'data' => null,
            ];
        }

        $publicKeyBase64 = config('license.public_key');
        if (empty($publicKeyBase64)) {
            return [
                'valid' => false,
                'error' => 'Kunci Publik verifikasi belum terkonfigurasi di sistem.',
                'data' => null,
            ];
        }

        $publicKey = base64_decode($publicKeyBase64);
        if (strlen($publicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            return [
                'valid' => false,
                'error' => 'Kunci Publik sistem tidak valid.',
                'data' => null,
            ];
        }

        $type = strtoupper($decoded['t'] ?? 'LIFETIME');
        $payload = strtoupper($decoded['m']) . '|' . strtoupper(trim($decoded['c'])) . '|' . $type;
        $signature = base64_decode($decoded['s'], true);

        if ($signature === false || strlen($signature) !== SODIUM_CRYPTO_SIGN_BYTES) {
            return [
                'valid' => false,
                'error' => 'Tanda tangan digital lisensi tidak valid.',
                'data' => null,
            ];
        }

        $isValid = sodium_crypto_sign_verify_detached($signature, $payload, $publicKey);

        if (!$isValid) {
            return [
                'valid' => false,
                'error' => 'Kunci Lisensi tidak sah atau telah dimodifikasi.',
                'data' => null,
            ];
        }

        return [
            'valid' => true,
            'error' => null,
            'data' => $decoded,
        ];
    }

    /**
     * Activate the application with the given license token
     */
    public function activate(string $token): array
    {
        $verification = $this->verifyAndParseToken($token);

        if (!$verification['valid']) {
            return [
                'success' => false,
                'message' => $verification['error'],
            ];
        }

        $data = $verification['data'];
        $currentMachineId = $this->getMachineId();

        if (strtoupper($data['m']) !== strtoupper($currentMachineId)) {
            return [
                'success' => false,
                'message' => "Kunci Lisensi ini diterbitkan khusus untuk perangkat ({$data['m']}) dan tidak cocok dengan laptop ini ({$currentMachineId}).",
            ];
        }

        // Deactivate any old licenses
        AppLicense::query()->update(['is_active' => false]);

        // Save new active license
        $license = AppLicense::create([
            'machine_id' => $currentMachineId,
            'client_name' => $data['c'],
            'license_token' => trim($token),
            'license_type' => $data['t'] ?? 'lifetime',
            'signature' => $data['s'],
            'is_active' => true,
            'activated_at' => now(),
            'meta' => [
                'issued_at' => $data['i'] ?? null,
                'activated_on_machine' => $currentMachineId,
                'platform' => PHP_OS_FAMILY,
            ],
        ]);

        return [
            'success' => true,
            'message' => "Selamat datang, {$data['c']}! Lisensi aplikasi Web Psikolog telah aktif seumur hidup.",
            'license' => $license,
        ];
    }

    /**
     * Check if the application is legally licensed for this device
     */
    public function isLicensed(): bool
    {
        if (!config('license.enabled', true)) {
            return true;
        }

        $activeLicense = AppLicense::where('is_active', true)->first();

        if (!$activeLicense) {
            return false;
        }

        // Must match current machine ID
        if (strtoupper($activeLicense->machine_id) !== strtoupper($this->getMachineId())) {
            return false;
        }

        // Verify cryptographic signature
        $check = $this->verifyAndParseToken($activeLicense->license_token);

        return $check['valid'];
    }

    /**
     * Get the currently active license model
     */
    public function getActiveLicense(): ?AppLicense
    {
        if (!$this->isLicensed()) {
            return null;
        }

        return AppLicense::where('is_active', true)->first();
    }

    /**
     * Build WhatsApp link with pre-filled message for activation
     */
    public function getWhatsAppLink(?string $psychologistName = null): string
    {
        $phone = preg_replace('/[^0-9]/', '', config('license.whatsapp_number', '6281234567890'));
        $machineId = $this->getMachineId();
        $name = trim($psychologistName ?? 'Psikolog');

        $message = "Halo Admin Web Psikolog,\nSaya ingin melakukan aktivasi lisensi aplikasi:\nNama: {$name}\nMachine ID: {$machineId}\n\nMohon bantuannya untuk pembuatan Kode Lisensi. Terima kasih.";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }
}

