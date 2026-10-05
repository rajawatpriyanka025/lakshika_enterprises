<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Marketplace;
use App\Models\Product;
use App\Support\ProductSeo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends AdminController
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->withCount('leads')
            ->when($request->filled('q'), fn (Builder $query) => $query->where('name', 'like', '%'.$request->string('q')->trim().'%'))
            ->when($request->filled('category'), fn (Builder $query) => $query->where('category_id', $request->integer('category')))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $titleCounts = ProductSeo::titleCounts();
        $products->getCollection()->each(function (Product $product) use ($titleCounts) {
            $product->setAttribute('seo_score', ProductSeo::score(ProductSeo::audit($product, $titleCounts)));
        });

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::query()->ordered()->pluck('name', 'id'),
            'filters' => $request->only(['q', 'category']),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Product(['is_active' => true, 'sort_order' => 0]));
    }

    public function store(Request $request): RedirectResponse
    {
        $product = new Product;
        $this->save($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return $this->form($product);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->save($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product saved.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImage($product->image_path);
        $this->deleteImage($product->og_image);
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted. Consider adding a redirect for its old URL.');
    }

    private function form(Product $product): View
    {
        $audit = $product->exists ? ProductSeo::audit($product) : [];

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::query()->ordered()->pluck('name', 'id'),
            'marketplaces' => Marketplace::query()->orderBy('sort_order')->get(),
            'audit' => $audit,
            'score' => ProductSeo::score($audit),
        ]);
    }

    private function save(Request $request, Product $product): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', 'alpha_dash', Rule::unique('products', 'slug')->ignore($product)],
            'category_id' => ['required', 'exists:categories,id'],
            'excerpt' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:20000'],
            'image' => self::IMAGE_RULES,
            'remove_image' => ['nullable', 'boolean'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'marketplace_links' => ['nullable', 'array'],
            'marketplace_links.*' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'focus_keyword' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:60', Rule::unique('products', 'sku')->ignore($product)],
            'material' => ['nullable', 'string', 'max:120'],
            'colour' => ['nullable', 'string', 'max:80'],
            'dimensions' => ['nullable', 'string', 'max:120'],
            'weight' => ['nullable', 'string', 'max:60'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'availability' => ['nullable', Rule::in(array_keys(Product::AVAILABILITY))],
        ] + $this->seoRules(), [
            'sku.unique' => 'Another product already uses this SKU.',
        ]);

        $product->fill([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? null,
            'category_id' => $data['category_id'],
            'excerpt' => $data['excerpt'],
            'description' => $data['description'],
            'focus_keyword' => $data['focus_keyword'] ?? null,
            'sku' => $data['sku'] ?? null,
            'material' => $data['material'] ?? null,
            'colour' => $data['colour'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'weight' => $data['weight'] ?? null,
            'price' => $data['price'] ?? null,
            'availability' => $data['availability'] ?? null,
            'image_alt' => $data['image_alt'] ?? null,
            'marketplace_links' => array_filter($data['marketplace_links'] ?? []),
            'sort_order' => $data['sort_order'] ?? 0,
            'featured' => $request->boolean('featured'),
            'is_active' => $request->boolean('is_active'),
            'image_path' => $this->replaceImage($request, 'image', 'remove_image', $product->image_path, 'products'),
        ] + $this->seoData($request, $data, $product->og_image));

        // Every product gets stored SEO fields: generate any left blank.
        ProductSeo::fillMissing($product);

        $product->save();
    }
}
