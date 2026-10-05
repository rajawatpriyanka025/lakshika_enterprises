@extends('layouts.app')

@php
    $schemaAvailability = [
        'in_stock' => 'https://schema.org/InStock',
        'out_of_stock' => 'https://schema.org/OutOfStock',
        'pre_order' => 'https://schema.org/PreOrder',
    ];
    $productSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => seo()->metaDescription(),
        'category' => $product->category->name,
        'brand' => ['@type' => 'Brand', 'name' => setting('site_name')],
        'url' => route('products.show', $product->slug),
        'image' => $product->image_path ? asset('storage/'.$product->image_path) : null,
        'sku' => $product->sku,
        'material' => $product->material,
        'color' => $product->colour,
        'size' => $product->dimensions,
        'weight' => $product->weight,
        'offers' => $product->price !== null ? array_filter([
            '@type' => 'Offer',
            'price' => number_format((float) $product->price, 2, '.', ''),
            'priceCurrency' => 'INR',
            'availability' => $schemaAvailability[$product->availability] ?? null,
            'url' => route('products.show', $product->slug),
            'seller' => ['@type' => 'Organization', 'name' => setting('site_name')],
        ]) : null,
    ], fn ($value) => filled($value));
@endphp

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <section class="product-detail section">
        @include('partials.breadcrumbs')
        <div class="product-detail-grid">
            <div class="product-detail-art product-art-{{ crc32($product->slug) % 4 }}">
                @if ($product->image_path)
                    <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->image_alt ?: $product->name }}" width="800" height="800">
                @else
                    <span class="clock-illustration clock-illustration-large" aria-hidden="true"><i></i><b></b></span>
                @endif
                <span class="art-caption">A thoughtful detail</span>
            </div>
            <div class="product-detail-copy">
                <a class="eyebrow" href="{{ route('collections.show', $product->category->slug) }}">{{ $product->category->name }}</a>
                <h1>{{ $product->name }}</h1>
                <p class="product-lead">{{ $product->excerpt }}</p>
                @if ($product->price !== null || $product->availability)
                    <p class="product-price">
                        @if ($product->price !== null)<strong>₹{{ number_format((float) $product->price, fmod((float) $product->price, 1) ? 2 : 0) }}</strong>@endif
                        @if ($product->availability)<span class="availability availability-{{ $product->availability }}">{{ \App\Models\Product::AVAILABILITY[$product->availability] ?? '' }}</span>@endif
                    </p>
                @endif
                <div class="product-description">{{ paragraphs($product->description) }}</div>
                @if ($specifications = $product->specifications())
                    <table class="product-specs">
                        <caption class="sr-only">Specifications</caption>
                        <tbody>
                            @foreach ($specifications as $specLabel => $specValue)
                                <tr><th scope="row">{{ $specLabel }}</th><td>{{ $specValue }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if ($marketplaces->isNotEmpty())
                    <div class="detail-note"><span class="note-star" aria-hidden="true">✳</span><p>See current product details and availability directly on your preferred marketplace.</p></div>
                    <div class="marketplace-buttons">
                        @foreach ($marketplaces as $marketplace)
                            <a class="button button-dark" href="{{ $product->marketplaceUrl($marketplace) }}" target="_blank" rel="nofollow sponsored noopener">{{ $product->hasDirectListing($marketplace) ? 'Buy on' : 'Search on' }} {{ $marketplace->name }} <span aria-hidden="true">↗</span></a>
                        @endforeach
                    </div>
                    <p class="marketplace-disclaimer">Marketplace pages open in a new tab. Availability, pricing and delivery are confirmed by the marketplace.</p>
                @endif
                @if (setting('whatsapp_number'))
                    <div class="product-enquiry-actions">
                        <a class="button button-whatsapp" href="{{ route('whatsapp', ['product' => $product->slug]) }}" target="_blank" rel="nofollow noopener">Enquire about this product on WhatsApp <span aria-hidden="true">↗</span></a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if (setting('whatsapp_number'))
        <section class="section product-enquiry-section">
            <div class="contact-copy">
                <span class="eyebrow">QUESTIONS OR BULK ORDERS</span>
                <h2>Enquire about the {{ $product->name }}.</h2>
                <p>Ask about sizes, finishes, bulk pricing or delivery to your city. Your message opens in WhatsApp with this product already included.</p>
            </div>
            <div class="whatsapp-topics">
                @foreach (['product' => 'Ask a question', 'bulk' => 'Bulk / wholesale price', 'corporate' => 'Corporate gifting', 'dealer' => 'Become a dealer'] as $topic => $topicLabel)
                    <a class="marketplace-link" href="{{ route('whatsapp', ['product' => $product->slug, 'type' => $topic]) }}" target="_blank" rel="nofollow noopener">
                        <span><span class="eyebrow">ON WHATSAPP</span><strong>{{ $topicLabel }}</strong></span>
                        <span class="marketplace-arrow" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($relatedProducts->isNotEmpty())
        <section class="section related-section">
            <div class="section-heading"><div><span class="eyebrow">MORE TO DISCOVER</span><h2>More from this collection.</h2></div><a class="text-link" href="{{ route('collections.show', $product->category->slug) }}">See the collection <span aria-hidden="true">→</span></a></div>
            <div class="product-grid">
                @foreach ($relatedProducts as $relatedProduct)
                    @include('partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </section>
    @endif
@endsection
