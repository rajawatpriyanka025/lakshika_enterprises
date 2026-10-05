<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $page = Page::for('shop');

        $products = Product::query()
            ->active()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->latest('featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        seo()->forPage($page)->breadcrumbs(['Shop' => null]);

        if ($search !== '') {
            // Internal search results are thin, duplicate pages: keep them out of the index.
            seo()->set(['title' => "Search results for “{$search}”", 'noindex' => true, 'canonical' => route('shop')]);
        }

        return view('catalog.index', [
            'page' => $page,
            'products' => $products,
            'categories' => Category::query()->active()->ordered()->get(),
            'search' => $search,
        ]);
    }

    public function category(Category $category): View
    {
        abort_unless($category->is_active, 404);

        $products = $category->products()
            ->active()
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12);

        seo()->forModel($category, $category->name, $category->description)
            ->breadcrumbs(['Shop' => route('shop'), $category->name => null]);

        return view('catalog.category', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    public function product(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        $relatedProducts = Product::query()
            ->active()
            ->with('category')
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(3)
            ->get();

        seo()->forModel($product, $product->name, $product->excerpt)
            ->set(['type' => 'product'])
            ->breadcrumbs([
                $product->category->name => route('collections.show', $product->category->slug),
                $product->name => null,
            ]);

        return view('catalog.product', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
