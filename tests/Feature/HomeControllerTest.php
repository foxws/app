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
