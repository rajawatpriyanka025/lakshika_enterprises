@extends('admin.layouts.app')

@section('title', $page->name.' page')
@section('subtitle', 'Leave a field empty to use the default text. In headings, wrap words in *asterisks* for italics; press Enter for a line break.')

@php
    $publicUrl = match ($page->key) {
        'home' => route('home'),
        'shop' => route('shop'),
        'guides' => route('guides.index'),
        'faqs' => route('faqs'),
        'about' => route('about'),
        'contact' => route('contact'),
        default => url('/'),
    };
@endphp

@section('actions')
    <a class="btn" href="{{ $publicUrl }}" target="_blank" rel="noopener">View page ↗</a>
    <a class="btn" href="{{ route('admin.pages.index') }}">← All pages</a>
@endsection

@section('content')
    <form method="post" action="{{ route('admin.pages.update', $page->key) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-layout">
            <div class="stack">
                <div class="card">
                    <h2>Page content</h2>
                    @foreach ($page->definition() as $fieldName => $fieldDefinition)
                        @php($fieldType = $fieldDefinition['type'] ?? 'text')
                        @if (in_array($fieldType, ['textarea', 'heading', 'longtext'], true))
                            @include('admin.partials.textarea', [
                                'name' => "content[{$fieldName}]",
                                'label' => $fieldDefinition['label'],
                                'value' => $page->content[$fieldName] ?? '',
                                'rows' => $fieldType === 'longtext' ? 7 : ($fieldType === 'heading' ? 2 : 3),
                                'hint' => 'Default: '.\Illuminate\Support\Str::limit(str_replace("\n", ' ⏎ ', $fieldDefinition['default'] ?? ''), 140),
                            ])
                        @else
                            @include('admin.partials.field', [
                                'name' => "content[{$fieldName}]",
                                'label' => $fieldDefinition['label'],
                                'value' => $page->content[$fieldName] ?? '',
                                'placeholder' => $fieldDefinition['default'] ?? '',
                            ])
                        @endif
                    @endforeach
                </div>
                @include('admin.partials.seo-panel', ['model' => $page, 'urlBase' => $publicUrl, 'titleFrom' => 'default_meta_title', 'descFrom' => 'default_meta_description'])
                <input type="hidden" id="default_meta_title" value="{{ $page->defaultMeta('meta_title') }}">
                <input type="hidden" id="default_meta_description" value="{{ $page->defaultMeta('meta_description') }}">
            </div>
            <aside>
                <div class="card">
                    <h2>Save</h2>
                    <p class="muted mt-0">Changes go live immediately.</p>
                    <button class="btn btn-primary btn-block" type="submit">Save page</button>
                </div>
                <div class="card">
                    <h2>Default SEO</h2>
                    <p class="muted mt-0" style="font-size:13px"><strong>Title:</strong> {{ $page->defaultMeta('meta_title') }}</p>
                    <p class="muted" style="font-size:13px;margin-bottom:0"><strong>Description:</strong> {{ $page->defaultMeta('meta_description') }}</p>
                </div>
            </aside>
        </div>
    </form>
@endsection
