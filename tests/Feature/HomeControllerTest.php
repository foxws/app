<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;

test('orders packages by most recently synced version first', function () {
    $stale = ProjectFactory::new()->create(['slug' => 'stale-project', 'title' => 'Stale Project']);
    VersionFactory::new()->create([
        'project_id' => $stale->id,
        'is_default' => true,
        'last_synced_at' => now()->subDays(10),
    ]);

    $fresh = ProjectFactory::new()->create(['slug' => 'fresh-project', 'title' => 'Fresh Project']);
    VersionFactory::new()->create([
        'project_id' => $fresh->id,
        'is_default' => true,
        'last_synced_at' => now()->subHour(),
    ]);

    $response = $this->get('/');

    $response->assertOk();

    expect(array_column($response->inertiaProps('packages'), 'slug'))
        ->toBe(['fresh-project', 'stale-project']);
});

test('sorts a project with no synced version after any that have one', function () {
    $unsynced = ProjectFactory::new()->create(['slug' => 'unsynced-project', 'title' => 'Unsynced Project']);
    VersionFactory::new()->create(['project_id' => $unsynced->id, 'is_default' => true, 'last_synced_at' => null]);

    $synced = ProjectFactory::new()->create(['slug' => 'synced-project', 'title' => 'Synced Project']);
    VersionFactory::new()->create(['project_id' => $synced->id, 'is_default' => true, 'last_synced_at' => now()]);

    $response = $this->get('/');

    $response->assertOk();

    expect(array_column($response->inertiaProps('packages'), 'slug'))
        ->toBe(['synced-project', 'unsynced-project']);
});

test('a project flagged as a side project is listed there instead of the package grid', function () {
    ProjectFactory::new()->create([
        'slug' => 'shaka-playground',
        'title' => 'Shaka Playground',
        'github_repository' => 'francoism90/shaka-playground',
        'metadata' => [
            'kind' => 'misc',
            'type' => 'Experiment',
            'desc' => 'Drop in a manifest, see how Shaka Player handles it.',
            'status' => 'active',
        ],
    ]);

    $response = $this->get('/');

    $response->assertOk();

    expect(array_column($response->inertiaProps('packages'), 'slug'))->not->toContain('shaka-playground');

    expect($response->inertiaProps('sideProjects'))->toBe([[
        'name' => 'Shaka Playground',
        'slug' => 'shaka-playground',
        'kind' => 'misc',
        'type' => 'Experiment',
        'desc' => 'Drop in a manifest, see how Shaka Player handles it.',
        'status' => 'active',
        'href' => 'https://github.com/francoism90/shaka-playground',
    ]]);
});

test('a project with no kind metadata is listed as a package, not a side project', function () {
    ProjectFactory::new()->create(['slug' => 'laravel-podman', 'title' => 'Laravel Podman']);

    $response = $this->get('/');

    $response->assertOk();

    expect(array_column($response->inertiaProps('packages'), 'slug'))->toContain('laravel-podman');
    expect($response->inertiaProps('sideProjects'))->toBe([]);
});
