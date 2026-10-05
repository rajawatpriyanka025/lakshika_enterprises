<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Guide;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $page = Page::for('home');
        seo()->forPage($page);

        return view('pages.home', [
            'page' => $page,
            'categories' => Category::query()->active()->ordered()->withCount(['products' => fn ($query) => $query->active()])->get(),
            'products' => Product::query()->active()->with('category')->where('featured', true)->orderBy('sort_order')->orderBy('name')->limit(4)->get(),
        ]);
    }

    public function guides(): View
    {
        $page = Page::for('guides');
        seo()->forPage($page)->breadcrumbs(['Guides' => null]);

        return view('pages.guides', [
            'page' => $page,
            'guides' => Guide::query()->published()->ordered()->get(),
        ]);
    }

    public function guide(string $slug): View
    {
        $guide = Guide::query()->published()->where('slug', $slug)->firstOrFail();

        seo()->forModel($guide, $guide->title, $guide->intro)
            ->set(['type' => 'article'])
            ->breadcrumbs(['Guides' => route('guides.index'), $guide->title => null]);

        return view('pages.guide', [
            'guide' => $guide,
            'moreGuides' => Guide::query()->published()->ordered()->whereKeyNot($guide->id)->limit(3)->get(),
        ]);
    }

    public function about(): View
    {
        $page = Page::for('about');
        seo()->forPage($page)->breadcrumbs(['About' => null]);

        return view('pages.about', ['page' => $page]);
    }

    public function contact(): View
    {
        $page = Page::for('contact');
        seo()->forPage($page)->breadcrumbs(['Contact' => null]);

        return view('pages.contact', ['page' => $page]);
    }

    public function faqs(): View
    {
        $page = Page::for('faqs');
        seo()->forPage($page)->breadcrumbs(['FAQs' => null]);

        return view('pages.faqs', [
            'page' => $page,
            'faqs' => Faq::query()->active()->get(),
        ]);
    }
}
