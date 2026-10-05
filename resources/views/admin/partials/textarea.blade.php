{{-- Params: name, label, value, hint, required, count, rows, tall, id. --}}
@php
    $dot = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id ??= str_replace('.', '_', $dot);
@endphp
<div class="field @error($dot) has-error @enderror">
    <label for="{{ $id }}">
        {{ $label }}@if (! empty($required)) <span aria-hidden="true">*</span>@endif
        @isset($count)<span class="counter" data-counter-for="{{ $id }}" aria-hidden="true"></span>@endisset
    </label>
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows ?? 4 }}" class="{{ ! empty($tall) ? 'tall' : '' }}"
        @if (! empty($required)) required @endif
        @isset($count) data-count="{{ $count }}" @endisset
        @error($dot) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror>{{ old($dot, $value ?? null) }}</textarea>
    @isset($hint)<span class="hint">{{ $hint }}</span>@endisset
    @error($dot)<span class="error" id="{{ $id }}-error">{{ $message }}</span>@enderror
</div>
