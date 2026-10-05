{{-- Params: name, label, path (current, relative to the public disk), remove (checkbox name), hint. --}}
@php($id = str_replace(['[', ']'], ['_', ''], $name))
<div class="field image-field @error($name) has-error @enderror">
    <span class="field-label">{{ $label }}</span>
    <img id="{{ $id }}-preview" src="{{ ! empty($path) ? asset('storage/'.$path) : '' }}" alt="" @if (empty($path)) hidden @endif>
    <label class="sr-only" for="{{ $id }}">Upload {{ strtolower($label) }}</label>
    <input id="{{ $id }}" type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp" data-preview="{{ $id }}-preview">
    <span class="hint">{{ $hint ?? 'JPG, PNG or WebP, up to 4 MB.' }}</span>
    @if (! empty($path) && ! empty($remove))
        <label class="check" style="margin-top:8px"><input type="checkbox" name="{{ $remove }}" value="1"> <span>Remove this image</span></label>
    @endif
    @error($name)<span class="error">{{ $message }}</span>@enderror
</div>
