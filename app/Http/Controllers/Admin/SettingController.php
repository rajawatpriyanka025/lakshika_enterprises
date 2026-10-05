<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends AdminController
{
    public function edit(string $group = 'general'): View
    {
        abort_unless(config()->has("settings.groups.{$group}"), 404);

        return view('admin.settings.edit', [
            'groups' => config('settings.groups'),
            'group' => $group,
            'values' => Setting::query()->pluck('value', 'key'),
        ]);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        abort_unless(config()->has("settings.groups.{$group}"), 404);

        $fields = config("settings.groups.{$group}.fields");

        $validated = $request->validate(collect($fields)->map(fn ($field) => $field['rules'])->all());

        $values = [];

        foreach ($fields as $key => $field) {
            $type = $field['type'] ?? 'text';

            $values[$key] = match ($type) {
                // Store an explicit 0 so an unticked box doesn't fall back to a default of "on".
                'checkbox' => $request->boolean($key) ? '1' : '0',
                'image' => $this->replaceImage($request, $key, "remove_{$key}", Setting::query()->find($key)?->value, 'seo'),
                default => isset($validated[$key]) ? trim((string) $validated[$key]) : null,
            };
        }

        Setting::putMany($values);

        return redirect()->route('admin.settings.edit', $group)->with('status', 'Settings saved.');
    }
}
