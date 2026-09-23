<?php

declare(strict_types=1);

use Domain\Users\Models\User;

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

    $response = $this->get('/')->assertOk()->assertHeader('X-Cache-Status', 'MISS');

    expect($response->inertiaProps('auth'))->toBeNull();
});
