@extends('layouts.app')

@push('structured-data')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => setting('site_name'),
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => ['@type' => 'EntryPoint', 'urlTemplate' => route('shop').'?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
    <script type="application/ld+json">{!! json_encode(seo()->organizationSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <section class="hero">
        <div class="hero-copy">
            <span class="eyebrow">{{ $page->field('hero_eyebrow') }}</span>
            <h1>{{ rich_heading($page->field('hero_heading')) }}</h1>
            <p>{{ $page->field('hero_text') }}</p>
            <div class="hero-actions">
                <a class="button button-dark" href="{{ route('shop') }}">{{ $page->field('hero_button') }} <span aria-hidden="true">↗</span></a>
                <a class="text-link" href="{{ route('guides.index') }}">A guide to choosing your clock <span aria-hidden="true">→</span></a>
            </div>
            <div class="hero-note"><span class="note-star" aria-hidden="true">✳</span><span>Considered details.<br>Everyday kind of lovely.</span></div>
        </div>
        <div class="hero-visual" aria-hidden="true">
            <div class="hero-sun"></div>
            <div class="hero-arch"></div>
            <div class="hero-clock"><span class="hero-clock-center"></span><span class="hero-clock-hand hero-clock-hour"></span><span class="hero-clock-hand hero-clock-minute"></span><span class="clock-tick tick-1"></span><span class="clock-tick tick-2"></span><span class="clock-tick tick-3"></span><span class="clock-tick tick-4"></span></div>
            <div class="hero-vase"><span></span></div>
            <div class="hero-visual-label"><span>01 / 04</span><span>A softer sense of time</span></div>
        </div>
    </section>

    <section class="intro-strip">
        <span>{{ $page->field('intro_label') }}</span>
        <p>{{ rich_heading($page->field('intro_text')) }}</p>
        <a class="text-link" href="{{ route('about') }}">Our story <span aria-hidden="true">→</span></a>
    </section>

    @if ($categories->isNotEmpty())
        <section class="section section-collections">
            <div class="section-heading">
                <div><span class="eyebrow">{{ $page->field('collections_eyebrow') }}</span><h2>{{ $page->field('collections_heading') }}</h2></div>
                <a class="text-link" href="{{ route('shop') }}">View all styles <span aria-hidden="true">→</span></a>
            </div>
            <div class="collection-grid">
                @foreach ($categories as $category)
                    <a class="collection-tile collection-tile-{{ $loop->index % 4 }}" href="{{ route('collections.show', $category->slug) }}">
                        <span class="collection-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        @if ($category->image_path)
                            <img class="tile-image" src="{{ asset('storage/'.$category->image_path) }}" alt="" loading="lazy">
                        @else
                            <span class="tile-clock" aria-hidden="true"><i></i></span>
                        @endif
                        <span class="collection-title">{{ $category->name }} <span aria-hidden="true">↗</span></span>
                        <span class="collection-count">{{ $category->products_count }} {{ \Illuminate\Support\Str::plural('style', $category->products_count) }} to explore</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="feature-band">
        <div class="feature-art" aria-hidden="true"><div class="feature-clock"><i></i></div><span class="feature-line"></span></div>
        <div class="feature-copy">
            <span class="eyebrow">{{ $page->field('feature_eyebrow') }}</span>
            <h2>{{ rich_heading($page->field('feature_heading')) }}</h2>
            <p>{{ $page->field('feature_text') }}</p>
            <a class="button button-light" href="{{ route('guides.index') }}">Read our clock guides <span aria-hidden="true">↗</span></a>
        </div>
    </section>

    @if ($products->isNotEmpty())
        <section class="section section-products">
            <div class="section-heading">
                <div><span class="eyebrow">{{ $page->field('products_eyebrow') }}</span><h2>{{ $page->field('products_heading') }}</h2></div>
                <a class="text-link" href="{{ route('shop') }}">Browse the collection <span aria-hidden="true">→</span></a>
            </div>
            <div class="product-grid">
                @foreach ($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    <section class="quote-band">
        <span class="quote-mark" aria-hidden="true">“</span>
        <blockquote>{{ $page->field('quote') }}</blockquote>
        <span class="eyebrow">THE LAKSHIKA POINT OF VIEW</span>
    </section>

    <section class="section seo-intro">
        <div><span class="eyebrow">{{ $page->field('seo_eyebrow') }}</span><h2>{{ $page->field('seo_heading') }}</h2></div>
        <div class="seo-intro-copy">
            {{ paragraphs($page->field('seo_text')) }}
            <a class="text-link" href="{{ route('guides.index') }}">Explore all wall clock guides <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
