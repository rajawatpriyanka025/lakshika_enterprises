<?php

namespace App\Models\Concerns;

use App\Models\Redirect;
use Illuminate\Support\Str;

/**
 * Generates a unique slug from the model's title field and, when a published
 * slug changes, records a 301 so existing links and search rankings survive.
 */
trait HasSlug
{
    abstract public function slugSource(): string;

    abstract public static function pathForSlug(string $slug): string;

    public static function bootHasSlug(): void
    {
        static::saving(function (self $model) {
            $model->slug = static::uniqueSlug($model->slug ?: $model->{$model->slugSource()}, $model->getKey());
        });

        static::updated(function (self $model) {
            if (! $model->wasChanged('slug') || ! $model->getOriginal('slug')) {
                return;
            }

            Redirect::point(static::pathForSlug($model->getOriginal('slug')), static::pathForSlug($model->slug));
        });
    }

    public static function uniqueSlug(string $value, mixed $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'item';
        $slug = $base;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
