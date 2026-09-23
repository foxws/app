<?php

declare(strict_types=1);

use Domain\Users\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    config(['responsecache.debug.enabled' => true]);
});

it('serves a repeat guest visit to a marketing page from cache', function () {
    $this->get('/')->assertOk()->assertHeader('X-Cache-Status', 'MISS');
    $this->get('/')->assertOk()->assertHeader('X-Cache-Status', 'HIT');
});

it('caches a repeat authenticated visit under its own key', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')->assertOk()->assertHeader('X-Cache-Status', 'MISS');
    $this->actingAs($user)->get('/')->assertOk()->assertHeader('X-Cache-Status', 'HIT');
});

it('does not leak an authenticated visit into a later guest response', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')->assertOk();

    // actingAs() sets the user directly on the shared auth guard, and that
    // stays resolved for the rest of the test — unlike a real request,
    // where each visitor's guard resolves independently from its own
    // session. Log out so this second call is an actual guest request.
    Auth::logout();

    $response = $this->get('/')->assertOk()->assertHeader('X-Cache-Status', 'MISS');

    expect($response->inertiaProps('auth'))->toBeNull();
});
