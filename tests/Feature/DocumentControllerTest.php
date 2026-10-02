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
        ->toContain(['title' => 'Installation', 'path' => route('document', ['test-project', 'installation'], absolute: false), 'exact' => false])
        ->and($document['surround'][1])
        ->toBe(['title' => 'Configuration', 'path' => route('document', ['test-project', 'configuration'], absolute: false)])
        ->and($crumbs[0]['href'])->toBe(route('project', 'test-project', absolute: false))
        ->and($document['nav'][0]['children'][0]['path'])->not->toContain('http');
});

test('surrounds a document with its neighbors in the same section, not raw database order', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    // `order` only ranks documents within their own section (each section
    // restarts its own count), so sorting the whole table by `order` alone
    // interleaves unrelated sections — here "Models" (Reference, order 3)
    // falls between Installation (order 2) and its real next neighbor,
    // Registering Projects (order 4), both in "Getting Started".
    DocumentFactory::new()->create([
        'version_id' => $version->id, 'slug' => 'upgrading', 'title' => 'Upgrading',
        'section' => 'Meta', 'order' => 1,
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id, 'slug' => 'installation', 'title' => 'Installation',
        'section' => 'Getting Started', 'order' => 2,
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id, 'slug' => 'models', 'title' => 'Models',
        'section' => 'Reference', 'order' => 3,
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id, 'slug' => 'registering-projects', 'title' => 'Registering Projects',
        'section' => 'Getting Started', 'order' => 4,
    ]);

    $response = $this->get('/test-project/installation');

    $response->assertOk();

    $document = $response->inertiaProps('document');

    expect($document['surround'][0])
        ->toBe(['title' => 'Upgrading', 'path' => route('document', ['test-project', 'upgrading'], absolute: false)])
        ->and($document['surround'][1])
        ->toBe(['title' => 'Registering Projects', 'path' => route('document', ['test-project', 'registering-projects'], absolute: false)]);
});

test('redirects a direct visit to the overview document\'s own route to the project page', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'body' => '# Test Project',
    ]);

    $response = $this->get('/test-project/index');

    $response->assertRedirect(route('project', 'test-project', absolute: false));
});

test('points prev/next and cross-reference links at the project page when the neighbor is the overview', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'order' => 1,
        'body' => '# Test Project',
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'order' => 2,
        'body' => "# Installation\n\nSee [the intro](index.md) first.\n",
    ]);

    $response = $this->get('/test-project/installation');

    $response->assertOk();

    $document = $response->inertiaProps('document');

    expect($document['surround'][0])
        ->toBe(['title' => 'Introduction', 'path' => route('project', 'test-project', absolute: false)])
        ->and($document['html'])
        ->toContain('href="'.route('project', 'test-project', absolute: false).'"')
        ->not->toContain('test-project/index');
});

test('resolves a document under a requested version, stamping generated links with ?version=', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $default = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true, 'name' => '1.0.0']);
    $latest = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => false, 'name' => 'latest']);

    DocumentFactory::new()->create([
        'version_id' => $latest->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'order' => 1,
        'body' => "# Installation\n\nRun the installer.\n",
    ]);
    DocumentFactory::new()->create([
        'version_id' => $latest->id,
        'slug' => 'configuration',
        'title' => 'Configuration',
        'order' => 2,
        'body' => "# Configuration\n\nConfigure it.\n",
    ]);
    // A default-version document with the same slug, so a passing test here
    // proves ?version= actually changes which content gets read.
    DocumentFactory::new()->create([
        'version_id' => $default->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'body' => '# Stable installation',
    ]);

    $response = $this->get('/test-project/installation?version=latest');

    $response->assertOk();

    $document = $response->inertiaProps('document');
    $crumbs = $response->inertiaProps('crumbs');
    $projectPath = route('project', ['project' => 'test-project', 'version' => 'latest'], absolute: false);
    $configurationPath = route('document', ['project' => 'test-project', 'document' => 'configuration', 'version' => 'latest'], absolute: false);

    expect($document['html'])->not->toContain('Stable installation')
        ->and($document['project'])->toBe(['name' => 'Test Project', 'slug' => 'test-project', 'href' => $projectPath])
        ->and($document['surround'][1])->toBe(['title' => 'Configuration', 'path' => $configurationPath])
        ->and($crumbs[0]['href'])->toBe($projectPath);
});

test('omits ?version= from a document\'s generated links when the requested version is the default', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $default = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true, 'name' => '1.0.0']);

    DocumentFactory::new()->create([
        'version_id' => $default->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'body' => "# Installation\n\nRun the installer.\n",
    ]);

    $response = $this->get('/test-project/installation?version=1.0.0');

    $response->assertOk();

    $document = $response->inertiaProps('document');

    expect($document['project']['href'])->toBe(route('project', 'test-project', absolute: false));
});

test('preserves the requested version when redirecting a direct visit to the overview document', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true, 'name' => '1.0.0']);
    $latest = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => false, 'name' => 'latest']);

    DocumentFactory::new()->create([
        'version_id' => $latest->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'body' => '# Test Project',
    ]);

    $response = $this->get('/test-project/index?version=latest');

    $response->assertRedirect(route('project', ['project' => 'test-project', 'version' => 'latest'], absolute: false));
});

test('falls back to the default version when a document is requested under an unknown version', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $default = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true, 'name' => '1.0.0']);

    DocumentFactory::new()->create([
        'version_id' => $default->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'body' => "# Installation\n\nRun the installer.\n",
    ]);

    $response = $this->get('/test-project/installation?version=nonexistent');

    $response->assertOk();

    expect($response->inertiaProps('document')['project']['href'])
        ->toBe(route('project', 'test-project', absolute: false));
});

test('gives a document JSON-LD breadcrumbs back through its project to the homepage', function () {
    config(['app.name' => 'Foxws']);

    $project = ProjectFactory::new()->create(['slug' => 'laravel-podman', 'title' => 'Laravel Podman']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);
    DocumentFactory::new()->create(['version_id' => $version->id, 'slug' => 'installation', 'title' => 'Installation']);

    $response = $this->get('/laravel-podman/installation');

    expect(structuredData($response)['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Foxws', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Laravel Podman', 'item' => url('/laravel-podman')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Installation', 'item' => url('/laravel-podman/installation')],
    ]);
});
