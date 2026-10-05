{{-- Params: name, label, options (value => label), value, placeholder, hint, required, id. --}}
@php
    $dot = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id ??= str_replace('.', '_', $dot);
    $current = (string) old($dot, $value ?? '');
@endphp
<div class="field @error($dot) has-error @enderror">
    <label for="{{ $id }}">{{ $label }}@if (! empty($required)) <span aria-hidden="true">*</span>@endif</label>
    <select id="{{ $id }}" name="{{ $name }}" @if (! empty($required)) required @endif @error($dot) aria-invalid="true" @enderror>
        @isset($placeholder)<option value="">{{ $placeholder }}</option>@endisset
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @isset($hint)<span class="hint">{{ $hint }}</span>@endisset
    @error($dot)<span class="error">{{ $message }}</span>@enderror
</div>
