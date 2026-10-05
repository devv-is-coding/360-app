<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('password.confirm'));

    $response->assertOk();
});
