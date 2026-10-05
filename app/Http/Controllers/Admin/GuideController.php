<?php

namespace App\Http\Controllers\Admin;

use App\Models\Guide;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuideController extends AdminController
{
    public function index(): View
    {
        return view('admin.guides.index', ['guides' => Guide::query()->ordered()->get()]);
    }

    public function create(): View
    {
        return view('admin.guides.form', ['guide' => new Guide([
            'is_published' => true,
            'published_at' => now(),
            'sections' => [['heading' => '', 'body' => '']],
        ])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $guide = new Guide;
        $this->save($request, $guide);

        return redirect()->route('admin.guides.edit', $guide)->with('status', 'Guide created.');
    }

    public function edit(Guide $guide): View
    {
        return view('admin.guides.form', ['guide' => $guide]);
    }

    public function update(Request $request, Guide $guide): RedirectResponse
    {
        $this->save($request, $guide);

        return redirect()->route('admin.guides.edit', $guide)->with('status', 'Guide saved.');
    }

    public function destroy(Guide $guide): RedirectResponse
    {
        $this->deleteImage($guide->image_path);
        $this->deleteImage($guide->og_image);
        $guide->delete();

        return redirect()->route('admin.guides.index')->with('status', 'Guide deleted.');
    }

    private function save(Request $request, Guide $guide): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', 'alpha_dash', Rule::unique('guides', 'slug')->ignore($guide)],
            'intro' => ['required', 'string', 'max:1000'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.heading' => ['nullable', 'string', 'max:160'],
            'sections.*.body' => ['nullable', 'string', 'max:10000'],
            'image' => self::IMAGE_RULES,
            'remove_image' => ['nullable', 'boolean'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ] + $this->seoRules());

        $sections = collect($data['sections'])
            ->map(fn ($section) => ['heading' => trim($section['heading'] ?? ''), 'body' => trim($section['body'] ?? '')])
            ->filter(fn ($section) => $section['heading'] !== '' || $section['body'] !== '')
            ->values()
            ->all();

        $guide->fill([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? null,
            'intro' => $data['intro'],
            'sections' => $sections,
            'image_alt' => $data['image_alt'] ?? null,
            'published_at' => $data['published_at'] ?? now(),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published'),
            'image_path' => $this->replaceImage($request, 'image', 'remove_image', $guide->image_path, 'guides'),
        ] + $this->seoData($request, $data, $guide->og_image));

        $guide->save();
    }
}
