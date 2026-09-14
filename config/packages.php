<?php

declare(strict_types=1);

/*
 * foxws's own composer packages, shown on the homepage and rendered as
 * individual project pages at /{slug}. Stry is the flagship project — the
 * self-hosted video platform these packages were extracted from.
 *
 * Versions/install counts are placeholders until wired to Packagist. Doc
 * content (nav, prose, code samples) is static here until foxws/laravel-docs
 * is configured and synced from each package's GitHub repo.
 */

return [

    'stry' => [
        'name' => 'Stry',
        'slug' => 'foxws/stry',
        'flagship' => true,
        'role' => null,
        'desc' => 'Self-hosted video on demand. Shaka playback, Podman transcoding, Inertia admin.',
        'version' => 'v0.8 beta',
        'eyebrow' => 'SELF-HOSTED VOD · SHAKA · PODMAN · INERTIA',
        'title_lines' => ['Stry', ''],
        'lead' => 'Your own streaming platform. Upload a file, rootless Podman runners transcode it, Shaka Player serves adaptive playback — and the whole admin is Inertia.',
        'install' => 'composer create-project foxws/stry',
        'code' => [
            'filename' => 'config/stry.php',
            'language' => 'PHP',
            'code' => "return [\n    'runtime' => 'podman',\n    'profiles' => ['1080p', '720p', '480p'],\n    'manifest' => 'dash',\n];",
        ],
        'meta' => [
            ['k' => 'Latest release', 'v' => 'v0.8 beta'],
            ['k' => 'Requires', 'v' => 'PHP ^8.3'],
            ['k' => 'Laravel', 'v' => '11.x'],
            ['k' => 'Inertia', 'v' => '2.x'],
            ['k' => 'Licence', 'v' => 'MIT'],
        ],
        'nav' => [
            ['group' => 'GETTING STARTED', 'items' => ['Introduction', 'Installation', 'Configuration']],
            ['group' => 'PIPELINE', 'items' => ['Podman runners', 'Transcode profiles', 'Manifests', 'Storage drivers']],
            ['group' => 'PLAYBACK', 'items' => ['Shaka setup', 'DRM keys', 'Analytics']],
            ['group' => 'REFERENCE', 'items' => ['API', 'Upgrade guide', 'Changelog']],
        ],
        'on_this_page' => ['Requirements', 'Install with Composer', 'Podman runtime', 'Next steps'],
        'features' => [
            ['t' => 'Adaptive playback', 'd' => 'Shaka Player with DASH and HLS manifests.'],
            ['t' => 'Podman pipeline', 'd' => 'Rootless containers per transcode job.'],
            ['t' => 'Inertia admin', 'd' => 'Typed page props, no API layer to maintain.'],
            ['t' => 'Storage agnostic', 'd' => 'Local disk, S3 or any Flysystem driver.'],
        ],
    ],

    'laravel-podman' => [
        'name' => 'Laravel Podman',
        'slug' => 'foxws/laravel-podman',
        'flagship' => false,
        'role' => 'CONTAINERS',
        'desc' => 'Define, run and supervise rootless Podman containers from your Laravel app. Pods as config, jobs as containers.',
        'version' => 'v1.0.3',
        'eyebrow' => 'CONTAINERS · ROOTLESS · LARAVEL 11',
        'title_lines' => ['Laravel', 'Podman'],
        'lead' => 'Define, run and supervise rootless Podman containers straight from your Laravel app. Pods as config, jobs as containers.',
        'install' => 'composer require foxws/laravel-podman',
        'code' => [
            'filename' => 'config/podman.php',
            'language' => 'PHP',
            'code' => "return [\n    'pods' => [\n        'transcode' => [\n            'image' => 'docker.io/jrottenberg/ffmpeg',\n            'rootless' => true,\n            'timeout' => 3600,\n        ],\n    ],\n];",
        ],
        'meta' => [
            ['k' => 'Latest release', 'v' => 'v1.0.3'],
            ['k' => 'Requires', 'v' => 'PHP ^8.3'],
            ['k' => 'Laravel', 'v' => '11.x'],
            ['k' => 'Runtime', 'v' => 'Podman 5'],
            ['k' => 'Licence', 'v' => 'MIT'],
        ],
        'nav' => [
            ['group' => 'GETTING STARTED', 'items' => ['Introduction', 'Installation', 'Configuration']],
            ['group' => 'CONTAINERS', 'items' => ['Defining a pod', 'Running a job', 'Volumes & mounts', 'Health checks']],
            ['group' => 'OPERATIONS', 'items' => ['Supervision', 'Logs & events', 'Rootless setup']],
            ['group' => 'REFERENCE', 'items' => ['API', 'Changelog']],
        ],
        'on_this_page' => ['What it does', 'Installation', 'Defining a pod', 'Running a job', 'Rootless notes'],
        'features' => [
            ['t' => 'Pods as config', 'd' => 'Declare containers in a config file, not shell scripts.'],
            ['t' => 'Jobs as containers', 'd' => 'Dispatch a queue job, get an isolated container.'],
            ['t' => 'Rootless by default', 'd' => 'No daemon, no root, no privileged sockets.'],
            ['t' => 'Health checks', 'd' => 'Restart policies wired to Laravel events.'],
        ],
    ],

    'laravel-shaka' => [
        'name' => 'Laravel Shaka',
        'slug' => 'foxws/laravel-shaka',
        'flagship' => false,
        'role' => 'STREAMING',
        'desc' => 'Package media into DASH and HLS, generate manifests and hand Shaka Player everything it needs.',
        'version' => 'v0.7.0',
        'eyebrow' => 'STREAMING · DASH · HLS',
        'title_lines' => ['Laravel', 'Shaka'],
        'lead' => 'Package media into DASH and HLS, generate manifests and hand Shaka Player everything it needs to play back adaptively.',
        'install' => 'composer require foxws/laravel-shaka',
        'code' => [
            'filename' => 'config/shaka.php',
            'language' => 'PHP',
            'code' => "return [\n    'manifests' => ['dash', 'hls'],\n    'renditions' => ['1080p', '720p', '480p'],\n];",
        ],
        'meta' => [
            ['k' => 'Latest release', 'v' => 'v0.7.0'],
            ['k' => 'Requires', 'v' => 'PHP ^8.3'],
            ['k' => 'Laravel', 'v' => '11.x'],
            ['k' => 'Player', 'v' => 'Shaka 4.x'],
            ['k' => 'Licence', 'v' => 'MIT'],
        ],
        'nav' => [
            ['group' => 'GETTING STARTED', 'items' => ['Introduction', 'Installation', 'Configuration']],
            ['group' => 'STREAMING', 'items' => ['Manifests', 'Renditions', 'DRM keys']],
            ['group' => 'REFERENCE', 'items' => ['API', 'Changelog']],
        ],
        'on_this_page' => ['What it does', 'Installation', 'Generating a manifest'],
        'features' => [
            ['t' => 'DASH & HLS', 'd' => 'One command, both manifest formats.'],
            ['t' => 'DRM ready', 'd' => 'Widevine and PlayReady key delivery hooks.'],
            ['t' => 'Adaptive renditions', 'd' => 'Bitrate ladders derived from source resolution.'],
            ['t' => 'Shaka-native', 'd' => 'Manifests validated against Shaka Player.'],
        ],
    ],

    'laravel-model-cache' => [
        'name' => 'Laravel Model Cache',
        'slug' => 'foxws/laravel-model-cache',
        'flagship' => false,
        'role' => 'PERFORMANCE',
        'desc' => 'Cache helpers for Eloquent models — tagged, invalidated on save, no manual key juggling.',
        'version' => 'v1.2.0',
        'eyebrow' => 'PERFORMANCE · CACHING',
        'title_lines' => ['Laravel', 'Model Cache'],
        'lead' => 'Cache helpers for Laravel Eloquent models — tagged, invalidated on save, no manual key juggling.',
        'install' => 'composer require foxws/laravel-model-cache',
        'code' => [
            'filename' => 'app/Models/Video.php',
            'language' => 'PHP',
            'code' => "use Foxws\\ModelCache\\Cacheable;\n\nclass Video extends Model\n{\n    use Cacheable;\n}",
        ],
        'meta' => [
            ['k' => 'Latest release', 'v' => 'v1.2.0'],
            ['k' => 'Requires', 'v' => 'PHP ^8.3'],
            ['k' => 'Laravel', 'v' => '11.x'],
            ['k' => 'Licence', 'v' => 'MIT'],
        ],
        'nav' => [
            ['group' => 'GETTING STARTED', 'items' => ['Introduction', 'Installation', 'Configuration']],
            ['group' => 'CACHING', 'items' => ['Tagging', 'Invalidation', 'Query scopes']],
            ['group' => 'REFERENCE', 'items' => ['API', 'Changelog']],
        ],
        'on_this_page' => ['What it does', 'Installation', 'Caching a query'],
        'features' => [
            ['t' => 'Tagged caching', 'd' => 'Invalidate by model, not by hand-tracked keys.'],
            ['t' => 'Save-aware', 'd' => 'Cache clears automatically on model events.'],
            ['t' => 'Query scopes', 'd' => 'Cache scoped queries, not just single models.'],
            ['t' => 'Store agnostic', 'd' => 'Works with any tag-capable cache store.'],
        ],
    ],

    'laravel-algos' => [
        'name' => 'Laravel Algos',
        'slug' => 'foxws/laravel-algos',
        'flagship' => false,
        'role' => 'DOMAIN',
        'desc' => 'Create algorithms (algos) as first-class, testable classes with a consistent contract.',
        'version' => 'v0.9.1',
        'eyebrow' => 'DOMAIN · ALGORITHMS',
        'title_lines' => ['Laravel', 'Algos'],
        'lead' => 'Create algorithms (algos) for your Laravel application as first-class, testable classes with a consistent contract.',
        'install' => 'composer require foxws/laravel-algos',
        'code' => [
            'filename' => 'app/Algos/RecommendVideos.php',
            'language' => 'PHP',
            'code' => "use Foxws\\Algos\\Algo;\n\nclass RecommendVideos extends Algo\n{\n    public function handle(): Collection\n    {\n        //\n    }\n}",
        ],
        'meta' => [
            ['k' => 'Latest release', 'v' => 'v0.9.1'],
            ['k' => 'Requires', 'v' => 'PHP ^8.3'],
            ['k' => 'Laravel', 'v' => '11.x'],
            ['k' => 'Licence', 'v' => 'MIT'],
        ],
        'nav' => [
            ['group' => 'GETTING STARTED', 'items' => ['Introduction', 'Installation', 'Configuration']],
            ['group' => 'ALGOS', 'items' => ['Creating an algo', 'Registering an algo', 'Testing']],
            ['group' => 'REFERENCE', 'items' => ['API', 'Changelog']],
        ],
        'on_this_page' => ['What it does', 'Installation', 'Creating an algo'],
        'features' => [
            ['t' => 'Consistent contract', 'd' => 'Every algo implements the same interface.'],
            ['t' => 'Testable by design', 'd' => 'Pure classes, no framework glue required.'],
            ['t' => 'Composable', 'd' => 'Chain algos or run them independently.'],
            ['t' => 'Artisan scaffolding', 'd' => 'make:algo generates the boilerplate.'],
        ],
    ],

];
