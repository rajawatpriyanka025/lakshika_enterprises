<article class="product-card">
    <a class="product-art product-art-{{ $loop->index % 4 }}" href="{{ route('products.show', $product->slug) }}" aria-label="View {{ $product->name }}">
        @if ($product->image_path)
            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->image_alt ?: $product->name }}" loading="lazy" width="600" height="600">
        @else
            <span class="clock-illustration" aria-hidden="true"><i></i><b></b></span>
        @endif
        <span class="art-caption">A thoughtful detail</span>
    </a>
    <div class="product-card-copy">
        @if ($product->category)
            <a class="eyebrow" href="{{ route('collections.show', $product->category->slug) }}">{{ $product->category->name }}</a>
        @endif
        <h3><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h3>
        <p>{{ $product->excerpt }}</p>
        <a class="text-link" href="{{ route('products.show', $product->slug) }}">Discover this style <span aria-hidden="true">→</span></a>
    </div>
</article>
