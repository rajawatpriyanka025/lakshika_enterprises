<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;

/**
 * Editable copy and SEO for the fixed site pages. Field definitions and defaults
 * live in config/pages.php; only edited values are stored in `content`.
 */
class Page extends Model
{
    use HasSeo;

    protected $fillable = ['key', 'name', 'content'];

    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    public static function for(string $key): self
    {
        return static::query()->firstOrNew(
            ['key' => $key],
            ['name' => config("pages.{$key}.name", ucfirst($key))],
        );
    }

    public function definition(): array
    {
        return config("pages.{$this->key}.fields", []);
    }

    public function field(string $name): string
    {
        $value = $this->content[$name] ?? null;

        return filled($value) ? $value : (string) config("pages.{$this->key}.fields.{$name}.default", '');
    }

    public function defaultMeta(string $name): string
    {
        return (string) config("pages.{$this->key}.{$name}", '');
    }
}
