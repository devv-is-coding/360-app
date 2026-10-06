<?php

use App\Enums\Role;
use App\Models\Tenant;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

afterEach(function () {
    // End tenancy first so the tenant connection is purged; Windows cannot delete
    // an SQLite database file that is still open.
    tenancy()->end();

    // Drops the tenant databases created during the test.
    Tenant::all()->each->delete();
});

function tenantOnDomain(string $id): Tenant
{
    $tenant = Tenant::create(['id' => $id, 'name' => ucfirst($id).' Branch']);
    $tenant->domains()->create(['domain' => "{$id}.localhost"]);

    return $tenant;
}

test('the super admin login screen can be rendered', function () {
    get(route('admin.login'))
        ->assertOk()
        ->assertSee(route('admin.login.store'));
});

test('super admins can log in through the super admin login', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    post(route('admin.login.store'), ['email' => $superAdmin->email, 'password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    assertAuthenticatedAs($superAdmin);
});

test('the super admin login refuses other roles', function (Role $role) {
    $user = User::factory()->role($role)->create();

    post(route('admin.login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    assertGuest();
})->with([
    'admin' => Role::Admin,
    'manager' => Role::Manager,
    'staff' => Role::Staff,
    'customer' => Role::Customer,
]);

test('the customer login refuses other roles', function (Role $role) {
    $user = User::factory()->role($role)->create();

    post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    assertGuest();
})->with([
    'super admin' => Role::SuperAdmin,
    'admin' => Role::Admin,
    'manager' => Role::Manager,
    'staff' => Role::Staff,
]);

test('inactive users cannot log in', function () {
    $user = User::factory()->inactive()->create();

    post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    assertGuest();
});

test('the tenant login screen shows the tenant', function () {
    tenantOnDomain('acme');

    get('http://acme.localhost/login')
        ->assertOk()
        ->assertSee('Acme Branch');
});

test('tenant users can log in on their own tenant domain', function (Role $role) {
    $user = User::factory()->role($role)->for(tenantOnDomain('acme'))->create();

    post('http://acme.localhost/login', ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('http://acme.localhost/dashboard');

    assertAuthenticatedAs($user);
})->with([
    'admin' => Role::Admin,
    'manager' => Role::Manager,
    'staff' => Role::Staff,
]);

test('the tenant login refuses users of another tenant', function () {
    tenantOnDomain('acme');
    $otherTenantAdmin = User::factory()->role(Role::Admin)->for(tenantOnDomain('globex'))->create();

    post('http://acme.localhost/login', ['email' => $otherTenantAdmin->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    assertGuest();
});

test('the tenant login refuses platform-wide roles', function (Role $role) {
    tenantOnDomain('acme');
    $user = User::factory()->role($role)->create();

    post('http://acme.localhost/login', ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    assertGuest();
})->with([
    'super admin' => Role::SuperAdmin,
    'customer' => Role::Customer,
]);

test('tenant users can open their tenant dashboard', function () {
    $staff = User::factory()->role(Role::Staff)->for(tenantOnDomain('acme'))->create();

    actingAs($staff)
        ->get('http://acme.localhost/dashboard')
        ->assertOk()
        ->assertSee('Acme Branch');
});

test('the tenant dashboard is forbidden to users of another tenant', function () {
    tenantOnDomain('acme');
    $otherTenantAdmin = User::factory()->role(Role::Admin)->for(tenantOnDomain('globex'))->create();

    actingAs($otherTenantAdmin)
        ->get('http://acme.localhost/dashboard')
        ->assertForbidden();
});

test('the tenant dashboard redirects guests to the tenant login', function () {
    tenantOnDomain('acme');

    get('http://acme.localhost/dashboard')
        ->assertRedirect('http://acme.localhost/login');
});
