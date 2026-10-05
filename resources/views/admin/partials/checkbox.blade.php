{{-- Params: name, label, checked, hint. Unchecked boxes send nothing; controllers read them with $request->boolean(). --}}
@php($isChecked = old('_token') ? (bool) old($name) : (bool) ($checked ?? false))
<div class="field">
    <label class="check">
        <input type="checkbox" name="{{ $name }}" value="1" @checked($isChecked)>
        <span>{{ $label }}@isset($hint)<small>{{ $hint }}</small>@endisset</span>
    </label>
</div>
