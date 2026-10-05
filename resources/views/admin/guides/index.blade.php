@extends('admin.layouts.app')

@section('title', 'Guides')
@section('subtitle', 'Buying guides and articles. Helpful content like this is what brings in search traffic.')

@section('actions')
    <a class="btn btn-primary" href="{{ route('admin.guides.create') }}">+ Write a guide</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead><tr><th>Guide</th><th>Status</th><th>SEO</th><th>Published</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($guides as $guide)
                    <tr>
                        <td><a class="row-link" href="{{ route('admin.guides.edit', $guide) }}">{{ $guide->title }}</a><span class="sub">/guides/{{ $guide->slug }} · {{ count($guide->sections ?? []) }} sections</span></td>
                        <td>
                            @if (! $guide->is_published)
                                <span class="badge badge-off">Draft</span>
                            @elseif ($guide->published_at?->isFuture())
                                <span class="badge badge-warn">Scheduled</span>
                            @else
                                <span class="badge badge-on">Published</span>
                            @endif
                        </td>
                        <td>@if ($guide->noindex)<span class="badge badge-warn">noindex</span>@elseif ($guide->meta_description)<span class="badge badge-on">✓ Good</span>@else<span class="badge badge-warn">No meta description</span>@endif</td>
                        <td>{{ $guide->published_at?->format('j M Y') ?? '—' }}</td>
                        <td class="table-actions">
                            <a class="btn btn-sm" href="{{ route('guides.show', $guide->slug) }}" target="_blank" rel="noopener">View ↗</a>
                            <a class="btn btn-sm" href="{{ route('admin.guides.edit', $guide) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No guides yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
