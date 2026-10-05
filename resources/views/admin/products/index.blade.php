@extends('admin.layouts.app')

@section('title', 'Products')

@section('actions')
    <a class="btn" href="{{ route('admin.product-seo.index') }}">Edit SEO for all products</a>
    <a class="btn btn-primary" href="{{ route('admin.products.create') }}">+ Add product</a>
@endsection

@section('content')
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
        <button class="btn" type="submit">Filter</button>
        @if (array_filter($filters))<a class="btn" href="{{ route('admin.products.index') }}">Clear</a>@endif
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th></th><th>Product</th><th>Collection</th><th>Status</th><th>SEO</th><th class="num">Leads</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td style="width:60px">
                            @if ($product->image_path)
                                <img class="thumb" src="{{ asset('storage/'.$product->image_path) }}" alt="">
                            @else
                                <span class="thumb thumb-empty" aria-hidden="true">◷</span>
                            @endif
                        </td>
                        <td><a class="row-link" href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a><span class="sub">/products/{{ $product->slug }}</span></td>
                        <td>{{ $product->category?->name }}</td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'badge-on' : 'badge-off' }}">{{ $product->is_active ? 'Live' : 'Hidden' }}</span>
                            @if ($product->featured)<span class="badge">Featured</span>@endif
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}#seo-checklist" class="score score-{{ \App\Support\ProductSeo::grade($product->seo_score) }}" title="SEO score — open checklist">{{ $product->seo_score }}%</a>
                            @if ($product->noindex)<span class="badge badge-warn">noindex</span>@endif
                        </td>
                        <td class="num">{{ $product->leads_count }}</td>
                        <td class="table-actions">
                            <a class="btn btn-sm" href="{{ route('products.show', $product->slug) }}" target="_blank" rel="noopener">View ↗</a>
                            <a class="btn btn-sm" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('admin.partials.pagination', ['paginator' => $products])
@endsection
