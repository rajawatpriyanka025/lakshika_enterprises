<?php

namespace App\Http\Controllers\Admin;

use App\Models\Marketplace;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MarketplaceController extends AdminController
{
    public function index(): View
    {
        return view('admin.marketplaces.index', ['marketplaces' => Marketplace::query()->orderBy('sort_order')->get()]);
    }

    public function create(): View
    {
        return view('admin.marketplaces.form', ['marketplace' => new Marketplace(['is_active' => true, 'search_parameter' => 'q'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Marketplace::query()->create($this->validated($request));

        return redirect()->route('admin.marketplaces.index')->with('status', 'Marketplace added.');
    }

    public function edit(Marketplace $marketplace): View
    {
        return view('admin.marketplaces.form', ['marketplace' => $marketplace]);
    }

    public function update(Request $request, Marketplace $marketplace): RedirectResponse
    {
        $marketplace->update($this->validated($request));

        return redirect()->route('admin.marketplaces.index')->with('status', 'Marketplace saved.');
    }

    public function destroy(Marketplace $marketplace): RedirectResponse
    {
        $marketplace->delete();

        return redirect()->route('admin.marketplaces.index')->with('status', 'Marketplace removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'search_url' => ['required', 'url', 'max:255'],
            'search_parameter' => ['required', 'string', 'max:40', 'alpha_dash'],
            'store_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]) + ['is_active' => $request->boolean('is_active'), 'sort_order' => $request->integer('sort_order')];
    }
}
