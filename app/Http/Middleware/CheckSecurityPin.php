<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSecurityPin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->get('pin_unlocked')) {
            return redirect()->route('lock-screen');
        }

        return $next($request);
    }
}

