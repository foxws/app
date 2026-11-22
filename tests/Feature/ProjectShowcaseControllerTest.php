<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\DocumentFactory;
use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;

test('builds the project page from front matter metadata', function () {
    ProjectFactory::new()->create([
        'slug' => 'stry',
        'title' => 'Stry',
        'github_repository' => 'francoism90/stry',
        'metadata' => [
            'kind' => 'personal',
            'desc' => 'A streaming platform built with Laravel and Inertia.js.',
            'image' => 'https://example.com/stry.png',
            'introduction' => "Stream your **own** library.\n\nSelf-hosted.",
            'technologies' => ['Laravel', 'Inertia.js', '', 42],
            'docs' => 'https://docs.example.com/stry',
        ],
    ]);

    $response = $this->get('/projects/stry');

    $response->assertOk();

    $project = $response->inertiaProps('project');

    expect($project)
        ->name->toBe('Stry')
        ->desc->toBe('A streaming platform built with Laravel and Inertia.js.')
        ->image->toBe('https://example.com/stry.png')
        ->technologies->toBe(['Laravel', 'Inertia.js'])
        ->docs->toBe('https://docs.example.com/stry')
        ->source->toBe('https://github.com/francoism90/stry')
        ->and($project['introduction'])
        ->toContain('<strong>own</strong>')
        ->toContain('<p>Self-hosted.</p>');
});

test('leaves out fields the front matter does not set, or sets to something unusable', function () {
    ProjectFactory::new()->create([
        'slug' => 'pods',
        'metadata' => ['kind' => 'personal', 'image' => 'docs/screenshot.png', 'technologies' => 'Podman'],
    ]);

    $response = $this->get('/projects/pods');

    $response->assertOk();

    expect($response->inertiaProps('project'))
        ->image->toBeNull()
        ->introduction->toBeNull()
        ->technologies->toBe([])
        ->docs->toBeNull();
});

test('introduces the project with the readme docs:sync stored', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'stry',
        'github_repository' => 'francoism90/stry',
        'metadata' => ['kind' => 'personal'],
    ]);
    DocumentFactory::new()->file('README.md')->for($project)->create([
        'body' => "# stry\n\n**stry** streams your library.\n\n> [!WARNING]\n> Keep backups.",
    ]);

    $response = $this->get('/projects/stry');

    $response->assertOk();

    expect($response->inertiaProps('project.introduction'))
        ->toContain('<p><strong>stry</strong> streams your library.</p>')
        ->toContain('<div class="callout callout-warning" data-callout="warning">')
        ->not->toContain('<h1>');
});

test('an introduction in the front matter wins over the readme', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'pods',
        'github_repository' => 'foxws/pods',
        'metadata' => ['kind' => 'personal', 'introduction' => 'From the front matter.'],
    ]);
    DocumentFactory::new()->file('README.md')->for($project)->create(['body' => 'From the readme.']);

    expect($this->get('/projects/pods')->inertiaProps('project.introduction'))->toBe("<p>From the front matter.</p>\n");
});

test('links "read the docs" to the docs on this site when the project has synced docs', function () {
    $project = ProjectFactory::new()->create(['slug' => 'stry', 'metadata' => ['kind' => 'personal']]);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/projects/stry');

    $response->assertOk();

    expect($response->inertiaProps('project.docs'))->toBe(route('project', 'stry', absolute: false));
});

test('lists the other projects, not this one or any package', function () {
    ProjectFactory::new()->create(['slug' => 'stry', 'title' => 'Stry', 'metadata' => ['kind' => 'personal']]);
    ProjectFactory::new()->create(['slug' => 'awesome-kde', 'title' => 'Awesome KDE', 'metadata' => ['kind' => 'misc']]);
    ProjectFactory::new()->create(['slug' => 'laravel-podman', 'title' => 'Laravel Podman']);

    $response = $this->get('/projects/stry');

    $response->assertOk();

    expect(array_column($response->inertiaProps('otherProjects'), 'slug'))->toBe(['awesome-kde'])
        ->and($response->inertiaProps('crumbs'))->toBe([
            ['label' => 'projects', 'href' => '/#projects'],
            ['label' => 'stry'],
        ]);
});

test('returns not found for a package, which has its docs page instead', function () {
    ProjectFactory::new()->create(['slug' => 'laravel-podman']);

    $this->get('/projects/laravel-podman')->assertNotFound();
});

test('only links to web addresses and paths from front matter', function (string $url) {
    ProjectFactory::new()->create([
        'slug' => 'linked',
        'github_repository' => 'example/linked',
        'metadata' => ['kind' => 'personal', 'docs' => $url, 'source' => $url],
    ]);

    $project = $this->get('/projects/linked')->assertOk()->inertiaProps('project');

    expect($project)
        ->docs->toBeNull()
        ->source->toBe('https://github.com/example/linked');
})->with(['javascript:alert(1)', ' JavaScript:alert(1)', 'data:text/html,hello', '//example.com']);

test('links to a path on this site from front matter', function () {
    ProjectFactory::new()->create([
        'slug' => 'linked',
        'metadata' => ['kind' => 'personal', 'docs' => '/laravel-docs/installation'],
    ]);

    expect($this->get('/projects/linked')->assertOk()->inertiaProps('project'))
        ->docs->toBe('/laravel-docs/installation');
});
