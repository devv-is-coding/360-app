<?php

use App\Enums\Role;
use App\Models\User;
use Laravel\Fortify\Features;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = post(route('register.store'), [
        'username' => 'superadmin',
        'email' => 'test@example.com',
        'first_name' => 'John',
        'middle_name' => 'Quincy',
        'last_name' => 'Doe',
        'contact_number' => '09171234567',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    assertAuthenticated();

    $user = User::firstWhere('email', 'test@example.com');

    expect($user->username)->toBe('superadmin')
        ->and($user->full_name)->toBe('John Quincy Doe')
        ->and($user->contact_number)->toBe('09171234567');
});

test('registering creates a super admin', function () {
    post(route('register.store'), [
        'username' => 'superadmin',
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasNoErrors();

    $user = User::firstWhere('email', 'test@example.com');

    expect($user->role)->toBe(Role::SuperAdmin)
        ->and($user->isSuperAdmin())->toBeTrue()
        ->and($user->is_active)->toBeTrue();
});

test('optional profile fields may be omitted', function () {
    post(route('register.store'), [
        'username' => 'superadmin',
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasNoErrors();

    $user = User::firstWhere('email', 'test@example.com');

    expect($user->middle_name)->toBeNull()
        ->and($user->contact_number)->toBeNull()
        ->and($user->full_name)->toBe('John Doe');
});

test('username must be unique', function () {
    User::factory()->create(['username' => 'superadmin']);

    post(route('register.store'), [
        'username' => 'superadmin',
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('username');

    assertGuest();
});

test('username rejects characters outside letters, numbers, dashes and underscores', function () {
    post(route('register.store'), [
        'username' => 'super admin!',
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('username');

    assertGuest();
});
