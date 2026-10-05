{{-- Params: action (url), confirm (message), label. --}}
<form class="inline-form" method="post" action="{{ $action }}" data-confirm="{{ $confirm ?? 'Delete this item? This cannot be undone.' }}">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger {{ $small ?? true ? 'btn-sm' : '' }}" type="submit">{{ $label ?? 'Delete' }}</button>
</form>
