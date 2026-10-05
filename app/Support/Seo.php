<?php

namespace App\Support;

use App\Models\Page;
use Illuminate\Database\Eloquent\Model;

/**
 * Collects the SEO data for the current request; the layout renders it.
 */
class Seo
{
    public ?string $title = null;

    public ?string $description = null;

    public ?string $canonical = null;

    public ?string $image = null;

    public string $type = 'website';

    public bool $noindex = false;

    /** @var array<string, string|null> label => url */
    public array $breadcrumbs = [];

    public function set(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key) && $value !== null) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /** Apply a model's SEO overrides on top of the given fallbacks. */
    public function forModel(Model $model, string $title, ?string $description = null): static
    {
        return $this->set([
            'title' => $model->meta_title ?: $title,
            'description' => $model->meta_description ?: $description,
            'canonical' => $model->canonical_url ?: null,
            'image' => method_exists($model, 'seoImageUrl') ? $model->seoImageUrl() : null,
            'noindex' => (bool) $model->noindex,
        ]);
    }

    public function forPage(Page $page): static
    {
        return $this->set([
            'title' => $page->meta_title ?: $page->defaultMeta('meta_title'),
            'description' => $page->meta_description ?: $page->defaultMeta('meta_description'),
            'canonical' => $page->canonical_url ?: null,
            'image' => $page->og_image ? asset('storage/'.$page->og_image) : null,
            'noindex' => (bool) $page->noindex,
        ]);
    }

    public function breadcrumbs(array $crumbs): static
    {
        $this->breadcrumbs = ['Home' => route('home')] + $crumbs;

        return $this;
    }

    public function fullTitle(): string
    {
        $site = setting('site_name');
        $title = trim((string) $this->title);

        if ($title === '') {
            return $site.' | '.setting('tagline');
        }

        return str_contains(strtolower($title), strtolower($site)) ? $title : $title.' | '.$site;
    }

    public function metaDescription(): string
    {
        $description = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($this->description ?: setting('default_meta_description')))));

        return mb_strlen($description) > 160 ? rtrim(mb_substr($description, 0, 157)).'…' : $description;
    }

    /** Canonical URL: explicit override, else the current path plus the page number when paginated. */
    public function canonicalUrl(): string
    {
        if ($this->canonical) {
            return str_starts_with($this->canonical, 'http') ? $this->canonical : url($this->canonical);
        }

        $page = (int) request()->query('page', 1);

        return url()->current().($page > 1 ? '?page='.$page : '');
    }

    public function robots(): string
    {
        if ($this->noindex || setting('discourage_indexing')) {
            return 'noindex, follow';
        }

        return 'index, follow, max-image-preview:large, max-snippet:-1';
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            return $this->image;
        }

        $default = setting('default_og_image');

        return $default ? asset('storage/'.$default) : asset('images/brand/logo-square.png');
    }

    public function breadcrumbSchema(): ?array
    {
        if (count($this->breadcrumbs) < 2) {
            return null;
        }

        $position = 1;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($this->breadcrumbs)->map(fn ($url, $label) => array_filter([
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $label,
                'item' => $url,
            ]))->values()->all(),
        ];
    }

    public function organizationSchema(): array
    {
        $sameAs = collect(config('settings.groups.social.fields'))
            ->keys()
            ->map(fn ($key) => setting($key))
            ->filter()
            ->values()
            ->all();

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => setting('site_name'),
            'url' => url('/'),
            'logo' => asset('images/brand/logo-square.png'),
            'description' => setting('default_meta_description'),
            'email' => setting('contact_email'),
            'telephone' => setting('contact_phone'),
            'sameAs' => $sameAs ?: null,
            'contactPoint' => setting('contact_phone') ? [
                '@type' => 'ContactPoint',
                'telephone' => setting('contact_phone'),
                'contactType' => 'customer service',
                'areaServed' => 'IN',
                'availableLanguage' => ['en', 'hi'],
            ] : null,
        ]);
    }
}
