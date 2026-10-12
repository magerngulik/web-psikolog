<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppIsLicensed
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $isLicensed = $this->licenseService->isLicensed();

        // If not licensed, force redirect to activation screen
        if (!$isLicensed) {
            if (!$request->routeIs('activation') && !$request->routeIs('rpp.verify')) {
                return redirect()->route('activation');
            }
        } else {
            // If already licensed and visiting activation, redirect to dashboard / lock-screen
            if ($request->routeIs('activation')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}

