{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach ($staticPages as $page)
        <url><loc>{{ $page['url'] }}</loc>@if ($page['lastmod'])<lastmod>{{ $page['lastmod']->toAtomString() }}</lastmod>@endif<changefreq>{{ $page['changefreq'] }}</changefreq><priority>{{ $page['priority'] }}</priority></url>
    @endforeach
    @foreach ($categories as $category)
        <url><loc>{{ route('collections.show', $category->slug) }}</loc><lastmod>{{ $category->updated_at->toAtomString() }}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority>@if ($category->image_path)<image:image><image:loc>{{ asset('storage/'.$category->image_path) }}</image:loc></image:image>@endif</url>
    @endforeach
    @foreach ($products as $product)
        <url><loc>{{ route('products.show', $product->slug) }}</loc><lastmod>{{ $product->updated_at->toAtomString() }}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority>@if ($product->image_path)<image:image><image:loc>{{ asset('storage/'.$product->image_path) }}</image:loc></image:image>@endif</url>
    @endforeach
    @foreach ($guides as $guide)
        <url><loc>{{ route('guides.show', $guide->slug) }}</loc><lastmod>{{ $guide->updated_at->toAtomString() }}</lastmod><changefreq>monthly</changefreq><priority>0.7</priority>@if ($guide->image_path)<image:image><image:loc>{{ asset('storage/'.$guide->image_path) }}</image:loc></image:image>@endif</url>
    @endforeach
</urlset>
