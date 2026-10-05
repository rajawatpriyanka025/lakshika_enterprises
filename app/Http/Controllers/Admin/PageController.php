<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController extends AdminController
{
    public function index(): View
    {
        $stored = Page::query()->get()->keyBy('key');

        return view('admin.pages.index', [
            'pages' => collect(config('pages'))->map(fn ($definition, $key) => $stored->get($key) ?? Page::for($key)),
        ]);
    }

    public function edit(string $key): View
    {
        abort_unless(config()->has("pages.{$key}"), 404);

        return view('admin.pages.form', ['page' => Page::for($key)]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        abort_unless(config()->has("pages.{$key}"), 404);

        $page = Page::for($key);

        $fieldRules = collect($page->definition())
            ->mapWithKeys(fn ($field, $name) => ["content.{$name}" => ['nullable', 'string', ($field['type'] ?? 'text') === 'longtext' ? 'max:10000' : 'max:1000']])
            ->all();

        $data = $request->validate($fieldRules + $this->seoRules());

        // Store only values that differ from the defaults so future copy updates still flow through.
        $content = collect($data['content'] ?? [])
            ->map(fn ($value) => str_replace("\r\n", "\n", trim((string) $value)))
            ->reject(fn ($value, $name) => $value === '' || $value === config("pages.{$key}.fields.{$name}.default"))
            ->all();

        $page->fill(['name' => config("pages.{$key}.name"), 'content' => $content] + $this->seoData($request, $data, $page->og_image));
        $page->save();

        return redirect()->route('admin.pages.edit', $key)->with('status', $page->name.' page saved.');
    }
}
