<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\EnsureAppIsLicensed::class,
        ]);

        $middleware->alias([
            'pin.protected' => \App\Http\Middleware\CheckSecurityPin::class,
            'licensed' => \App\Http\Middleware\EnsureAppIsLicensed::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Log all exceptions to help debug Windows production builds
        $exceptions->report(function (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[NATIVE_EXCEPTION]: ' . get_class($e) . ': ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        });

        // Render a safe fallback page for any unhandled exception in production
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! app()->environment('local') && ! $request->is('api/*') && ! $request->expectsJson()) {
                try {
                    // Try to redirect to activation screen as safe fallback if not already on activation
                    if (! $request->routeIs('activation') && ! str_contains($request->path(), 'activation')) {
                        return redirect()->to('/activation');
                    }
                } catch (\Throwable) {
                    // fall through to user-friendly HTML error
                }

                // If exception happened on activation or redirect fails, return friendly recovery screen
                return response(
                    '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Web Psikolog Basirah</title></head>' .
                    '<body style="background:#080f14;color:#94a3b8;font-family:system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:24px;box-sizing:border-box;">' .
                    '<div style="max-width:540px;background:#0d1820;border:1px solid rgba(45,212,191,0.25);border-radius:16px;padding:32px;text-align:center;box-shadow:0 20px 40px rgba(0,0,0,0.5);">' .
                    '<h1 style="color:#2dd4bf;font-size:22px;margin:0 0 12px;font-weight:600;">Web Psikolog Basirah</h1>' .
                    '<p style="color:#94a3b8;font-size:14px;line-height:1.6;margin:0 0 20px;">Aplikasi sedang melakukan inisialisasi awal. Silakan tutup jendela ini, tunggu beberapa detik, lalu buka kembali aplikasi.</p>' .
                    '<div style="background:#050a0d;border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:12px;font-family:monospace;font-size:11px;color:#f87171;word-break:break-all;text-align:left;">' .
                    '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage() ?: get_class($e)) .
                    '</div>' .
                    '</div>' .
                    '</body></html>',
                    500
                );
            }
        });
    })->create();

