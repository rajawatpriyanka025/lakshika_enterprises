@extends('layouts.app')

@section('content')
    <section class="page-hero page-hero-compact">
        @include('partials.breadcrumbs')
        <span class="eyebrow">{{ $page->field('hero_eyebrow') }}</span>
        <h1>{{ rich_heading($page->field('hero_heading')) }}</h1>
        <p>{{ $page->field('hero_text') }}</p>
    </section>
    <section class="section guide-list">
        @foreach ($guides as $guide)
            <a class="guide-list-item" href="{{ route('guides.show', $guide->slug) }}">
                <span class="guide-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="guide-item-copy"><span class="eyebrow">THE WALL CLOCK NOTES</span><strong>{{ $guide->title }}</strong><small>{{ $guide->intro }}</small></span>
                <span class="guide-arrow" aria-hidden="true">↗</span>
            </a>
        @endforeach
        <a class="guide-list-item" href="{{ route('faqs') }}">
            <span class="guide-number">{{ str_pad($guides->count() + 1, 2, '0', STR_PAD_LEFT) }}</span>
            <span class="guide-item-copy"><span class="eyebrow">QUICK ANSWERS</span><strong>Wall clock FAQs</strong><small>Short answers about choosing, placing and finding a wall clock.</small></span>
            <span class="guide-arrow" aria-hidden="true">↗</span>
        </a>
    </section>
    <section class="guide-cta"><span class="eyebrow">START WITH A LOOK</span><h2>Found a style that feels like you?</h2><p>Browse wall clock collections for different rooms and ways of living.</p><a class="button button-light" href="{{ route('shop') }}">Explore all styles <span aria-hidden="true">↗</span></a></section>
@endsection
