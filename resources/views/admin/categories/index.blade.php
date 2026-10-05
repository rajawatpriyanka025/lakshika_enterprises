@extends('admin.layouts.app')

@section('title', 'Collections')
@section('subtitle', 'Product categories. Each has its own page, e.g. /collections/kitchen-wall-clocks.')

@section('actions')
    <a class="btn btn-primary" href="{{ route('admin.categories.create') }}">+ Add collection</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead><tr><th>Collection</th><th>Status</th><th>SEO</th><th class="num">Products</th><th class="num">Order</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td><a class="row-link" href="{{ route('admin.categories.edit', $category) }}">{{ $category->name }}</a><span class="sub">/collections/{{ $category->slug }}</span></td>
                        <td><span class="badge {{ $category->is_active ? 'badge-on' : 'badge-off' }}">{{ $category->is_active ? 'Live' : 'Hidden' }}</span></td>
                        <td>@if ($category->noindex)<span class="badge badge-warn">noindex</span>@elseif ($category->meta_description)<span class="badge badge-on">✓ Good</span>@else<span class="badge badge-warn">No meta description</span>@endif</td>
                        <td class="num">{{ $category->products_count }}</td>
                        <td class="num">{{ $category->sort_order }}</td>
                        <td class="table-actions">
                            <a class="btn btn-sm" href="{{ route('collections.show', $category->slug) }}" target="_blank" rel="noopener">View ↗</a>
                            <a class="btn btn-sm" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No collections yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
