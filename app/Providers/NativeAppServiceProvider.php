<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $this->ensureInitialDataSeeded();

        Window::open()
            ->title('Web Psikolog — Clinical Practice & Records')
            ->width(1366)
            ->height(860)
            ->minWidth(1024)
            ->minHeight(700)
            ->rememberState()
            ->backgroundColor('#080f14')
            ->showDevTools(false);
    }

    protected function ensureInitialDataSeeded(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('users') || ! \Illuminate\Support\Facades\Schema::hasTable('app_licenses')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('system_references')) {
                if (\App\Models\SystemReference::count() === 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('DB auto-init check in NativeAppServiceProvider: ' . $e->getMessage());
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'max_execution_time' => '120',
        ];
    }
}
