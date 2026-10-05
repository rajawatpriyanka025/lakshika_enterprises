@extends('admin.layouts.app')

@section('title', 'Marketplaces')
@section('subtitle', 'Where customers can buy. Shown on product pages, the contact page and in the footer.')

@section('actions')
    <a class="btn btn-primary" href="{{ route('admin.marketplaces.create') }}">+ Add marketplace</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead><tr><th class="num">#</th><th>Marketplace</th><th>Brand store link</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($marketplaces as $marketplace)
                    <tr>
                        <td class="num">{{ $marketplace->sort_order }}</td>
                        <td><a class="row-link" href="{{ route('admin.marketplaces.edit', $marketplace) }}">{{ $marketplace->name }}</a><span class="sub">{{ $marketplace->search_url }}</span></td>
                        <td>@if ($marketplace->store_url)<a href="{{ $marketplace->store_url }}" target="_blank" rel="noopener">Store page ↗</a>@else<span class="muted">Brand search</span>@endif</td>
                        <td><span class="badge {{ $marketplace->is_active ? 'badge-on' : 'badge-off' }}">{{ $marketplace->is_active ? 'Shown' : 'Hidden' }}</span></td>
                        <td class="table-actions">
                            <a class="btn btn-sm" href="{{ route('admin.marketplaces.edit', $marketplace) }}">Edit</a>
                            @include('admin.partials.delete', ['action' => route('admin.marketplaces.destroy', $marketplace), 'confirm' => 'Remove '.$marketplace->name.'? Product links for it will be lost.'])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No marketplaces yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
