<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Support\ProductSeo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Review and edit the SEO fields of every product on one screen.
 */
class ProductSeoController extends AdminController
{
    public function index(Request $request): View
    {
        $titleCounts = ProductSeo::titleCounts();

        $rows = Product::query()
            ->with('category')
            ->when($request->filled('q'), fn (Builder $query) => $query->where('name', 'like', '%'.$request->string('q')->trim().'%'))
            ->when($request->filled('category'), fn (Builder $query) => $query->where('category_id', $request->integer('category')))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) use ($titleCounts) {
                $audit = ProductSeo::audit($product, $titleCounts);

                return [
                    'product' => $product,
                    'audit' => $audit,
                    'score' => ProductSeo::score($audit),
                    'failing' => collect($audit)->where('pass', false)->pluck('label'),
                ];
            })
            ->when($request->input('show') === 'issues', fn ($rows) => $rows->where('score', '<', 80))
            ->values();

        $all = $rows->pluck('score');

        return view('admin.product-seo.index', [
            'rows' => $rows,
            'categories' => Category::query()->ordered()->pluck('name', 'id'),
            'filters' => $request->only(['q', 'category', 'show']),
            'average' => $all->isEmpty() ? 0 : (int) round($all->avg()),
            'missingMeta' => Product::query()->where(fn ($query) => $query->whereNull('meta_title')->orWhere('meta_title', '')->orWhereNull('meta_description')->orWhere('meta_description', ''))->count(),
            'missingKeyword' => Product::query()->where(fn ($query) => $query->whereNull('focus_keyword')->orWhere('focus_keyword', ''))->count(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'products' => ['required', 'array'],
            'products.*.focus_keyword' => ['nullable', 'string', 'max:100'],
            'products.*.meta_title' => ['nullable', 'string', 'max:70'],
            'products.*.meta_description' => ['nullable', 'string', 'max:320'],
            'products.*.image_alt' => ['nullable', 'string', 'max:160'],
        ], [
            'products.*.meta_title.max' => 'Meta titles must be 70 characters or fewer.',
            'products.*.meta_description.max' => 'Meta descriptions must be 320 characters or fewer.',
        ]);

        $products = Product::query()->with('category')->findMany(array_keys($data['products']))->keyBy('id');
        $changed = 0;

        foreach ($data['products'] as $id => $fields) {
            $product = $products->get($id);

            if (! $product) {
                continue;
            }

            $product->fill(collect($fields)->only(['focus_keyword', 'meta_title', 'meta_description', 'image_alt'])->map(fn ($value) => filled($value) ? trim($value) : null)->all());
            ProductSeo::fillMissing($product);

            if ($product->isDirty()) {
                $product->save();
                $changed++;
            }
        }

        return back()->with('status', $changed === 0 ? 'No changes to save.' : "SEO saved for {$changed} ".str('product')->plural($changed).'.');
    }

    /** Generate meta titles, descriptions and alt text wherever they are missing. */
    public function autofill(): RedirectResponse
    {
        $filled = 0;

        Product::query()->with('category')->each(function (Product $product) use (&$filled) {
            if (ProductSeo::fillMissing($product)) {
                $product->save();
                $filled++;
            }
        });

        return back()->with('status', $filled === 0
            ? 'Every product already has a meta title and description.'
            : "Generated missing SEO fields for {$filled} ".str('product')->plural($filled).'. Review them below and add focus keywords.');
    }
}
