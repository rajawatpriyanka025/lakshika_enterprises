{{--
    Text-like input. Params: name, label, value, type, hint, required, count (soft max length),
    id, prefix, placeholder, attrs (extra attribute string, trusted).
--}}
@php
    $type ??= 'text';
    $dot = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id ??= str_replace('.', '_', $dot);
    $current = old($dot, $value ?? null);
    if ($current instanceof \DateTimeInterface) {
        $current = $type === 'date' ? $current->format('Y-m-d') : $current->format('Y-m-d\TH:i');
    }
@endphp
<div class="field @error($dot) has-error @enderror">
    <label for="{{ $id }}">
        {{ $label }}@if (! empty($required)) <span aria-hidden="true">*</span>@endif
        @isset($count)<span class="counter" data-counter-for="{{ $id }}" aria-hidden="true"></span>@endisset
    </label>
    @isset($prefix)<div class="input-prefix"><span>{{ $prefix }}</span>@endisset
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $current }}"
        @if (! empty($required)) required @endif
        @isset($count) data-count="{{ $count }}" @endisset
        @isset($placeholder) placeholder="{{ $placeholder }}" @endisset
        @error($dot) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
        {!! $attrs ?? '' !!}>
    @isset($prefix)</div>@endisset
    @isset($hint)<span class="hint">{{ $hint }}</span>@endisset
    @error($dot)<span class="error" id="{{ $id }}-error">{{ $message }}</span>@enderror
</div>
