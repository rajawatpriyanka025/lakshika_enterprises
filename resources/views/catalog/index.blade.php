@extends('layouts.app')

@section('content')
    <section class="page-hero page-hero-compact">
        @include('partials.breadcrumbs')
        <span class="eyebrow">{{ $page->field('hero_eyebrow') }}</span>
        <h1>{{ $search !== '' ? 'A good place to start.' : rich_heading($page->field('hero_heading')) }}</h1>
        <p>{{ $search !== '' ? 'Showing styles related to “'.$search.'”.' : $page->field('hero_text') }}</p>
    </section>

    <section class="section catalog-section">
        <div class="catalog-toolbar">
            <form class="search-form" action="{{ route('shop') }}" method="get" role="search">
                <label class="sr-only" for="catalog-search">Search products</label>
                <input id="catalog-search" type="search" name="q" value="{{ $search }}" placeholder="Search products">
                <button type="submit" aria-label="Search">⌕</button>
            </form>
            <span class="result-count">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('style', $products->total()) }}</span>
        </div>
        <nav class="category-chips" aria-label="Browse by collection">
            <a class="category-chip {{ $search === '' ? 'is-active' : '' }}" href="{{ route('shop') }}">All styles</a>
            @foreach ($categories as $category)
                <a class="category-chip" href="{{ route('collections.show', $category->slug) }}">{{ $category->name }}</a>
            @endforeach
        </nav>
        @if ($products->isNotEmpty())
            <div class="product-grid">
                @foreach ($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="pagination-wrap">{{ $products->links() }}</div>
        @else
            <div class="empty-state">
                <span class="eyebrow">NO MATCHES JUST YET</span>
                <h2>Let's try another search.</h2>
                <p>Try a room name like “kitchen” or a style such as “modern” — or <a href="{{ route('contact') }}#enquiry">tell us what you need</a>.</p>
                <a class="button button-dark" href="{{ route('shop') }}">See all products</a>
            </div>
        @endif
    </section>

    <section class="marketplace-note"><span class="eyebrow">BUYING IN BULK?</span><p>Ask about wholesale, dealer and corporate gifting orders.</p><a class="text-link" href="{{ setting('whatsapp_number') ? route('whatsapp', ['type' => 'bulk']) : route('contact') }}" target="_blank" rel="nofollow noopener">Send a bulk enquiry on WhatsApp <span aria-hidden="true">→</span></a></section>
@endsection
