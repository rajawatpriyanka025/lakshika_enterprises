<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When maintenance mode is switched on in Admin → Settings, send visitors to the
 * maintenance page. Signed-in admins can still browse the full site to check it.
 */
class MaintenanceMode
{
    /** Paths that keep working during maintenance. */
    private const ALLOWED = ['admin', 'admin/*', 'maintenance', 'whatsapp', 'robots.txt', 'up'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! setting('maintenance_enabled') || $request->is(...self::ALLOWED) || $request->user()?->is_admin) {
            return $next($request);
        }

        return redirect()->route('maintenance');
    }
}
