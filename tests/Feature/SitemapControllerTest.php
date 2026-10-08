<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\DocumentFactory;
use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;
use Illuminate\Testing\TestResponse;

/**
 * @return array<string, string|null> each <loc> keyed to its <lastmod>, if any
 */
function sitemapEntries(TestResponse $response): array
{
    $urlset = simplexml_load_string($response->getContent());

    $entries = [];

    foreach ($urlset->url as $url) {
        $entries[(string) $url->loc] = isset($url->lastmod) ? (string) $url->lastmod : null;
    }

    return $entries;
}

test('lists the static pages and each documented project, its overview at the project url', function () {
    $project = ProjectFactory::new()->create(['slug' => 'laravel-podman']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'index',
        'updated_at' => '2026-09-01 12:00:00',
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'installation',
        'updated_at' => '2026-09-20 12:00:00',
    ]);

    $response = $this->get('/sitemap.xml');

    $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');

    expect(sitemapEntries($response))->toBe([
        url('/') => null,
        url('/terms') => null,
        url('/laravel-podman') => '2026-09-20T12:00:00+00:00',
        url('/laravel-podman/installation') => '2026-09-20T12:00:00+00:00',
    ]);
});

test('lists only the default version so versioned urls do not show up as duplicates', function () {
    $project = ProjectFactory::new()->create(['slug' => 'laravel-podman']);
    $default = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);
    $older = VersionFactory::new()->create(['project_id' => $project->id]);

    DocumentFactory::new()->create(['version_id' => $default->id, 'slug' => 'installation']);
    DocumentFactory::new()->create(['version_id' => $older->id, 'slug' => 'upgrading']);

    $response = $this->get('/sitemap.xml');

    expect(array_keys(sitemapEntries($response)))
        ->toContain(url('/laravel-podman/installation'))
        ->not->toContain(url('/laravel-podman/upgrading'));
});

test('leaves out projects without documents, since their page is a 404', function () {
    $project = ProjectFactory::new()->create(['slug' => 'awesome-kde']);
    VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    $response = $this->get('/sitemap.xml');

    expect(array_keys(sitemapEntries($response)))->not->toContain(url('/awesome-kde'));
});

test('lists the project page of each project, with or without docs', function () {
    ProjectFactory::new()->create(['slug' => 'flatpaks', 'metadata' => ['kind' => 'personal']]);
    ProjectFactory::new()->create(['slug' => 'laravel-podman']);

    $response = $this->get('/sitemap.xml');

    expect(array_keys(sitemapEntries($response)))
        ->toContain(url('/projects/flatpaks'))
        ->not->toContain(url('/projects/laravel-podman'));
});
