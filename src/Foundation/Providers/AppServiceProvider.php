<?php

declare(strict_types=1);

namespace Foundation\Providers;

use Foxws\Docs\Jobs\SyncDocsSearchIndex;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Filesystem\FilesystemManager;
use Spatie\ResponseCache\Facades\ResponseCache;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerFilesystem();
        $this->registerTelescope();
    }

    public function boot(): void
    {
        $this->bootResponseCacheInvalidation();
    }

    protected function registerFilesystem(): void
    {
        $this->app->singleton('filesystem', fn ($app) => new FilesystemManager($app));
    }

    protected function registerTelescope(): void
    {
        if ((bool) config('telescope.enabled') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
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
