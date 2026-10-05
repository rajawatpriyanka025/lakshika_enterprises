@extends('admin.layouts.app')

@section('title', 'FAQs')
@section('subtitle', 'Shown on the FAQs page with FAQPage structured data.')

@section('actions')
    <a class="btn btn-primary" href="{{ route('admin.faqs.create') }}">+ Add FAQ</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead><tr><th class="num">#</th><th>Question</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($faqs as $faq)
                    <tr>
                        <td class="num">{{ $faq->sort_order }}</td>
                        <td><a class="row-link" href="{{ route('admin.faqs.edit', $faq) }}">{{ $faq->question }}</a><span class="sub">{{ \Illuminate\Support\Str::limit($faq->answer, 110) }}</span></td>
                        <td><span class="badge {{ $faq->is_active ? 'badge-on' : 'badge-off' }}">{{ $faq->is_active ? 'Live' : 'Hidden' }}</span></td>
                        <td class="table-actions">
                            <a class="btn btn-sm" href="{{ route('admin.faqs.edit', $faq) }}">Edit</a>
                            @include('admin.partials.delete', ['action' => route('admin.faqs.destroy', $faq), 'confirm' => 'Delete this FAQ?'])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No FAQs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
