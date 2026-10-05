@extends('layouts.app')

@push('structured-data')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => seo()->metaDescription(),
            'url' => route('collections.show', $category->slug),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $products->total(),
                'itemListElement' => $products->values()->map(fn ($product, $index) => [
                    '@type' => 'ListItem',
                    'position' => $products->firstItem() + $index,
                    'url' => route('products.show', $product->slug),
                    'name' => $product->name,
                ])->all(),
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    <section class="page-hero page-hero-compact">
        @include('partials.breadcrumbs')
        <span class="eyebrow">A COLLECTION FOR YOUR HOME</span>
        <h1>{{ $category->name }}<br><em>with a little more feeling.</em></h1>
        {{ paragraphs($category->description) }}
    </section>

    <section class="section catalog-section">
        @if ($products->isNotEmpty())
            <div class="product-grid">
                @foreach ($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="pagination-wrap">{{ $products->links() }}</div>
        @else
            <div class="empty-state"><span class="eyebrow">NEW STYLES COMING SOON</span><h2>We are refreshing this collection.</h2><p>In the meantime, explore everything in our collection.</p><a class="button button-dark" href="{{ route('shop') }}">Explore all styles</a></div>
        @endif
    </section>

    <section class="guide-cta"><span class="eyebrow">A FEW THOUGHTFUL TIPS</span><h2>Still deciding?</h2><p>Our guides make it easier to think about size, placement and the little details that bring a room together.</p><a class="button button-light" href="{{ route('guides.index') }}">Browse the guides <span aria-hidden="true">↗</span></a></section>
@endsection
