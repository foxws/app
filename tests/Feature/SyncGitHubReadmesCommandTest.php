<?php

declare(strict_types=1);

use Foxws\Docs\Database\Factories\ProjectFactory;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    ProjectFactory::new()->create([
        'slug' => 'stry',
        'github_repository' => 'francoism90/stry',
        'metadata' => ['kind' => 'personal'],
    ]);
});

test('the synced readme introduces the project page, replacing the cached one', function () {
    Http::fake([
        'api.github.com/repos/francoism90/stry/readme' => Http::response("# stry\n\n**stry** streams your library."),
    ]);

    expect($this->get('/projects/stry')->inertiaProps('project.introduction'))->toBeNull();

    $this->artisan('github:sync-readmes')->assertSuccessful();

    expect($this->get('/projects/stry')->inertiaProps('project.introduction'))
        ->toBe("<p><strong>stry</strong> streams your library.</p>\n");
});

test('only fetches readmes for projects, not packages', function () {
    ProjectFactory::new()->create(['slug' => 'laravel-podman', 'github_repository' => 'foxws/laravel-podman']);

    Http::fake([
        'api.github.com/repos/francoism90/stry/readme' => Http::response('Intro.'),
    ]);

    $this->artisan('github:sync-readmes')->assertSuccessful();

    Http::assertSentCount(1);
});

test('a failed fetch keeps the last synced readme', function () {
    Http::fakeSequence('api.github.com/repos/francoism90/stry/readme')
        ->push('First intro.')
        ->push(status: 503);

    $this->artisan('github:sync-readmes')->assertSuccessful();
    $this->artisan('github:sync-readmes')->assertSuccessful();

    expect($this->get('/projects/stry')->inertiaProps('project.introduction'))->toBe("<p>First intro.</p>\n");
});

test('a removed readme takes the introduction away', function () {
    Http::fakeSequence('api.github.com/repos/francoism90/stry/readme')
        ->push('First intro.')
        ->push(status: 404);

    $this->artisan('github:sync-readmes')->assertSuccessful();
    $this->artisan('github:sync-readmes')->assertSuccessful();

    expect($this->get('/projects/stry')->inertiaProps('project.introduction'))->toBeNull();
});

test('an introduction in the front matter wins over the readme', function () {
    ProjectFactory::new()->create([
        'slug' => 'flatpaks',
        'github_repository' => 'francoism90/flatpaks',
        'metadata' => ['kind' => 'personal', 'introduction' => 'From the front matter.'],
    ]);

    Http::fake([
        'api.github.com/repos/*/readme' => Http::response('From the readme.'),
    ]);

    $this->artisan('github:sync-readmes')->assertSuccessful();

    expect($this->get('/projects/flatpaks')->inertiaProps('project.introduction'))->toBe("<p>From the front matter.</p>\n");
});
