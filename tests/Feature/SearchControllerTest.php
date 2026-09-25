<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\DocumentFactory;
use Foxws\Docs\Database\Factories\ProjectFactory;
use Foxws\Docs\Database\Factories\VersionFactory;

test('searches documents by title', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'installation',
        'title' => 'Installation Guide',
        'section' => 'Getting Started',
        'body' => 'Run the installer to get started quickly.',
    ]);
    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'slug' => 'configuration',
        'title' => 'Configuration',
    ]);

    $response = $this->getJson('/api/v1/search?query=Installation');

    $response->assertOk();

    expect($response->json())->toBe([
        [
            'label' => 'Installation Guide',
            'labelHtml' => '<mark>Installation</mark> Guide',
            'suffix' => 'Test Project — Getting Started',
            'suffixHtml' => null,
            'prefix' => 'TEST-PROJECT',
            'description' => 'Run the installer to get started quickly.',
            'descriptionHtml' => null,
            'to' => route('document', ['test-project', 'installation'], absolute: false),
        ],
    ]);
});

test('highlights the query wherever it matches in the result', function () {
    $project = ProjectFactory::new()->create(['slug' => 'test-project', 'title' => 'Test Project']);
    $version = VersionFactory::new()->create(['project_id' => $project->id, 'is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'title' => 'Deploying with Podman',
        'section' => 'Podman',
        'body' => 'Learn how podman handles rootless containers.',
    ]);

    $response = $this->getJson('/api/v1/search?query=podman');

    $response->assertOk();

    expect($response->json('0.labelHtml'))->toBe('Deploying with <mark>Podman</mark>')
        ->and($response->json('0.suffixHtml'))->toBe('Test Project — <mark>Podman</mark>')
        ->and($response->json('0.descriptionHtml'))->toBe('Learn how <mark>podman</mark> handles rootless containers.');
});

test('shows HTML entities in the description as the characters they stand for', function () {
    $version = VersionFactory::new()->create(['is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'title' => 'Chaining',
        'body' => 'Chain commands with `a && b`.',
    ]);

    $response = $this->getJson('/api/v1/search?query=chain');

    $response->assertOk();

    expect($response->json('0.description'))->toBe('Chain commands with a && b.')
        ->and($response->json('0.descriptionHtml'))->toBe('<mark>Chain</mark> commands with a &amp;&amp; b.');
});

test('excludes documents marked as not searchable', function () {
    $version = VersionFactory::new()->create(['is_default' => true]);

    DocumentFactory::new()->create([
        'version_id' => $version->id,
        'title' => 'Hidden Draft',
        'searchable' => false,
    ]);

    $response = $this->getJson('/api/v1/search?query=Hidden');

    $response->assertOk();

    expect($response->json())->toBeEmpty();
});

test('filters results to a single project', function () {
    $wanted = ProjectFactory::new()->create(['slug' => 'wanted']);
    $wantedVersion = VersionFactory::new()->create(['project_id' => $wanted->id, 'is_default' => true]);
    DocumentFactory::new()->create([
        'version_id' => $wantedVersion->id,
        'title' => 'Shared Topic',
    ]);

    $other = ProjectFactory::new()->create(['slug' => 'other']);
    $otherVersion = VersionFactory::new()->create(['project_id' => $other->id, 'is_default' => true]);
    DocumentFactory::new()->create([
        'version_id' => $otherVersion->id,
        'title' => 'Shared Topic',
    ]);

    $response = $this->getJson('/api/v1/search?query=Shared&filter[project]=wanted');

    $response->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.prefix'))->toBe('WANTED');
});

test('requires a query of at least two characters', function () {
    $response = $this->getJson('/api/v1/search?query=a');

    $response->assertUnprocessable();
});
