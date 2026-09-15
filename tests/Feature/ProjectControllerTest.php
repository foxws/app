<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\DocumentFactory;
use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;

test('renders the project overview document as the page body while keeping it in the nav', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'body' => "# Test Project\n\nWelcome to the docs.\n\n## Getting Started\n\nDo the thing.\n",
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'body' => "# Installation\n\nRun the installer.\n",
    ]);

    $response = $this->get('/test-project');

    $response->assertOk();

    $project = $response->inertiaProps('project');

    expect($project['overview']['html'])
        ->toContain('Welcome to the docs.')
        ->not->toContain('<h1>')
        ->and($project['overview']['toc'])->toContain(['id' => 'getting-started', 'text' => 'Getting Started', 'children' => []])
        ->and($project['nav'])->toHaveCount(1)
        ->and($project['nav'][0]['children'])
        ->toContain(['title' => 'Installation', 'path' => route('document', ['test-project', 'installation'], absolute: false), 'exact' => false])
        ->toContain(['title' => 'Introduction', 'path' => route('project', 'test-project', absolute: false), 'exact' => true])
        ->and($project['get_started'])->toBe(route('document', ['test-project', 'installation'], absolute: false));
});

test('omits the "get started" link when the project has no other documents besides its overview', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'body' => '# Test Project',
    ]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['get_started'])->toBeNull();
});

test('rewrites cross-reference links in the project overview to their sibling document pages', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'body' => "# Test Project\n\nSee [installation](installation.md) to get started.\n",
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'body' => "# Installation\n\nRun the installer.\n",
    ]);

    $response = $this->get('/test-project');

    $response->assertOk();

    $project = $response->inertiaProps('project');

    expect($project['overview']['html'])
        ->toContain('href="'.route('document', ['test-project', 'installation'], absolute: false).'"')
        ->not->toContain('href="installation.md"');
});

test('builds the package info box from index metadata and the default version', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'test-project',
        'title' => 'Test Project',
        'metadata' => [
            'requires' => 'PHP ^8.3',
            'laravel' => '11.x',
            'runtime' => 'Podman 5',
            'licence' => 'MIT',
        ],
    ]);
    VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true, 'name' => 'v1.0.3']);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['package'])->toBe([
        'version' => 'v1.0.3',
        'requires' => 'PHP ^8.3',
        'laravel' => '11.x',
        'runtime' => 'Podman 5',
        'licence' => 'MIT',
    ]);
});

test('omits the package info box when there is no version or relevant metadata', function () {
    ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['package'])->toBeNull();
});

test('builds the "used by" card from index metadata', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'test-project',
        'title' => 'Test Project',
        'metadata' => [
            'used_by' => [
                'name' => 'Stry',
                'desc' => 'See it running in production',
                'href' => '/stry',
            ],
        ],
    ]);
    VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['used_by'])->toBe([
        'name' => 'Stry',
        'desc' => 'See it running in production',
        'href' => '/stry',
    ]);
});

test('resolves a project page under a requested version, stamping generated links with ?version=', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $default = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true, 'name' => '1.0.0']);
    $latest = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => false, 'name' => 'latest']);

    DocumentFactory::new()->create([
        'version_id' => $latest->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'body' => "# Test Project\n\nSee [installation](installation.md).\n",
    ]);
    DocumentFactory::new()->create([
        'version_id' => $latest->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'body' => "# Installation\n\nRun the installer.\n",
    ]);
    // A default-version document with the same slug, so a passing test here
    // proves ?version= actually changes which content gets read, not just
    // which links get decorated.
    DocumentFactory::new()->create([
        'version_id' => $default->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'body' => '# Stable docs',
    ]);

    $response = $this->get('/test-project?version=latest');

    $response->assertOk();

    $project = $response->inertiaProps('project');
    $installationPath = route('document', ['project' => 'test-project', 'document' => 'installation', 'version' => 'latest'], absolute: false);

    expect($project['version'])->toBe('latest')
        ->and($project['overview']['html'])
        ->not->toContain('Stable docs')
        ->toContain('href="'.$installationPath.'"')
        ->and($project['nav'][0]['children'])
        ->toContain(['title' => 'Installation', 'path' => $installationPath, 'exact' => false])
        ->and($project['get_started'])->toBe($installationPath);
});

test('omits ?version= from generated links when the requested version is the default', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $default = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true, 'name' => '1.0.0']);

    DocumentFactory::new()->create([
        'version_id' => $default->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'body' => "# Installation\n\nRun the installer.\n",
    ]);

    $response = $this->get('/test-project?version=1.0.0');

    $response->assertOk();

    expect($response->inertiaProps('project')['get_started'])
        ->toBe(route('document', ['test-project', 'installation'], absolute: false));
});

test('falls back to the default version when the requested version does not exist', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true, 'name' => '1.0.0']);

    $response = $this->get('/test-project?version=nonexistent');

    $response->assertOk();

    expect($response->inertiaProps('project')['version'])->toBe('1.0.0');
});

test('rejects a non-string version query parameter', function () {
    ProjectFactory::new()->create(['slug' => 'test-project']);

    $response = $this->get('/test-project?version[]=a&version[]=b');

    $response->assertInvalid('version');
});

test('omits the "used by" card when the index has no used_by metadata, or it is missing a name or href', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'test-project',
        'title' => 'Test Project',
        'metadata' => ['used_by' => ['desc' => 'Missing name and href']],
    ]);
    VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['used_by'])->toBeNull();
});
