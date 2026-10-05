@extends('layouts.app')

@push('structured-data')
    @if ($faqs->isNotEmpty())
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqs->map(fn ($faq) => [
                    '@type' => 'Question',
                    'name' => $faq->question,
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
                ])->values()->all(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
        </script>
    @endif
@endpush

@section('content')
    <section class="page-hero page-hero-compact">
        @include('partials.breadcrumbs')
        <span class="eyebrow">{{ $page->field('hero_eyebrow') }}</span>
        <h1>{{ rich_heading($page->field('hero_heading')) }}</h1>
        <p>{{ $page->field('hero_text') }}</p>
    </section>
    <section class="section faq-list">
        @foreach ($faqs as $faq)
            <article class="faq-item">
                <span class="faq-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <div><h2>{{ $faq->question }}</h2>{{ paragraphs($faq->answer) }}</div>
            </article>
        @endforeach
        <div class="faq-footnote"><p>Still have a question? <a href="{{ route('contact') }}#enquiry">Message us on WhatsApp</a> or explore the <a href="{{ route('shop') }}">full collection</a>.</p></div>
    </section>
@endsection
