<?php

use App\Models\Setting;
use App\Support\Seo;
use Illuminate\Support\HtmlString;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('seo')) {
    function seo(): Seo
    {
        return app(Seo::class);
    }
}

if (! function_exists('rich_heading')) {
    /** Escape editor text, then allow *emphasis* and line breaks. */
    function rich_heading(?string $text): HtmlString
    {
        $html = e(trim((string) $text));
        $html = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $html);

        return new HtmlString(nl2br($html, false));
    }
}

if (! function_exists('map_embed_url')) {
    /**
     * The Google Maps embed URL from settings. Accepts the pasted <iframe> code or a bare URL,
     * only allows Google Maps embeds, and falls back to a map of the business address.
     */
    function map_embed_url(): ?string
    {
        $raw = trim((string) setting('map_embed'));

        if ($raw !== '' && preg_match('/src=["\']([^"\']+)["\']/i', $raw, $match)) {
            $raw = html_entity_decode($match[1]);
        }

        if ($raw !== '' && preg_match('#^https://(www\.)?google\.[a-z.]+/maps/embed\?#i', $raw)) {
            return $raw;
        }

        $address = trim((string) setting('business_address'));

        return $address !== '' ? 'https://maps.google.com/maps?'.http_build_query(['q' => setting('site_name').', '.$address, 'output' => 'embed']) : null;
    }
}

if (! function_exists('paragraphs')) {
    /** Turn blank-line separated editor text into escaped paragraphs. */
    function paragraphs(?string $text): HtmlString
    {
        return new HtmlString(collect(preg_split('/\R{2,}/', trim((string) $text)))
            ->filter(fn ($paragraph) => trim($paragraph) !== '')
            ->map(fn ($paragraph) => '<p>'.rich_heading($paragraph).'</p>')
            ->implode("\n"));
    }
}
