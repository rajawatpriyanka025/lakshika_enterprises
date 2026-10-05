{{--
    Shared SEO card. Params: model, urlBase (public URL before the slug, or full URL when no slug field),
    titleFrom / descFrom (ids of fields used when meta fields are empty).
--}}
<div class="card">
    <h2>Search engine listing</h2>
    <div class="serp" data-serp data-suffix="{{ setting('site_name') }}" data-base="{{ $urlBase }}"
        data-title-fallback="{{ $titleFrom ?? '' }}" data-desc-fallback="{{ $descFrom ?? '' }}" aria-label="Google preview">
        <div class="serp-url">{{ $urlBase }}{{ $model->slug ?? '' }}</div>
        <div class="serp-title"></div>
        <div class="serp-desc"></div>
    </div>
    <div style="margin-top:16px">
        @if (! empty($focusKeyword))
            @include('admin.partials.field', ['name' => 'focus_keyword', 'label' => 'Focus keyword', 'value' => $model->focus_keyword, 'placeholder' => 'The main phrase people search for',
                'hint' => 'One phrase per page, e.g. "wooden wall clock" or "brass table lamp". The SEO checklist checks where it appears.'])
        @endif
        @include('admin.partials.field', ['name' => 'meta_title', 'label' => 'Meta title', 'value' => $model->meta_title, 'count' => 60,
            'hint' => (! empty($autofill) ? 'Generated from the name if left empty. ' : 'Leave empty to use the name. ').'"| '.setting('site_name').'" is added automatically. Aim for 50–60 characters.'])
        @include('admin.partials.textarea', ['name' => 'meta_description', 'label' => 'Meta description', 'value' => $model->meta_description, 'count' => 160, 'rows' => 3,
            'hint' => (! empty($autofill) ? 'Generated from the summary if left empty. ' : '').'The summary shown in Google. Aim for 120–160 characters and include the main keyword.'])
        <details>
            <summary class="muted" style="cursor:pointer;margin-bottom:12px">Advanced</summary>
            @include('admin.partials.field', ['name' => 'canonical_url', 'label' => 'Canonical URL', 'value' => $model->canonical_url, 'placeholder' => '/products/another-page',
                'hint' => 'Only set this if another page is the main version of this content. Leave empty in most cases.'])
            @include('admin.partials.image', ['name' => 'og_image_file', 'label' => 'Social share image', 'path' => $model->og_image, 'remove' => 'remove_og_image',
                'hint' => 'Shown when the link is shared on WhatsApp, Facebook etc. 1200 × 630 px. Defaults to the main image.'])
            @include('admin.partials.checkbox', ['name' => 'noindex', 'label' => 'Hide from search engines (noindex)', 'checked' => $model->noindex,
                'hint' => 'Also removes the page from the sitemap.'])
        </details>
    </div>
</div>
