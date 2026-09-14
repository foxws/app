<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\DocumentFactory;
use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;

test('renders a document with relative nav, surround, and breadcrumb links', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'order' => 1,
        'body' => "# Installation\n\nRun the installer.\n",
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'configuration',
        'title' => 'Configuration',
        'order' => 2,
        'body' => "# Configuration\n\nConfigure it.\n",
    ]);

    $response = $this->get('/test-project/installation');

    $response->assertOk();

    $document = $response->inertiaProps('document');
    $crumbs = $response->inertiaProps('crumbs');

    // Regression: these are built server-side with route(), which defaults to
    // fully-qualified URLs — Nuxt UI's Inertia Link treats any href with a
    // scheme as external and falls back to a hard browser navigation instead
    // of an SPA visit.
    expect($document['nav'][0]['children'])
        ->toContain(['title' => 'Installation', 'path' => route('document', ['test-project', 'installation'], absolute: false)])
        ->and($document['surround'][1])
        ->toBe(['title' => 'Configuration', 'path' => route('document', ['test-project', 'configuration'], absolute: false)])
        ->and($crumbs[0]['href'])->toBe(route('project', 'test-project', absolute: false))
        ->and($document['nav'][0]['children'][0]['path'])->not->toContain('http');
});
