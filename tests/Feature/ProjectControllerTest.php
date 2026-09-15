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
        ->not->toContain('Introduction');
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
