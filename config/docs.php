<?php

declare(strict_types=1);

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
use Foxws\Docs\Models\Version;

return [

    /*
     * Override these to use your own models. Yours must extend the
     * respective base model, e.g. `App\Models\Project extends
     * Foxws\Docs\Models\Project`.
     */
    'models' => [
        'project' => Project::class,
        'version' => Version::class,
        'document' => Document::class,
    ],

    /*
     * Package-wide default SEO, used when a project/document doesn't override it.
     */
    'seo' => [
        'title_pattern' => env('DOCS_SEO_TITLE_PATTERN', '%s — '.env('APP_NAME', 'Laravel')),
        'description' => env('DOCS_SEO_DESCRIPTION', ''),
    ],

    'markdown' => [
        /*
         * Passed to Str::markdown() when rendering a document's body.
         * Docs are pulled from GitHub repositories, so raw HTML is
         * stripped and unsafe links are disallowed by default.
         */
        'options' => [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ],
    ],

    'search' => [
        'enabled' => env('DOCS_SEARCH_ENABLED', true),
        'index_prefix' => env('DOCS_SEARCH_PREFIX', 'docs_'),
    ],

    'sync' => [
        'prune_missing' => env('DOCS_PRUNE_MISSING', true),

        /*
         * When enabled, docs:sync checks each project's latest GitHub
         * release before syncing and registers it as the default version
         * automatically, if it isn't already registered.
         */
        'auto_discover_versions' => env('DOCS_AUTO_DISCOVER_VERSIONS', true),

        /*
         * When a version is auto-discovered, this regex derives its display
         * name from the release tag — the first match wins (e.g. "v2.0.0"
         * or "version-2.0.0" both become "2.0.0"). Set to null to use the
         * raw tag as the name unchanged.
         */
        'version_name_pattern' => env('DOCS_VERSION_NAME_PATTERN', '/\d.*/'),

        /*
         * How many non-default versions to keep per project, most recently
         * created first — older ones are deleted on the next docs:sync. The
         * default version is never pruned, regardless of its age. Set to 0
         * to keep everything.
         */
        'keep_versions' => env('DOCS_KEEP_VERSIONS', 5),

        /*
         * Maximum number of old versions to delete per docs:sync run, once
         * keep_versions is exceeded. Caps how much a single run prunes if a
         * project has accumulated a large backlog.
         */
        'prune_chunk_size' => env('DOCS_PRUNE_CHUNK_SIZE', 50),

        'queue' => [
            /*
             * When enabled, docs:sync dispatches each project's sync as a
             * chained queue job (one after another, never in parallel)
             * instead of running inline. Override for a single run with
             * --queue or --sync regardless of this setting.
             */
            'enabled' => env('DOCS_SYNC_QUEUED', false),

            /*
             * Connection/queue name the chained jobs are dispatched on.
             * Leave null to use the application's default.
             */
            'connection' => env('DOCS_SYNC_QUEUE_CONNECTION'),
            'queue' => env('DOCS_SYNC_QUEUE'),

            /*
             * Overlap protection for a project's queued sync job (see
             * SyncProjectDocuments::middleware()): how long, in seconds, a
             * duplicate dispatch waits before retrying (overlap_release_after),
             * and the maximum a lock is trusted before being force-expired
             * if a worker dies mid-job without releasing it (overlap_expires_after).
             */
            'overlap_release_after' => env('DOCS_SYNC_QUEUE_OVERLAP_RELEASE_AFTER', 30),
            'overlap_expires_after' => env('DOCS_SYNC_QUEUE_OVERLAP_EXPIRES_AFTER', 600),
        ],
    ],

    'github' => [
        'token' => env('DOCS_GITHUB_TOKEN'),

        /*
         * GitHub's Trees API and the raw.githubusercontent.com CDN can both
         * briefly lag right after a ref is pushed/tagged, 404ing a file
         * that exists at the source. Each request is retried this many
         * times, waiting this long between attempts, before the sync gives
         * up on it. Set times to 1 to disable retrying.
         */
        'retry' => [
            'times' => env('DOCS_GITHUB_RETRY_TIMES', 3),
            'sleep_milliseconds' => env('DOCS_GITHUB_RETRY_SLEEP_MILLISECONDS', 500),
        ],
    ],

    'cache' => [
        /*
         * When disabled, Document::toHtml() always renders fresh from
         * markdown, regardless of its $shouldCache argument.
         */
        'enabled' => env('DOCS_CACHE_ENABLED', true),

        /*
         * Store used to cache each document's rendered HTML. Set to null
         * to use the application's default cache store.
         */
        'store' => env('DOCS_CACHE_STORE'),

        /*
         * How long (in seconds) rendered HTML stays cached before being
         * re-rendered from markdown. Set to null to cache forever — safe
         * since the cache key is tied to each document's blob_sha and
         * changes automatically whenever docs:sync updates its content.
         */
        'ttl' => env('DOCS_CACHE_TTL', 86400),
    ],

];
