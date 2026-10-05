@extends('admin.layouts.app')

@section('title', 'Settings')
@section('subtitle', $groups[$group]['label'])

@section('content')
    <nav class="tabs" aria-label="Settings sections">
        @foreach ($groups as $groupKey => $groupDefinition)
            <a class="{{ $groupKey === $group ? 'is-active' : '' }}" href="{{ route('admin.settings.edit', $groupKey) }}" @if ($groupKey === $group) aria-current="page" @endif>{{ $groupDefinition['label'] }}</a>
        @endforeach
    </nav>

    <form class="card" method="post" action="{{ route('admin.settings.update', $group) }}" enctype="multipart/form-data" style="max-width:820px">
        @csrf
        @method('PUT')
        @foreach ($groups[$group]['fields'] as $settingKey => $settingField)
            @php($settingType = $settingField['type'] ?? 'text')
            @php($settingValue = $values[$settingKey] ?? null)
            @switch($settingType)
                @case('textarea')
                    @include('admin.partials.textarea', ['name' => $settingKey, 'label' => $settingField['label'], 'value' => $settingValue, 'hint' => $settingField['help'] ?? null, 'rows' => 3])
                    @break
                @case('checkbox')
                    @include('admin.partials.checkbox', ['name' => $settingKey, 'label' => $settingField['label'], 'checked' => (bool) $settingValue, 'hint' => $settingField['help'] ?? null])
                    @break
                @case('image')
                    @include('admin.partials.image', ['name' => $settingKey, 'label' => $settingField['label'], 'path' => $settingValue, 'remove' => 'remove_'.$settingKey, 'hint' => $settingField['help'] ?? null])
                    @break
                @default
                    @include('admin.partials.field', ['name' => $settingKey, 'label' => $settingField['label'], 'type' => $settingType, 'value' => $settingValue, 'hint' => $settingField['help'] ?? null,
                        'placeholder' => config("settings.defaults.{$settingKey}")])
            @endswitch
        @endforeach
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save settings</button>
            @if ($group === 'maintenance')<a class="btn" href="{{ route('maintenance') }}" target="_blank" rel="noopener">Preview maintenance page ↗</a>@endif
        </div>
    </form>

    @if ($group === 'seo')
        <div class="card" style="max-width:820px">
            <h2>Search Console checklist</h2>
            <ol style="margin:0;padding-left:18px;line-height:1.9">
                <li>Add your site in <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console</a> using the "HTML tag" method and paste the code above.</li>
                <li>Submit your sitemap: <code>{{ route('sitemap') }}</code></li>
                <li>Do the same in <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a> (it also covers Yahoo and DuckDuckGo).</li>
                <li>Create a <a href="https://business.google.com" target="_blank" rel="noopener">Google Business Profile</a> with the logo from the brand kit, and link it to this website.</li>
                <li>Check robots.txt: <a href="{{ route('robots') }}" target="_blank" rel="noopener">{{ route('robots') }}</a></li>
            </ol>
        </div>
    @endif
@endsection
