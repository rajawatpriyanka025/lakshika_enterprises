<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · {{ setting('site_name') }} admin</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body>
@php
    $nav = [
        'Overview' => [
            ['Dashboard', 'admin.dashboard', 'admin.dashboard'],
        ],
        'CRM' => [
            ['Leads', 'admin.leads.index', 'admin.leads.*', $newLeadCount],
            ['Customers & dealers', 'admin.customers.index', 'admin.customers.*'],
            ['WhatsApp enquiries', 'admin.whatsapp-clicks.index', 'admin.whatsapp-clicks.*'],
        ],
        'Catalogue' => [
            ['Products', 'admin.products.index', 'admin.products.*'],
            ['Collections', 'admin.categories.index', 'admin.categories.*'],
            ['Marketplaces', 'admin.marketplaces.index', 'admin.marketplaces.*'],
        ],
        'Website' => [
            ['Pages', 'admin.pages.index', 'admin.pages.*'],
            ['Guides', 'admin.guides.index', 'admin.guides.*'],
            ['FAQs', 'admin.faqs.index', 'admin.faqs.*'],
        ],
        'SEO' => [
            ['Product SEO', 'admin.product-seo.index', 'admin.product-seo.*'],
            ['SEO & tracking', ['admin.settings.edit', 'seo'], null, null, request()->routeIs('admin.settings.*') && request()->route('group') === 'seo'],
            ['Redirects', 'admin.redirects.index', 'admin.redirects.*'],
        ],
        'Settings' => [
            ['Business details', ['admin.settings.edit', 'general'], null, null, request()->routeIs('admin.settings.*') && in_array(request()->route('group'), [null, 'general'], true)],
            ['Leads & WhatsApp', ['admin.settings.edit', 'crm'], null, null, request()->routeIs('admin.settings.*') && request()->route('group') === 'crm'],
            ['Contact, map & reviews', ['admin.settings.edit', 'contact'], null, null, request()->routeIs('admin.settings.*') && request()->route('group') === 'contact'],
            ['Welcome popup', ['admin.settings.edit', 'popup'], null, null, request()->routeIs('admin.settings.*') && request()->route('group') === 'popup'],
            ['Social profiles', ['admin.settings.edit', 'social'], null, null, request()->routeIs('admin.settings.*') && request()->route('group') === 'social'],
            ['My account', 'admin.account.edit', 'admin.account.*'],
        ],
    ];
@endphp
<div class="admin-shell">
    <div class="admin-topbar">
        <strong>{{ setting('site_name') }}</strong>
        <button type="button" data-nav-toggle aria-expanded="false" aria-controls="admin-sidebar">Menu</button>
    </div>
    <aside class="admin-sidebar" id="admin-sidebar">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/brand/favicon-512.png') }}" alt="">
            <span><strong>{{ setting('site_name') }}</strong><small>Admin &amp; CRM</small></span>
        </a>
        <nav aria-label="Admin">
            @foreach ($nav as $group => $items)
                <div class="nav-group">
                    <span>{{ $group }}</span>
                    @foreach ($items as $item)
                        @php
                            [$label, $route] = $item;
                            $href = is_array($route) ? route($route[0], $route[1]) : route($route);
                            $active = $item[4] ?? request()->routeIs($item[2]);
                            $badge = $item[3] ?? null;
                        @endphp
                        <a class="nav-link {{ $active ? 'is-active' : '' }}" href="{{ $href }}" @if ($active) aria-current="page" @endif>
                            <span>{{ $label }}</span>
                            @if ($badge)<span class="nav-badge" title="{{ $badge }} new">{{ $badge }}</span>@endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
        <div class="sidebar-footer">
            <a class="nav-link" href="{{ route('home') }}" target="_blank" rel="noopener">View website ↗</a>
            <a class="nav-link" href="{{ route('sitemap') }}" target="_blank" rel="noopener">Sitemap.xml ↗</a>
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Sign out ({{ auth()->user()->name }})</button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="page-header">
            <div>
                <h1>@yield('title')</h1>
                @hasSection('subtitle')<p>@yield('subtitle')</p>@endif
            </div>
            <div class="page-actions">@yield('actions')</div>
        </div>

        @if (session('status'))
            <div class="flash flash-success" role="status">✓ {{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="flash flash-error" role="alert">
                <div>
                    <strong>Please fix the following:</strong>
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</div>
<script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}" defer></script>
</body>
</html>
