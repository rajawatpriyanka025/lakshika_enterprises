@php
    $siteName = setting('site_name');
    $seo = seo();
    $socials = collect(config('settings.groups.social.fields'))->mapWithKeys(fn ($field, $key) => [$field['label'] => setting($key)])->filter();
@endphp
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seo->fullTitle() }}</title>
    <meta name="description" content="{{ $seo->metaDescription() }}">
    <meta name="robots" content="{{ $seo->robots() }}">
    <link rel="canonical" href="{{ $seo->canonicalUrl() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_IN">
    <meta property="og:type" content="{{ $seo->type }}">
    <meta property="og:title" content="{{ $seo->fullTitle() }}">
    <meta property="og:description" content="{{ $seo->metaDescription() }}">
    <meta property="og:url" content="{{ $seo->canonicalUrl() }}">
    <meta property="og:image" content="{{ $seo->imageUrl() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->fullTitle() }}">
    <meta name="twitter:description" content="{{ $seo->metaDescription() }}">
    <meta name="twitter:image" content="{{ $seo->imageUrl() }}">
    @if (setting('google_site_verification'))
        <meta name="google-site-verification" content="{{ setting('google_site_verification') }}">
    @endif
    @if (setting('bing_site_verification'))
        <meta name="msvalidate.01" content="{{ setting('bing_site_verification') }}">
    @endif
    <meta name="theme-color" content="#f5f7fa">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('images/brand/favicon-512.png') }}" sizes="512x512">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/favicon-512.png') }}">
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
    @if ($breadcrumbSchema = $seo->breadcrumbSchema())
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endif
    @stack('structured-data')
    @include('partials.tracking-head')
</head>
<body>
    @include('partials.tracking-body')
    <a class="skip-link" href="#main">Skip to content</a>
    @if (setting('announcement'))
        <div class="announcement">{{ setting('announcement') }}</div>
    @endif
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="{{ $siteName }} home">
                <img class="brand-logo" src="{{ asset('images/brand/logo.png') }}" alt="{{ $siteName }}" width="908" height="597">
            </a>
            <nav class="main-nav" aria-label="Main navigation">
                <a href="{{ route('shop') }}" @if (request()->routeIs('shop', 'collections.*', 'products.*')) aria-current="page" @endif>Shop all</a>
                <a href="{{ route('guides.index') }}" @if (request()->routeIs('guides.*')) aria-current="page" @endif>Guides</a>
                <a href="{{ route('faqs') }}" @if (request()->routeIs('faqs')) aria-current="page" @endif>FAQs</a>
                <a href="{{ route('about') }}" @if (request()->routeIs('about')) aria-current="page" @endif>Our story</a>
                <a href="{{ route('contact') }}" @if (request()->routeIs('contact')) aria-current="page" @endif>Contact</a>
            </nav>
            @if (setting('whatsapp_number'))
                <a class="header-cta" href="{{ route('whatsapp') }}" target="_blank" rel="nofollow noopener">Enquire on WhatsApp <span aria-hidden="true">↗</span></a>
            @else
                <a class="header-cta" href="{{ route('contact') }}">Contact us <span aria-hidden="true">↗</span></a>
            @endif
        </div>
    </header>

    <main id="main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="footer-main">
            <div class="footer-brand">
                <a class="brand brand-light" href="{{ route('home') }}">
                    <img class="brand-logo" src="{{ asset('images/brand/logo.png') }}" alt="{{ $siteName }}" width="908" height="597" loading="lazy">
                </a>
                <p>{{ setting('footer_text') }}</p>
                @if (setting('contact_phone') || setting('contact_email'))
                    <p class="footer-contact">
                        @if (setting('contact_phone'))<a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone')) }}">{{ setting('contact_phone') }}</a>@endif
                        @if (setting('contact_email'))<a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a>@endif
                    </p>
                @endif
            </div>
            <div class="footer-links">
                <strong>Explore</strong>
                <a href="{{ route('shop') }}">Shop all products</a>
                <a href="{{ route('guides.index') }}">Buying guides</a>
                <a href="{{ route('faqs') }}">FAQs</a>
                <a href="{{ route('about') }}">About us</a>
                <a href="{{ route('contact') }}">Contact &amp; bulk orders</a>
            </div>
            <div class="footer-links">
                <strong>Find us online</strong>
                @foreach ($marketplaces as $marketplace)
                    <a href="{{ $marketplace->brandUrl() }}" target="_blank" rel="nofollow sponsored noopener">{{ $marketplace->name }} <span aria-hidden="true">↗</span></a>
                @endforeach
                @foreach ($socials as $label => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener me">{{ $label }} <span aria-hidden="true">↗</span></a>
                @endforeach
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ now()->year }} {{ $siteName }}</span>
            <span>{{ setting('tagline') }}</span>
        </div>
    </footer>

    @if (setting('popup_enabled'))
        @include('partials.welcome-popup')
    @endif

    @if (setting('whatsapp_number'))
        @php($currentProduct = request()->route('product'))
        <a class="whatsapp-float" href="{{ route('whatsapp', $currentProduct instanceof \App\Models\Product ? ['product' => $currentProduct->slug] : []) }}" target="_blank" rel="nofollow noopener" aria-label="Chat with us on WhatsApp">
            <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path fill="currentColor" d="M12.04 2a9.9 9.9 0 0 0-8.5 14.98L2 22l5.17-1.5A9.9 9.9 0 1 0 12.04 2Zm0 18.1a8.2 8.2 0 0 1-4.2-1.15l-.3-.18-3.07.9.92-2.99-.2-.31a8.2 8.2 0 1 1 6.85 3.73Zm4.5-6.14c-.25-.12-1.46-.72-1.69-.8-.23-.08-.39-.12-.55.12-.17.25-.64.8-.78.97-.14.16-.29.18-.53.06a6.7 6.7 0 0 1-3.32-2.9c-.25-.43.25-.4.72-1.33.08-.16.04-.3-.02-.43l-.75-1.8c-.2-.48-.4-.41-.55-.42h-.47a.9.9 0 0 0-.65.3 2.74 2.74 0 0 0-.86 2.04 4.76 4.76 0 0 0 1 2.53 10.9 10.9 0 0 0 4.17 3.68c1.55.67 2.16.73 2.94.61.47-.07 1.46-.6 1.66-1.17.21-.58.21-1.07.15-1.17-.06-.1-.22-.17-.47-.29Z"/></svg>
        </a>
    @endif
</body>
</html>
