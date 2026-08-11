<?php

use App\Enums\Role;
use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
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

    $this->assertAuthenticated();

    $user = User::firstWhere('email', 'test@example.com');

    expect($user->username)->toBe('superadmin')
        ->and($user->full_name)->toBe('John Quincy Doe')
        ->and($user->contact_number)->toBe('09171234567');
});

test('registering creates a super admin', function () {
    $this->post(route('register.store'), [
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
    $this->post(route('register.store'), [
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

    $this->post(route('register.store'), [
        'username' => 'superadmin',
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('username rejects characters outside letters, numbers, dashes and underscores', function () {
    $this->post(route('register.store'), [
        'username' => 'super admin!',
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});
