<?php

declare(strict_types=1);

namespace Foundation\Providers;

use Foxws\Docs\Jobs\SyncDocsSearchIndex;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\ResponseCache\Facades\ResponseCache;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootResponseCacheInvalidation();
    }

    /**
     * `docs:sync` either runs inline (CommandFinished fires once synced) or
     * dispatches a chain ending in SyncDocsSearchIndex (JobProcessed fires
     * once that chain actually completes) — cover both so the cached
     * marketing pages never outlive the content they were rendered from.
     */
    protected function bootResponseCacheInvalidation(): void
    {
        Event::listen(CommandFinished::class, function (CommandFinished $event): void {
            if ($event->command === 'docs:sync') {
                ResponseCache::clear(['marketing']);
            }
        });

        Event::listen(JobProcessed::class, function (JobProcessed $event): void {
            if ($event->job->resolveName() === SyncDocsSearchIndex::class) {
                ResponseCache::clear(['marketing']);
            }
        });
    }
}
