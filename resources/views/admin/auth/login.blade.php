<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in · {{ setting('site_name') }} admin</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<main class="auth-page">
    <div class="auth-card">
        <img class="logo" src="{{ asset('images/brand/logo.png') }}" alt="{{ setting('site_name') }}">
        <h1>Admin sign in</h1>
        <p>Manage leads, products and website content.</p>
        <form method="post" action="{{ route('admin.login') }}">
            @csrf
            @include('admin.partials.field', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'attrs' => 'autocomplete="username" autofocus'])
            <div class="field @error('password') has-error @enderror">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
            </div>
            @include('admin.partials.checkbox', ['name' => 'remember', 'label' => 'Keep me signed in on this device'])
            <button class="btn btn-primary btn-block" type="submit">Sign in</button>
        </form>
    </div>
</main>
</body>
</html>
