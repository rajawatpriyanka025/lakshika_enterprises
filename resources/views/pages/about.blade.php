@extends('layouts.app')

@section('content')
    <section class="page-hero page-hero-about">
        @include('partials.breadcrumbs')
        <span class="eyebrow">{{ $page->field('hero_eyebrow') }}</span>
        <h1>{{ rich_heading($page->field('hero_heading')) }}</h1>
        <p>{{ $page->field('hero_text') }}</p>
    </section>
    <section class="section story-section">
        <div class="story-visual"><div class="story-sun"></div><div class="story-clock" aria-hidden="true"><i></i><b></b></div><span class="story-caption">A THOUGHTFUL DETAIL, EVERY DAY</span></div>
        <div class="story-copy">
            <span class="eyebrow">{{ $page->field('story_eyebrow') }}</span>
            <h2>{{ rich_heading($page->field('story_heading')) }}</h2>
            {{ paragraphs($page->field('story_text')) }}
            <a class="text-link" href="{{ route('shop') }}">Get to know the collection <span aria-hidden="true">→</span></a>
        </div>
    </section>
    <section class="guide-cta"><span class="eyebrow">OUR FAVORITE KIND OF DETAIL</span><h2>{{ $page->field('cta_heading') }}</h2><p>{{ $page->field('cta_text') }}</p><a class="button button-light" href="{{ route('shop') }}">Find your kind of clock <span aria-hidden="true">↗</span></a></section>
@endsection
