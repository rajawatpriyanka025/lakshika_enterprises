@extends('admin.layouts.app')

@section('title', $category->exists ? $category->name : 'Add collection')

@section('actions')
    @if ($category->exists)<a class="btn" href="{{ route('collections.show', $category->slug) }}" target="_blank" rel="noopener">View on site ↗</a>@endif
    <a class="btn" href="{{ route('admin.categories.index') }}">← All collections</a>
@endsection

@section('content')
    <form method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($category->exists) @method('PUT') @endif
        <div class="form-layout">
            <div class="stack">
                <div class="card">
                    @include('admin.partials.field', ['name' => 'name', 'label' => 'Collection name', 'value' => $category->name, 'required' => true, 'hint' => 'Use the phrase people search for, e.g. "Kitchen Wall Clocks" or "Table Lamps".'])
                    @include('admin.partials.field', ['name' => 'slug', 'label' => 'URL', 'value' => $category->slug, 'prefix' => url('/collections').'/', 'attrs' => 'data-slug-from="name" pattern="[a-z0-9\-]*"',
                        'hint' => $category->exists ? 'Changing this adds a 301 redirect from the old URL automatically.' : 'Filled from the name.'])
                    @include('admin.partials.textarea', ['name' => 'description', 'label' => 'Description', 'value' => $category->description, 'required' => true, 'rows' => 6,
                        'hint' => 'Shown at the top of the collection page. 2–3 helpful sentences with the main keyword work well.'])
                </div>
                @include('admin.partials.seo-panel', ['model' => $category, 'urlBase' => url('/collections').'/', 'titleFrom' => 'name', 'descFrom' => 'description'])
            </div>
            <aside>
                <div class="card">
                    <h2>Publish</h2>
                    @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Show on website', 'checked' => $category->is_active])
                    @include('admin.partials.field', ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'value' => $category->sort_order, 'attrs' => 'min="0"'])
                    <button class="btn btn-primary btn-block" type="submit">{{ $category->exists ? 'Save changes' : 'Create collection' }}</button>
                </div>
                <div class="card">
                    @include('admin.partials.image', ['name' => 'image', 'label' => 'Tile image', 'path' => $category->image_path, 'remove' => 'remove_image', 'hint' => 'Used on the home page collection tile. Landscape or square, under 4 MB.'])
                </div>
                @if ($category->exists)
                    <div class="card">
                        @include('admin.partials.delete', ['action' => route('admin.categories.destroy', $category), 'confirm' => 'Delete this collection?', 'label' => 'Delete collection'])
                    </div>
                @endif
            </aside>
        </div>
    </form>
@endsection
