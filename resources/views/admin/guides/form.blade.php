@extends('admin.layouts.app')

@section('title', $guide->exists ? $guide->title : 'Write a guide')

@section('actions')
    @if ($guide->exists)<a class="btn" href="{{ route('guides.show', $guide->slug) }}" target="_blank" rel="noopener">View on site ↗</a>@endif
    <a class="btn" href="{{ route('admin.guides.index') }}">← All guides</a>
@endsection

@php($sections = old('sections', $guide->sections ?: [['heading' => '', 'body' => '']]))

@section('content')
    <form method="post" action="{{ $guide->exists ? route('admin.guides.update', $guide) : route('admin.guides.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($guide->exists) @method('PUT') @endif
        <div class="form-layout">
            <div class="stack">
                <div class="card">
                    @include('admin.partials.field', ['name' => 'title', 'label' => 'Title', 'value' => $guide->title, 'required' => true, 'hint' => 'The H1. Match how people search, e.g. "How to Choose the Right Size for Your Living Room".'])
                    @include('admin.partials.field', ['name' => 'slug', 'label' => 'URL', 'value' => $guide->slug, 'prefix' => url('/guides').'/', 'attrs' => 'data-slug-from="title" pattern="[a-z0-9\-]*"',
                        'hint' => $guide->exists ? 'Changing this adds a 301 redirect from the old URL automatically.' : 'Filled from the title.'])
                    @include('admin.partials.textarea', ['name' => 'intro', 'label' => 'Introduction', 'value' => $guide->intro, 'required' => true, 'rows' => 3, 'hint' => 'One or two sentences that answer the question straight away.'])
                </div>

                <div class="card" data-repeater>
                    <div class="card-header">
                        <h2>Sections</h2>
                        <button class="btn btn-sm" type="button" data-repeater-add>+ Add section</button>
                    </div>
                    <p class="muted mt-0">Each heading becomes an H2. Leave a blank line between paragraphs; wrap words in *asterisks* for italics.</p>
                    <div data-repeater-list>
                        @foreach ($sections as $index => $section)
                            <div class="repeater-item">
                                <div class="repeater-index">Section {{ $loop->iteration }}</div>
                                <button class="btn btn-sm btn-danger repeater-remove" type="button" data-repeater-remove aria-label="Remove section">Remove</button>
                                <div class="field">
                                    <label for="sections_{{ $index }}_heading">Heading</label>
                                    <input id="sections_{{ $index }}_heading" type="text" name="sections[{{ $index }}][heading]" data-name="sections[__INDEX__][heading]" value="{{ $section['heading'] ?? '' }}" maxlength="160">
                                </div>
                                <div class="field">
                                    <label for="sections_{{ $index }}_body">Text</label>
                                    <textarea id="sections_{{ $index }}_body" name="sections[{{ $index }}][body]" data-name="sections[__INDEX__][body]" rows="6">{{ $section['body'] ?? '' }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <template>
                        <div class="repeater-item">
                            <div class="repeater-index">Section</div>
                            <button class="btn btn-sm btn-danger repeater-remove" type="button" data-repeater-remove aria-label="Remove section">Remove</button>
                            <div class="field"><label>Heading <input type="text" data-name="sections[__INDEX__][heading]" maxlength="160"></label></div>
                            <div class="field"><label>Text <textarea data-name="sections[__INDEX__][body]" rows="6"></textarea></label></div>
                        </div>
                    </template>
                </div>

                @include('admin.partials.seo-panel', ['model' => $guide, 'urlBase' => url('/guides').'/', 'titleFrom' => 'title', 'descFrom' => 'intro'])
            </div>
            <aside>
                <div class="card">
                    <h2>Publish</h2>
                    @include('admin.partials.checkbox', ['name' => 'is_published', 'label' => 'Published', 'checked' => $guide->is_published, 'hint' => 'Untick to keep as a draft.'])
                    @include('admin.partials.field', ['name' => 'published_at', 'label' => 'Publish date', 'type' => 'datetime-local', 'value' => $guide->published_at, 'hint' => 'A future date schedules the guide.'])
                    @include('admin.partials.field', ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'value' => $guide->sort_order, 'attrs' => 'min="0"'])
                    <button class="btn btn-primary btn-block" type="submit">{{ $guide->exists ? 'Save changes' : 'Create guide' }}</button>
                </div>
                <div class="card">
                    @include('admin.partials.image', ['name' => 'image', 'label' => 'Cover image', 'path' => $guide->image_path, 'remove' => 'remove_image', 'hint' => 'Optional. Landscape, at least 1200 px wide.'])
                    @include('admin.partials.field', ['name' => 'image_alt', 'label' => 'Alt text', 'value' => $guide->image_alt])
                </div>
                @if ($guide->exists)
                    <div class="card">
                        @include('admin.partials.delete', ['action' => route('admin.guides.destroy', $guide), 'confirm' => 'Delete this guide?', 'label' => 'Delete guide'])
                    </div>
                @endif
            </aside>
        </div>
    </form>
@endsection
