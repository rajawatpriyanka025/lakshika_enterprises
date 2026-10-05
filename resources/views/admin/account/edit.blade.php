@extends('admin.layouts.app')

@section('title', 'My account')

@section('content')
    <form class="card" method="post" action="{{ route('admin.account.update') }}" style="max-width:640px">
        @csrf
        @method('PUT')
        @include('admin.partials.field', ['name' => 'name', 'label' => 'Name', 'value' => $user->name, 'required' => true])
        @include('admin.partials.field', ['name' => 'email', 'label' => 'Email (used to sign in)', 'type' => 'email', 'value' => $user->email, 'required' => true])
        <h2 style="margin-top:22px">Change password</h2>
        <div class="grid-2">
            <div class="field @error('password') has-error @enderror">
                <label for="password">New password</label>
                <input id="password" type="password" name="password" autocomplete="new-password">
                <span class="hint">Leave empty to keep your current password. At least 10 characters with letters and numbers.</span>
                @error('password')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
            </div>
        </div>
        <div class="field @error('current_password') has-error @enderror">
            <label for="current_password">Current password *</label>
            <input id="current_password" type="password" name="current_password" required autocomplete="current-password">
            <span class="hint">Required to save any change.</span>
            @error('current_password')<span class="error">{{ $message }}</span>@enderror
        </div>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Save account</button></div>
    </form>
@endsection
