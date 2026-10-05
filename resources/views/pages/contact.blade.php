@extends('layouts.app')

@php
    $phone = setting('contact_phone');
    $email = setting('contact_email');
    $address = setting('business_address');
    $hours = setting('business_hours');
    $mapUrl = map_embed_url();
    $rating = (float) setting('google_rating');
    $reviewCount = (int) setting('google_review_count');
    $hasWhatsapp = (bool) setting('whatsapp_number');
@endphp

@push('structured-data')
    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => setting('site_name'),
            'url' => url('/'),
            'image' => asset('images/brand/logo-square.png'),
            'telephone' => $phone,
            'email' => $email,
            'address' => $address ? ['@type' => 'PostalAddress', 'streetAddress' => $address, 'addressCountry' => 'IN'] : null,
            'hasMap' => setting('google_maps_url'),
            'sameAs' => array_values(array_filter([setting('instagram_url'), setting('facebook_url'), setting('google_reviews_url')])) ?: null,
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    <section class="page-hero page-hero-compact">
        @include('partials.breadcrumbs')
        <span class="eyebrow">{{ $page->field('hero_eyebrow') }}</span>
        <h1>{{ rich_heading($page->field('hero_heading')) }}</h1>
        <p>{{ $page->field('hero_text') }}</p>
    </section>

    <section class="section contact-section" id="enquiry">
        <div class="contact-copy">
            <span class="eyebrow">ENQUIRIES &amp; BULK ORDERS</span>
            <h2>{{ $page->field('form_heading') }}</h2>
            <p>{{ $page->field('form_text') }}</p>
            @if ($hasWhatsapp)
                <a class="button button-whatsapp contact-whatsapp-main" href="{{ route('whatsapp') }}" target="_blank" rel="nofollow noopener">Chat with us on WhatsApp <span aria-hidden="true">↗</span></a>
            @endif
        </div>
        @if ($hasWhatsapp)
            <div class="whatsapp-topics">
                @foreach (['product' => 'Ask about a product', 'bulk' => 'Bulk / wholesale order', 'dealer' => 'Become a dealer', 'corporate' => 'Corporate gifting'] as $topic => $topicLabel)
                    <a class="marketplace-link" href="{{ route('whatsapp', ['type' => $topic]) }}" target="_blank" rel="nofollow noopener">
                        <span><span class="eyebrow">ON WHATSAPP</span><strong>{{ $topicLabel }}</strong></span>
                        <span class="marketplace-arrow" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="section contact-details-section {{ $mapUrl ? '' : 'no-map' }}">
        <div class="contact-cards">
            @if ($phone)
                <a class="contact-card" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">
                    <span class="eyebrow">CALL US</span><strong>{{ $phone }}</strong>
                </a>
            @endif
            @if ($hasWhatsapp)
                <a class="contact-card" href="{{ route('whatsapp') }}" target="_blank" rel="nofollow noopener">
                    <span class="eyebrow">WHATSAPP</span><strong>+{{ preg_replace('/^(\d{2})(\d{5})(\d{5})$/', '$1 $2 $3', preg_replace('/\D+/', '', setting('whatsapp_number'))) }}</strong>
                </a>
            @endif
            @if ($email)
                <a class="contact-card" href="mailto:{{ $email }}">
                    <span class="eyebrow">EMAIL</span><strong>{{ $email }}</strong>
                </a>
            @endif
            @if ($address)
                <div class="contact-card">
                    <span class="eyebrow">VISIT US</span><strong>{!! nl2br(e($address)) !!}</strong>
                    @if (setting('google_maps_url'))<a class="text-link" href="{{ setting('google_maps_url') }}" target="_blank" rel="noopener">Get directions <span aria-hidden="true">→</span></a>@endif
                </div>
            @endif
            @if ($hours)
                <div class="contact-card">
                    <span class="eyebrow">BUSINESS HOURS</span><strong>{!! nl2br(e($hours)) !!}</strong>
                </div>
            @endif
        </div>

        @if ($mapUrl)
            <div class="contact-map">
                <iframe src="{{ $mapUrl }}" title="Map showing {{ setting('site_name') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        @endif
    </section>

    @if ($rating || setting('google_reviews_url') || setting('google_review_write_url'))
        <section class="section google-reviews">
            <div>
                <span class="eyebrow">WHAT OUR CUSTOMERS SAY</span>
                <h2>Reviewed on Google</h2>
                @if ($rating)
                    <div class="rating" aria-label="Rated {{ number_format($rating, 1) }} out of 5 on Google">
                        <span class="rating-stars" style="--rating: {{ min(5, $rating) }}" aria-hidden="true">★★★★★</span>
                        <strong>{{ number_format($rating, 1) }}</strong>
                        @if ($reviewCount)<span>from {{ number_format($reviewCount) }} {{ \Illuminate\Support\Str::plural('review', $reviewCount) }}</span>@endif
                    </div>
                @endif
            </div>
            <div class="google-review-actions">
                @if (setting('google_reviews_url'))
                    <a class="button button-dark" href="{{ setting('google_reviews_url') }}" target="_blank" rel="noopener">Read our Google reviews <span aria-hidden="true">↗</span></a>
                @endif
                @if (setting('google_review_write_url'))
                    <a class="button button-outline" href="{{ setting('google_review_write_url') }}" target="_blank" rel="noopener">Write a review</a>
                @endif
            </div>
        </section>
    @endif

    @if ($marketplaces->isNotEmpty())
        <section class="section contact-section">
            <div class="contact-copy">
                <span class="eyebrow">SHOP WITH US ONLINE</span>
                <h2>{{ $page->field('marketplaces_heading') }}</h2>
                <p>{{ $page->field('marketplaces_text') }}</p>
            </div>
            <div class="contact-marketplaces">
                @foreach ($marketplaces as $marketplace)
                    <a class="marketplace-link" href="{{ $marketplace->brandUrl() }}" target="_blank" rel="nofollow sponsored noopener">
                        <span><span class="eyebrow">FIND OUR LISTINGS</span><strong>{{ $marketplace->name }}</strong></span>
                        <span class="marketplace-arrow" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
    <section class="faq-footnote contact-faq"><p>Not sure what suits your space? Start with our <a href="{{ route('guides.index') }}">buying guides</a> or browse the <a href="{{ route('shop') }}">full collection</a>.</p></section>
@endsection
