@extends('layouts.app')

@push('structured-data')
    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $guide->title,
            'description' => seo()->metaDescription(),
            'image' => $guide->seoImageUrl() ?: asset('images/brand/logo-square.png'),
            'datePublished' => ($guide->published_at ?? $guide->created_at)?->toAtomString(),
            'dateModified' => $guide->updated_at?->toAtomString(),
            'mainEntityOfPage' => route('guides.show', $guide->slug),
            'author' => ['@type' => 'Organization', 'name' => setting('site_name'), 'url' => url('/')],
            'publisher' => ['@type' => 'Organization', 'name' => setting('site_name'), 'logo' => ['@type' => 'ImageObject', 'url' => asset('images/brand/logo-square.png')]],
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    <section class="article-hero">
        @include('partials.breadcrumbs')
        <span class="eyebrow">THE WALL CLOCK NOTES</span>
        <h1>{{ $guide->title }}</h1>
        <p>{{ $guide->intro }}</p>
        <div class="article-divider"><span>{{ strtoupper(setting('site_name')) }}</span><span>·</span><time datetime="{{ ($guide->published_at ?? $guide->created_at)?->toDateString() }}">{{ ($guide->published_at ?? $guide->created_at)?->format('j M Y') }}</time></div>
    </section>
    @if ($guide->image_path)
        <figure class="article-image"><img src="{{ asset('storage/'.$guide->image_path) }}" alt="{{ $guide->image_alt ?: $guide->title }}"></figure>
    @endif
    <article class="article-body">
        @foreach ($guide->sections ?? [] as $section)
            <section>
                <span class="article-section-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <div>
                    @if (filled($section['heading'] ?? null))<h2>{{ $section['heading'] }}</h2>@endif
                    {{ paragraphs($section['body'] ?? '') }}
                </div>
            </section>
        @endforeach
        <div class="article-endnote"><span class="note-star" aria-hidden="true">✳</span><p>Explore <a href="{{ route('shop') }}">wall clock styles from {{ setting('site_name') }}</a>@if ($moreGuides->isNotEmpty()), or keep reading:
            @foreach ($moreGuides as $more)<a href="{{ route('guides.show', $more->slug) }}">{{ $more->title }}</a>@if (! $loop->last), @endif @endforeach
            @else or browse more <a href="{{ route('guides.index') }}">thoughtful clock guides</a>@endif.</p></div>
    </article>
@endsection
