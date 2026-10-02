<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\ProjectFactory;
use Illuminate\Support\Facades\Http;

test('the synced monthly install count replaces the cached homepage', function () {
    Http::preventStrayRequests();
    Http::fake([
        'packagist.org/packages/foxws/laravel-podman/stats.json' => Http::response(['downloads' => ['total' => 2839, 'monthly' => 1689, 'daily' => 52]]),
    ]);

    ProjectFactory::new()->create(['slug' => 'laravel-podman', 'github_repository' => 'foxws/laravel-podman']);

    expect($this->get('/')->inertiaProps('packages.0.downloads'))->toBeNull();

    $this->artisan('packagist:sync')->assertSuccessful();

    expect($this->get('/')->inertiaProps('packages.0.downloads'))->toBe(1689);
});

test('a failed fetch keeps the last synced count', function () {
    Http::preventStrayRequests();
    Http::fakeSequence('packagist.org/packages/foxws/laravel-podman/stats.json')
        ->push(['downloads' => ['monthly' => 1689]])
        ->push(status: 503);

    ProjectFactory::new()->create(['slug' => 'laravel-podman', 'github_repository' => 'foxws/laravel-podman']);

    $this->artisan('packagist:sync')->assertSuccessful();
    $this->artisan('packagist:sync')->assertSuccessful();

    expect($this->get('/')->inertiaProps('packages.0.downloads'))->toBe(1689);
});

test('a package never synced, or not on GitHub, has no install count', function () {
    Http::preventStrayRequests();

    ProjectFactory::new()->create(['slug' => 'laravel-podman', 'github_repository' => 'foxws/laravel-podman']);
    ProjectFactory::new()->local()->create(['slug' => 'laravel-local']);

    expect(array_column($this->get('/')->inertiaProps('packages'), 'downloads'))->toBe([null, null]);
});
