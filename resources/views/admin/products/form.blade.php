@extends('admin.layouts.app')

@section('title', $product->exists ? $product->name : 'Add product')

@section('actions')
    @if ($product->exists)
        <a class="btn" href="{{ route('products.show', $product->slug) }}" target="_blank" rel="noopener">View on site ↗</a>
    @endif
    <a class="btn" href="{{ route('admin.products.index') }}">← All products</a>
@endsection

@section('content')
    <form method="post" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($product->exists) @method('PUT') @endif
        <div class="form-layout">
            <div class="stack">
                <div class="card">
                    <h2>Details</h2>
                    @include('admin.partials.field', ['name' => 'name', 'label' => 'Product name', 'value' => $product->name, 'required' => true, 'hint' => 'This is the H1 on the product page. Include the main keyword, e.g. "Wooden Kitchen Wall Clock" or "Brass Table Lamp".'])
                    @include('admin.partials.field', ['name' => 'slug', 'label' => 'URL', 'value' => $product->slug, 'prefix' => url('/products').'/', 'attrs' => 'data-slug-from="name" pattern="[a-z0-9\-]*"',
                        'hint' => $product->exists ? 'Changing this adds a 301 redirect from the old URL automatically.' : 'Filled from the name. Lowercase words separated by hyphens.'])
                    @include('admin.partials.textarea', ['name' => 'excerpt', 'label' => 'Short summary', 'value' => $product->excerpt, 'required' => true, 'rows' => 2, 'count' => 160, 'hint' => 'Shown on product cards and used as the meta description if you leave that empty.'])
                    @include('admin.partials.textarea', ['name' => 'description', 'label' => 'Description', 'value' => $product->description, 'required' => true, 'tall' => true,
                        'hint' => 'Aim for 150–300+ words: what it is, size, material, finish, where it works best, care tips. Leave a blank line between paragraphs.'])
                </div>

                <div class="card">
                    <h2>Specifications</h2>
                    <p class="muted mt-0">Shown as a table on the product page and sent to Google as product data. Fill what applies to this product; leave the rest empty.</p>
                    <div class="grid-2">
                        @include('admin.partials.field', ['name' => 'material', 'label' => 'Material', 'value' => $product->material, 'placeholder' => 'e.g. MDF wood, brass, cotton'])
                        @include('admin.partials.field', ['name' => 'colour', 'label' => 'Colour / finish', 'value' => $product->colour, 'placeholder' => 'e.g. Walnut brown'])
                        @include('admin.partials.field', ['name' => 'dimensions', 'label' => 'Size / dimensions', 'value' => $product->dimensions, 'placeholder' => 'e.g. 30 × 30 × 4 cm'])
                        @include('admin.partials.field', ['name' => 'weight', 'label' => 'Weight', 'value' => $product->weight, 'placeholder' => 'e.g. 650 g'])
                        @include('admin.partials.field', ['name' => 'sku', 'label' => 'SKU / model number', 'value' => $product->sku])
                        @include('admin.partials.select', ['name' => 'availability', 'label' => 'Availability', 'options' => \App\Models\Product::AVAILABILITY, 'value' => $product->availability, 'placeholder' => 'Not shown'])
                        @include('admin.partials.field', ['name' => 'price', 'label' => 'Price (₹)', 'type' => 'number', 'value' => $product->price, 'attrs' => 'min="0" step="0.01"',
                            'hint' => 'Optional. When set, the price is shown on the page and Google can display it in search results. Keep it in line with your marketplace price.'])
                    </div>
                </div>

                <div class="card">
                    <h2>Marketplace listings</h2>
                    <p class="muted mt-0">Paste the exact product page on each marketplace. Empty ones fall back to a brand search on that marketplace.</p>
                    @forelse ($marketplaces as $marketplace)
                        @include('admin.partials.field', ['name' => "marketplace_links[{$marketplace->id}]", 'label' => $marketplace->name.' product URL', 'type' => 'url',
                            'value' => $product->marketplace_links[$marketplace->id] ?? null, 'placeholder' => 'https://'])
                    @empty
                        <p class="muted"><a href="{{ route('admin.marketplaces.create') }}">Add a marketplace</a> first.</p>
                    @endforelse
                </div>

                @include('admin.partials.seo-panel', ['model' => $product, 'urlBase' => url('/products').'/', 'titleFrom' => 'name', 'descFrom' => 'excerpt', 'focusKeyword' => true, 'autofill' => true])
            </div>

            <aside>
                <div class="card">
                    <h2>Publish</h2>
                    @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Show on website', 'checked' => $product->is_active])
                    @include('admin.partials.checkbox', ['name' => 'featured', 'label' => 'Feature on home page', 'checked' => $product->featured])
                    @include('admin.partials.select', ['name' => 'category_id', 'label' => 'Collection', 'options' => $categories, 'value' => $product->category_id, 'required' => true, 'placeholder' => 'Choose…'])
                    @include('admin.partials.field', ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'value' => $product->sort_order, 'hint' => 'Lower numbers show first.', 'attrs' => 'min="0"'])
                    <button class="btn btn-primary btn-block" type="submit">{{ $product->exists ? 'Save changes' : 'Create product' }}</button>
                </div>
                @if ($product->exists)
                    <div class="card" id="seo-checklist">
                        <div class="card-header">
                            <h2>SEO checklist</h2>
                            <span class="score score-{{ \App\Support\ProductSeo::grade($score) }}">{{ $score }}%</span>
                        </div>
                        <ul class="checklist">
                            @foreach ($audit as $check)
                                <li class="{{ $check['pass'] ? 'is-pass' : 'is-fail' }}">
                                    <span aria-hidden="true">{{ $check['pass'] ? '✓' : '✕' }}</span>
                                    <span>{{ $check['label'] }}<span class="sr-only">: {{ $check['pass'] ? 'done' : 'to do' }}</span>@unless ($check['pass'])<small>{{ $check['hint'] }}</small>@endunless</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="muted" style="font-size:12px;margin-bottom:0">Updates each time you save.</p>
                    </div>
                @endif
                <div class="card">
                    <h2>Photo</h2>
                    @include('admin.partials.image', ['name' => 'image', 'label' => 'Main image', 'path' => $product->image_path, 'remove' => 'remove_image', 'hint' => 'Square, at least 800 × 800 px, under 4 MB. WebP or JPG loads fastest.'])
                    @include('admin.partials.field', ['name' => 'image_alt', 'label' => 'Alt text', 'value' => $product->image_alt, 'hint' => 'Describe the photo for Google Images and screen readers, e.g. "Round walnut wall clock above a grey sofa".'])
                </div>
                @if ($product->exists)
                    <div class="card">
                        @include('admin.partials.delete', ['action' => route('admin.products.destroy', $product), 'confirm' => 'Delete '.$product->name.'? Its page will stop working.', 'label' => 'Delete product'])
                    </div>
                @endif
            </aside>
        </div>
    </form>
@endsection
