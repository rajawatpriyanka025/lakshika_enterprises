<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        if (setting('discourage_indexing')) {
            $body = "User-agent: *\nDisallow: /\n";
        } else {
            $lines = [
                'User-agent: *',
                'Allow: /',
                'Disallow: /admin',
                'Disallow: /whatsapp',
                'Disallow: /shop?q=',
            ];

            $extra = trim((string) setting('robots_extra'));
            $body = implode("\n", $lines)."\n".($extra !== '' ? $extra."\n" : '')."\nSitemap: ".route('sitemap')."\n";
        }

        return response($body)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
