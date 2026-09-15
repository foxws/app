<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\DocumentFactory;
use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;

test('renders the project overview document as the page body and excludes it from the nav', function () {
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
        ->toContain(['title' => 'Installation', 'path' => route('document', ['test-project', 'installation'], absolute: false)])
        ->and(collect($project['nav'][0]['children'])->pluck('title')->all())
        ->not->toContain('Introduction')
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
