<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Redirect extends Model
{
    protected $fillable = ['from_path', 'to_url', 'status_code', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_hit_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(fn (self $redirect) => $redirect->from_path = static::normalisePath($redirect->from_path));
        static::saved(fn () => Cache::forget('redirects'));
        static::deleted(fn () => Cache::forget('redirects'));
    }

    public static function normalisePath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';

        return '/'.trim(strtolower($path), '/');
    }

    /** @return array<string, array{id:int, to:string, code:int}> */
    public static function map(): array
    {
        return Cache::rememberForever('redirects', fn () => static::query()
            ->where('is_active', true)
            ->get()
            ->mapWithKeys(fn (self $redirect) => [$redirect->from_path => ['id' => $redirect->id, 'to' => $redirect->to_url, 'code' => $redirect->status_code]])
            ->all());
    }

    /** Point an old path at a new one, collapsing chains so no redirect hops twice. */
    public static function point(string $from, string $to): void
    {
        $from = static::normalisePath($from);
        $to = static::normalisePath($to);

        if ($from === $to) {
            return;
        }

        static::query()->where('from_path', $to)->delete();
        static::query()->where('to_url', $from)->update(['to_url' => $to]);
        static::query()->updateOrCreate(['from_path' => $from], ['to_url' => $to, 'status_code' => 301, 'is_active' => true]);
        Cache::forget('redirects');
    }
}
