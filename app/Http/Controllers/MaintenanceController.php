<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MaintenanceController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $enabled = (bool) setting('maintenance_enabled');

        // Admins can preview the page at any time; everyone else only sees it while it's on.
        if (! $enabled && ! $request->user()?->is_admin) {
            return redirect()->route('home');
        }

        // 503 + Retry-After tells search engines the outage is temporary, so rankings are kept.
        return response()->view('pages.maintenance', ['preview' => ! $enabled], $enabled ? 503 : 200)
            ->header('Retry-After', '3600')
            ->header('X-Robots-Tag', 'noindex')
            ->header('Cache-Control', 'no-store, private');
    }
}
