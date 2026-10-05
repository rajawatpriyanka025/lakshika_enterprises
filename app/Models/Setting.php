<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function allCached(): array
    {
        if (! app()->bound('settings.values')) {
            try {
                $values = Cache::rememberForever('settings', fn () => static::query()->pluck('value', 'key')->all());
            } catch (\Throwable) {
                return []; // Table not migrated yet: fall back to config defaults.
            }

            app()->instance('settings.values', $values);
        }

        return app('settings.values');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::allCached()[$key] ?? null;

        return filled($value) ? $value : ($default ?? config("settings.defaults.{$key}"));
    }

    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        static::flush();
    }

    public static function flush(): void
    {
        Cache::forget('settings');
        app()->forgetInstance('settings.values');
    }
}
