@extends('admin.layouts.app')

@section('title', 'Redirects')
@section('subtitle', 'Send old or broken URLs to the right page. Renaming a product, collection or guide URL adds one here automatically.')

@section('actions')
    <a class="btn btn-primary" href="{{ route('admin.redirects.create') }}">+ Add redirect</a>
@endsection

@section('content')
    <form class="filters" method="get" role="search">
        <div class="field"><label class="sr-only" for="q">Search</label><input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Search URLs…"></div>
        <button class="btn" type="submit">Search</button>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>From</th><th>To</th><th>Type</th><th class="num">Hits</th><th>Last used</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($redirects as $redirect)
                    <tr>
                        <td><a class="row-link" href="{{ route('admin.redirects.edit', $redirect) }}">{{ $redirect->from_path }}</a>@unless ($redirect->is_active)<span class="sub">Disabled</span>@endunless</td>
                        <td>{{ $redirect->to_url }}</td>
                        <td><span class="badge">{{ $redirect->status_code === 301 ? '301 Permanent' : '302 Temporary' }}</span></td>
                        <td class="num">{{ number_format($redirect->hits) }}</td>
                        <td>{{ $redirect->last_hit_at?->diffForHumans() ?? '—' }}</td>
                        <td class="table-actions">
                            <a class="btn btn-sm" href="{{ route('admin.redirects.edit', $redirect) }}">Edit</a>
                            @include('admin.partials.delete', ['action' => route('admin.redirects.destroy', $redirect), 'confirm' => 'Delete this redirect?'])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No redirects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('admin.partials.pagination', ['paginator' => $redirects])
@endsection
