<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create {email?} {--name=} {--password=}', function (?string $email = null) {
    $email ??= text('Admin email', required: true);
    $name = $this->option('name') ?: text('Name', default: 'Admin', required: true);
    $password = $this->option('password') ?: password('Password (min 10 characters, letters and numbers)', required: true);

    $validator = Validator::make(compact('email', 'password'), [
        'email' => ['required', 'email'],
        'password' => ['required', Password::min(10)->letters()->numbers()],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user = User::query()->firstOrNew(['email' => strtolower($email)]);
    $user->forceFill(['name' => $name, 'password' => $password, 'is_admin' => true])->save();

    $this->info("Admin account ready for {$user->email}. Sign in at ".url('/admin/login'));

    return 0;
})->purpose('Create an admin user, or reset an existing user\'s password and make them an admin');
