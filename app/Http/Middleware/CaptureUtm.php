<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Remembers the campaign a visitor arrived from so enquiries can be attributed.
 */
class CaptureUtm
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession() && $request->filled('utm_source')) {
            $request->session()->put('utm', [
                'source' => mb_substr((string) $request->query('utm_source'), 0, 100),
                'medium' => mb_substr((string) $request->query('utm_medium'), 0, 100) ?: null,
                'campaign' => mb_substr((string) $request->query('utm_campaign'), 0, 100) ?: null,
            ]);
        }

        return $next($request);
    }
}
