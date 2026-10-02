<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Spatie\ResponseCache\Facades\ResponseCache;
use Tests\CreatesApplication;

expect()
    ->extend('toBeSameModel', fn (Model $model) => $this->is($model)->toBeTrue());

uses(TestCase::class, CreatesApplication::class, RefreshDatabase::class)
    ->beforeEach(function () {
        // Make sure we do not run on production
        throw_if(app()->environment() === 'production');

        // Fake instances
        Bus::fake();
        Mail::fake();
        Notification::fake();
        Queue::fake();
        Storage::fake();

        // RefreshDatabase rolls back between tests, but the response cache
        // store lives outside that transaction — without this, a route
        // cached by one test (e.g. every ProjectControllerTest case hits
        // the same /test-project URL) leaks its stale response into every
        // later test against that same URL.
        ResponseCache::clear();

        // Setup database
        $this->seed();
    })
    ->in(__DIR__);

/**
 * The JSON-LD script app.blade.php prints into a full page response, decoded.
 *
 * @return array<string, mixed>
 */
function structuredData(TestResponse $response): array
{
    preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $response->getContent(), $matches);

    return json_decode($matches[1] ?? 'null', true, flags: JSON_THROW_ON_ERROR) ?? [];
}
