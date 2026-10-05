<?php

namespace App\Http\Controllers\Admin;

use App\Models\Redirect;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RedirectController extends AdminController
{
    public function index(Request $request): View
    {
        return view('admin.redirects.index', [
            'redirects' => Redirect::query()
                ->when($request->filled('q'), fn ($query) => $query->where('from_path', 'like', '%'.$request->string('q')->trim().'%')->orWhere('to_url', 'like', '%'.$request->string('q')->trim().'%'))
                ->latest()
                ->paginate(50)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.redirects.form', ['redirect' => new Redirect(['status_code' => 301, 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Redirect::query()->create($this->validated($request));

        return redirect()->route('admin.redirects.index')->with('status', 'Redirect added.');
    }

    public function edit(Redirect $redirect): View
    {
        return view('admin.redirects.form', ['redirect' => $redirect]);
    }

    public function update(Request $request, Redirect $redirect): RedirectResponse
    {
        $redirect->update($this->validated($request, $redirect));

        return redirect()->route('admin.redirects.index')->with('status', 'Redirect saved.');
    }

    public function destroy(Redirect $redirect): RedirectResponse
    {
        $redirect->delete();

        return redirect()->route('admin.redirects.index')->with('status', 'Redirect deleted.');
    }

    private function validated(Request $request, ?Redirect $redirect = null): array
    {
        $request->merge(['from_path' => Redirect::normalisePath((string) $request->input('from_path'))]);

        $data = $request->validate([
            'from_path' => ['required', 'string', 'max:255', 'not_in:/,/admin', Rule::unique('redirects', 'from_path')->ignore($redirect)],
            'to_url' => ['required', 'string', 'max:255', 'regex:/^(https?:\/\/|\/)/', 'different:from_path'],
            'status_code' => ['required', Rule::in([301, 302])],
        ], [
            'from_path.not_in' => 'The home page and admin cannot be redirected.',
            'to_url.regex' => 'Start with / for a page on this site, or https:// for another site.',
        ]);

        return $data + ['is_active' => $request->boolean('is_active')];
    }
}
