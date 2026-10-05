@php
    $siteName = setting('site_name');
    $phone = setting('contact_phone');
    $email = setting('contact_email');
    $backAt = setting('maintenance_back_at');
@endphp
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Back soon · {{ $siteName }}</title>
    <meta name="theme-color" content="#f5f7fa">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
</head>
<body class="maintenance-body">
    @if ($preview)
        <div class="maintenance-preview">Preview: maintenance mode is off, so visitors can't see this page.</div>
    @endif
    <main class="maintenance">
        <img class="maintenance-logo" src="{{ asset('images/brand/logo.png') }}" alt="{{ $siteName }}" width="908" height="597">
        <span class="eyebrow">BACK SOON</span>
        <h1>{{ setting('maintenance_heading') }}</h1>
        <p>{!! nl2br(e(setting('maintenance_message'))) !!}</p>
        @if ($backAt)
            <p class="maintenance-back">Expected back: <strong>{{ $backAt }}</strong></p>
        @endif

        @if (setting('whatsapp_number') || $phone || $email)
            <div class="maintenance-actions">
                @if (setting('whatsapp_number'))
                    <a class="button button-whatsapp" href="{{ route('whatsapp') }}" target="_blank" rel="nofollow noopener">Chat on WhatsApp</a>
                @endif
                @if ($phone)
                    <a class="button button-outline" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">Call {{ $phone }}</a>
                @endif
            </div>
            @if ($email)
                <p class="maintenance-email">Or email us at <a href="mailto:{{ $email }}">{{ $email }}</a></p>
            @endif
        @endif
    </main>
</body>
</html>
