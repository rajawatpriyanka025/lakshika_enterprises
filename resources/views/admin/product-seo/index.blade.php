@extends('admin.layouts.app')

@section('title', 'Product SEO')
@section('subtitle', 'Focus keyword, meta title, description and image alt text for every product. Edit as many rows as you like, then save once.')

@section('actions')
    <form class="inline-form" method="post" action="{{ route('admin.product-seo.autofill') }}" data-confirm="Generate a meta title, description and alt text for every product that is missing them? Existing text is not changed.">
        @csrf
        <button class="btn" type="submit">Auto-fill missing</button>
    </form>
    <button class="btn btn-primary" type="submit" form="product-seo-form">Save all</button>
@endsection

@section('content')
    <div class="stat-grid">
        <div class="stat"><span>Average SEO score</span><strong><span class="score score-{{ \App\Support\ProductSeo::grade($average) }}">{{ $average }}%</span></strong><small>across {{ $rows->count() }} {{ \Illuminate\Support\Str::plural('product', $rows->count()) }} shown</small></div>
        <div class="stat"><span>Missing meta title or description</span><strong>{{ $missingMeta }}</strong><small>{{ $missingMeta ? 'use Auto-fill missing' : 'all set' }}</small></div>
        <div class="stat"><span>Without a focus keyword</span><strong>{{ $missingKeyword }}</strong><small>add one per product</small></div>
    </div>

    <form class="filters" method="get" role="search">
        <div class="field"><label class="sr-only" for="q">Search</label><input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search products…"></div>
        <div class="field">
            <label class="sr-only" for="category">Collection</label>
            <select id="category" name="category">
                <option value="">All collections</option>
                @foreach ($categories as $categoryId => $categoryName)
                    <option value="{{ $categoryId }}" @selected((string) ($filters['category'] ?? '') === (string) $categoryId)>{{ $categoryName }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="sr-only" for="show">Show</label>
            <select id="show" name="show">
                <option value="">All products</option>
                <option value="issues" @selected(($filters['show'] ?? '') === 'issues')>Needs work (under 80%)</option>
            </select>
        </div>
        <button class="btn" type="submit">Filter</button>
        @if (array_filter($filters))<a class="btn" href="{{ route('admin.product-seo.index') }}">Clear</a>@endif
    </form>

    <form id="product-seo-form" method="post" action="{{ route('admin.product-seo.update') }}">
        @csrf
        @method('PUT')
        <div class="seo-rows">
            @forelse ($rows as $row)
                @php($rowProduct = $row['product'])
                @php($rowKey = 'products.'.$rowProduct->id)
                <section class="card seo-row" aria-labelledby="seo-row-{{ $rowProduct->id }}">
                    <div class="seo-row-head">
                        <div>
                            <h2 id="seo-row-{{ $rowProduct->id }}"><a class="row-link" href="{{ route('admin.products.edit', $rowProduct) }}">{{ $rowProduct->name }}</a></h2>
                            <span class="muted" style="font-size:12px">/products/{{ $rowProduct->slug }} · {{ $rowProduct->category?->name }}@unless ($rowProduct->is_active) · <span class="badge badge-off">Hidden</span>@endunless</span>
                        </div>
                        <span class="score score-{{ \App\Support\ProductSeo::grade($row['score']) }}" title="SEO score">{{ $row['score'] }}%</span>
                    </div>
                    <div class="seo-row-grid">
                        <div class="field">
                            <label for="kw-{{ $rowProduct->id }}">Focus keyword</label>
                            <input id="kw-{{ $rowProduct->id }}" type="text" name="products[{{ $rowProduct->id }}][focus_keyword]" value="{{ old($rowKey.'.focus_keyword', $rowProduct->focus_keyword) }}" maxlength="100" placeholder="Main search phrase">
                        </div>
                        <div class="field @error($rowKey.'.meta_title') has-error @enderror">
                            <label for="mt-{{ $rowProduct->id }}">Meta title <span class="counter" data-counter-for="mt-{{ $rowProduct->id }}" aria-hidden="true"></span></label>
                            <input id="mt-{{ $rowProduct->id }}" type="text" name="products[{{ $rowProduct->id }}][meta_title]" value="{{ old($rowKey.'.meta_title', $rowProduct->meta_title) }}" data-count="60" placeholder="{{ \App\Support\ProductSeo::defaultTitle($rowProduct) }}">
                            @error($rowKey.'.meta_title')<span class="error">{{ $message }}</span>@enderror
                        </div>
                        <div class="field seo-row-wide @error($rowKey.'.meta_description') has-error @enderror">
                            <label for="md-{{ $rowProduct->id }}">Meta description <span class="counter" data-counter-for="md-{{ $rowProduct->id }}" aria-hidden="true"></span></label>
                            <textarea id="md-{{ $rowProduct->id }}" name="products[{{ $rowProduct->id }}][meta_description]" rows="2" data-count="160" placeholder="{{ \App\Support\ProductSeo::defaultDescription($rowProduct) }}">{{ old($rowKey.'.meta_description', $rowProduct->meta_description) }}</textarea>
                            @error($rowKey.'.meta_description')<span class="error">{{ $message }}</span>@enderror
                        </div>
                        @if ($rowProduct->image_path)
                            <div class="field seo-row-wide">
                                <label for="alt-{{ $rowProduct->id }}">Photo alt text</label>
                                <input id="alt-{{ $rowProduct->id }}" type="text" name="products[{{ $rowProduct->id }}][image_alt]" value="{{ old($rowKey.'.image_alt', $rowProduct->image_alt) }}" maxlength="160">
                            </div>
                        @endif
                    </div>
                    @if ($row['failing']->isNotEmpty())
                        <details class="seo-row-issues">
                            <summary>{{ $row['failing']->count() }} {{ \Illuminate\Support\Str::plural('thing', $row['failing']->count()) }} to improve</summary>
                            <ul>@foreach ($row['failing'] as $issue)<li>{{ $issue }}</li>@endforeach</ul>
                            <a class="btn btn-sm" href="{{ route('admin.products.edit', $rowProduct) }}#seo-checklist">Open full editor</a>
                        </details>
                    @else
                        <p class="seo-row-issues muted">✓ All checks pass.</p>
                    @endif
                </section>
            @empty
                <div class="card empty">No products match these filters.</div>
            @endforelse
        </div>
        @if ($rows->isNotEmpty())
            <div class="form-actions" style="margin-top:18px">
                <button class="btn btn-primary" type="submit">Save all</button>
                <span class="muted">Empty meta fields are generated automatically when you save.</span>
            </div>
        @endif
    </form>
@endsection
