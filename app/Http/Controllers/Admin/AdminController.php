<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class AdminController extends Controller
{
    protected const IMAGE_RULES = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

    /** Validation rules for the shared SEO panel. */
    protected function seoRules(): array
    {
        return [
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/|\/)/'],
            'og_image_file' => self::IMAGE_RULES,
            'remove_og_image' => ['nullable', 'boolean'],
            'noindex' => ['nullable', 'boolean'],
        ];
    }

    /** Pull the SEO fields out of validated data, storing an uploaded share image. */
    protected function seoData(Request $request, array $validated, ?string $currentImage = null): array
    {
        return [
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'noindex' => $request->boolean('noindex'),
            'og_image' => $this->replaceImage($request, 'og_image_file', 'remove_og_image', $currentImage, 'seo'),
        ];
    }

    /** Store a newly uploaded image (deleting the old one), honour a "remove" checkbox, or keep the current path. */
    protected function replaceImage(Request $request, string $field, ?string $removeField, ?string $current, string $folder): ?string
    {
        if ($request->hasFile($field)) {
            $this->deleteImage($current);

            return $request->file($field)->store($folder, 'public');
        }

        if ($removeField && $request->boolean($removeField)) {
            $this->deleteImage($current);

            return null;
        }

        return $current;
    }

    protected function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
