<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves the 301/302 redirects managed in Admin → Redirects before routing,
 * so old URLs keep working (and keep their search rankings) after changes.
 */
class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || $request->is('admin', 'admin/*')) {
            return $next($request);
        }

        try {
            $match = Redirect::map()[Redirect::normalisePath($request->path())] ?? null;
        } catch (\Throwable) {
            $match = null; // Table not migrated yet.
        }

        if ($match === null) {
            return $next($request);
        }

        Redirect::query()->whereKey($match['id'])->increment('hits', 1, ['last_hit_at' => now()]);

        $target = str_starts_with($match['to'], 'http') ? $match['to'] : url($match['to']);

        if ($request->getQueryString() && ! str_contains($target, '?')) {
            $target .= '?'.$request->getQueryString();
        }

        return redirect()->away($target, $match['code']);
    }
}
