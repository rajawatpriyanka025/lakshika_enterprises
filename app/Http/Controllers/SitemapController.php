<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Guide;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $pages = Page::query()->get()->keyBy('key');
        $hidden = $pages->filter(fn (Page $page) => $page->noindex)->keys()->all();

        $staticPages = collect([
            ['key' => 'home', 'url' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['key' => 'shop', 'url' => route('shop'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['key' => 'guides', 'url' => route('guides.index'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['key' => 'faqs', 'url' => route('faqs'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['key' => 'about', 'url' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['key' => 'contact', 'url' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ])->reject(fn ($page) => in_array($page['key'], $hidden, true))
            ->map(fn ($page) => $page + ['lastmod' => $pages->get($page['key'])?->updated_at]);

        return response()
            ->view('seo.sitemap', [
                'staticPages' => $staticPages,
                'categories' => Category::query()->active()->where('noindex', false)->whereNull('canonical_url')->ordered()->get(),
                'products' => Product::query()->active()->where('noindex', false)->whereNull('canonical_url')->orderBy('updated_at')->get(),
                'guides' => Guide::query()->published()->where('noindex', false)->whereNull('canonical_url')->ordered()->get(),
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
