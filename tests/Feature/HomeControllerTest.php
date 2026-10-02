<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\DocumentFactory;
use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use Inertia\Inertia;

test('orders packages by title, regardless of when they were created or last synced', function () {
    $zeta = ProjectFactory::new()->create(['slug' => 'zeta-project', 'title' => 'Zeta Project']);
    VersionFactory::new()->create(['project_id' => $zeta->id, 'is_default' => true, 'last_synced_at' => now()]);

    $alpha = ProjectFactory::new()->create(['slug' => 'alpha-project', 'title' => 'Alpha Project']);
    VersionFactory::new()->create(['project_id' => $alpha->id, 'is_default' => true, 'last_synced_at' => now()->subDays(10)]);

    $response = $this->get('/');

    $response->assertOk();

    expect(array_column($response->inertiaProps('packages'), 'slug'))
        ->toBe(['alpha-project', 'zeta-project']);
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
        'type' => 'Experiment',
        'desc' => 'Drop in a manifest, see how Shaka Player handles it.',
        'status' => 'active',
        'href' => 'https://github.com/francoism90/shaka-playground',
    ]]);
});

test('a side project with synced docs links to its own page instead of its source', function () {
    $project = ProjectFactory::new()->create(['slug' => 'stry', 'metadata' => ['kind' => 'personal']]);
    VersionFactory::new()->has(DocumentFactory::new(), 'documents')->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/');

    $response->assertOk();

    expect($response->inertiaProps('sideProjects.0.href'))->toBe('/stry');
});

test('a side project whose version has no docs still links to its source', function () {
    $project = ProjectFactory::new()->create([
        'slug' => 'flatpaks',
        'github_repository' => 'francoism90/flatpaks',
        'metadata' => ['kind' => 'personal'],
    ]);
    VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/');

    $response->assertOk();

    expect($response->inertiaProps('sideProjects.0.href'))->toBe('https://github.com/francoism90/flatpaks');
});

test('a project with no kind metadata is listed as a package, not a side project', function () {
    ProjectFactory::new()->create(['slug' => 'laravel-podman', 'title' => 'Laravel Podman']);

    $response = $this->get('/');

    $response->assertOk();

    expect(array_column($response->inertiaProps('packages'), 'slug'))->toContain('laravel-podman');
    expect($response->inertiaProps('sideProjects'))->toBe([]);
});

test('package install counts load after the page, keyed by slug', function () {
    Http::preventStrayRequests();
    Http::fake([
        'packagist.org/packages/foxws/laravel-podman/stats.json' => Http::response(['downloads' => ['total' => 2839, 'monthly' => 1689, 'daily' => 52]]),
    ]);

    ProjectFactory::new()->create(['slug' => 'laravel-podman', 'github_repository' => 'foxws/laravel-podman']);

    expect($this->get('/')->inertiaProps())->not->toHaveKey('downloads');

    loadDownloads()->assertJsonPath('props.downloads', ['laravel-podman' => 1689]);
});

test('a package whose Packagist lookup fails, or that is not on GitHub, gets no install count', function () {
    Http::preventStrayRequests();
    Http::fake([
        'packagist.org/packages/foxws/laravel-gone/stats.json' => Http::response(status: 404),
    ]);

    ProjectFactory::new()->create(['slug' => 'laravel-gone', 'github_repository' => 'foxws/laravel-gone']);
    ProjectFactory::new()->local()->create(['slug' => 'laravel-local']);

    loadDownloads()->assertJsonPath('props.downloads', []);
});

/**
 * A full visit followed by the partial reload Inertia's client sends for the
 * deferred prop — made by hand because assertInertia() can't read the
 * response-cached homepage, and the visit sets the asset version it checks.
 */
function loadDownloads(): TestResponse
{
    test()->get('/')->assertOk();

    return test()->get('/', [
        'X-Inertia' => 'true',
        'X-Inertia-Version' => Inertia::getVersion(),
        'X-Inertia-Partial-Component' => 'HomePage',
        'X-Inertia-Partial-Data' => 'downloads',
    ])->assertOk();
}
