@extends('admin.layouts.app')

@section('title', 'Pages')
@section('subtitle', 'Edit the headings, text and SEO of the main website pages.')

@section('content')
    <div class="table-wrap">
        <table>
            <thead><tr><th>Page</th><th>Meta title</th><th>Last edited</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @foreach ($pages as $pageKey => $page)
                    <tr>
                        <td><a class="row-link" href="{{ route('admin.pages.edit', $pageKey) }}">{{ $page->name }}</a>@if ($page->noindex)<span class="sub"><span class="badge badge-warn">noindex</span></span>@endif</td>
                        <td>{{ $page->meta_title ?: $page->defaultMeta('meta_title') }}<span class="sub">{{ $page->meta_title ? 'Custom' : 'Default' }}</span></td>
                        <td>{{ $page->updated_at?->diffForHumans() ?? 'Never' }}</td>
                        <td class="table-actions"><a class="btn btn-sm" href="{{ route('admin.pages.edit', $pageKey) }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
