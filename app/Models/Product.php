<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasSeo, HasSlug;

    public const AVAILABILITY = [
        'in_stock' => 'In stock',
        'out_of_stock' => 'Out of stock',
        'pre_order' => 'Pre-order',
    ];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'excerpt',
        'description',
        'focus_keyword',
        'sku',
        'material',
        'colour',
        'dimensions',
        'weight',
        'price',
        'availability',
        'image_path',
        'image_alt',
        'featured',
        'marketplace_links',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'marketplace_links' => 'array',
            'price' => 'decimal:2',
        ];
    }

    /** Specification rows for the product page and schema, skipping empty ones. */
    public function specifications(): array
    {
        return array_filter([
            'Material' => $this->material,
            'Colour' => $this->colour,
            'Size' => $this->dimensions,
            'Weight' => $this->weight,
            'SKU' => $this->sku,
        ], fn ($value) => filled($value));
    }

    public function slugSource(): string
    {
        return 'name';
    }

    public static function pathForSlug(string $slug): string
    {
        return '/products/'.$slug;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /** The direct listing URL for a marketplace, or a brand search link when none is set. */
    public function marketplaceUrl(Marketplace $marketplace): string
    {
        return ($this->marketplace_links[$marketplace->id] ?? null)
            ?: $marketplace->searchUrlFor(setting('site_name').' '.$this->name);
    }

    public function hasDirectListing(Marketplace $marketplace): bool
    {
        return filled($this->marketplace_links[$marketplace->id] ?? null);
    }
}
