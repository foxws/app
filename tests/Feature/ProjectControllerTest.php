<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\DocumentFactory;
use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;
use Illuminate\Support\Facades\Cache;

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

test('surrounds the overview with only a next link, into the first document', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'index',
        'title' => 'Introduction',
        'body' => '# Test Project',
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'installation',
        'title' => 'Installation',
        'order' => 1,
        'body' => "# Installation\n\nRun the installer.\n",
    ]);

    $response = $this->get('/test-project');

    $response->assertOk();

    $project = $response->inertiaProps('project');

    expect($project['surround'][0])->toBeNull()
        ->and($project['surround'][1])
        ->toBe(['title' => 'Installation', 'path' => route('document', ['test-project', 'installation'], absolute: false)]);
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
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true, 'name' => 'v1.0.3']);

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

test('shares the synced monthly install count of a package', function () {
    $project = ProjectFactory::new()->create(['slug' => 'laravel-podman', 'github_repository' => 'foxws/laravel-podman']);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    Cache::forever('packagist-downloads:foxws/laravel-podman', 1689);

    $response = $this->get('/laravel-podman');

    $response->assertOk();

    expect($response->inertiaProps('project.downloads'))->toBe(1689);
});

test('returns not found for a project without any version', function () {
    ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);

    $this->get('/test-project')->assertNotFound();
});

test('returns not found for a project whose version has no documents', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    $this->get('/test-project')->assertNotFound();
});

test('defaults the install command to composer for packages only', function (array $metadata, ?string $install) {
    $project = ProjectFactory::new()->create([
        'slug' => 'test-project',
        'github_repository' => 'foxws/test-project',
        'metadata' => $metadata,
    ]);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['install'])->toBe($install);
})->with([
    'package' => [[], 'composer require foxws/test-project'],
    'side project' => [['kind' => 'personal'], null],
    'side project with its own command' => [['kind' => 'personal', 'install' => 'flatpak install foo'], 'flatpak install foo'],
]);

test('reads a single "used by" project, as written before lists were supported', function () {
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
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['used_by'])->toBe([[
        'name' => 'Stry',
        'desc' => 'See it running in production',
        'href' => '/stry',
    ]]);
});

test('lists every "used by" project, skipping ones without a name or href', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'test-project',
        'title' => 'Test Project',
        'metadata' => [
            'used_by' => [
                ['name' => 'Stry', 'desc' => 'A self-hosted video streaming app.', 'href' => 'https://github.com/francoism90/stry'],
                ['name' => 'Missing href'],
                ['name' => 'foxws.nl', 'href' => 'https://foxws.nl'],
            ],
        ],
    ]);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['used_by'])->toBe([
        ['name' => 'Stry', 'desc' => 'A self-hosted video streaming app.', 'href' => 'https://github.com/francoism90/stry'],
        ['name' => 'foxws.nl', 'href' => 'https://foxws.nl'],
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
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true, 'name' => '1.0.0']);

    $response = $this->get('/test-project?version=nonexistent');

    $response->assertOk();

    expect($response->inertiaProps('project')['version'])->toBe('1.0.0');
});

test('rejects a non-string version query parameter', function () {
    ProjectFactory::new()->create(['slug' => 'test-project']);

    $response = $this->get('/test-project?version[]=a&version[]=b');

    $response->assertInvalid('version');
});

test('derives the source link from the github repository when metadata has no override', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'test-project',
        'title' => 'Test Project',
        'github_repository' => 'foxws/test-project',
    ]);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['source'])->toBe('https://github.com/foxws/test-project');
});

test('prefers a metadata source override over the github repository', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'test-project',
        'title' => 'Test Project',
        'github_repository' => 'foxws/test-project',
        'metadata' => ['source' => 'https://git.example.com/foxws/test-project'],
    ]);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['source'])->toBe('https://git.example.com/foxws/test-project');
});

test('omits the source link for a local-driven project with no metadata override', function () {
    $project = ProjectFactory::new()->local()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['source'])->toBeNull();
});

test('has no "used by" projects when the single entry is missing a name or href', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'test-project',
        'title' => 'Test Project',
        'metadata' => ['used_by' => ['desc' => 'Missing name and href']],
    ]);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/test-project');

    $response->assertOk();

    expect($response->inertiaProps('project')['used_by'])->toBe([]);
});
