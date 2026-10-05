@if (count(seo()->breadcrumbs) > 1)
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        @foreach (seo()->breadcrumbs as $label => $url)
            @if (! $loop->first)<span aria-hidden="true">/</span>@endif
            @if ($url && ! $loop->last)
                <a href="{{ $url }}">{{ $label }}</a>
            @else
                <span @if ($loop->last) aria-current="page" @endif>{{ $label }}</span>
            @endif
        @endforeach
    </nav>
@endif
