<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    protected $fillable = ['name', 'search_url', 'search_parameter', 'store_url', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function searchUrlFor(string $term): string
    {
        $separator = str_contains($this->search_url, '?') ? '&' : '?';

        return $this->search_url.$separator.http_build_query([$this->search_parameter => trim($term)]);
    }

    /** Link to the brand's storefront on this marketplace, falling back to a brand search. */
    public function brandUrl(): string
    {
        return $this->store_url ?: $this->searchUrlFor((string) setting('site_name'));
    }
}
