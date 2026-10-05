<?php

namespace App\Models\Concerns;

/**
 * Shared SEO columns: meta_title, meta_description, canonical_url, og_image, noindex.
 */
trait HasSeo
{
    public function initializeHasSeo(): void
    {
        $this->mergeFillable(['meta_title', 'meta_description', 'canonical_url', 'og_image', 'noindex']);
        $this->mergeCasts(['noindex' => 'boolean']);
    }

    public function seoImageUrl(): ?string
    {
        $path = $this->og_image ?: ($this->image_path ?? null);

        return $path ? asset('storage/'.$path) : null;
    }
}
