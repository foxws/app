<?php

declare(strict_types=1);

namespace Modules\Marketing\Commands;

use Foxws\Docs\Models\Project;
use Illuminate\Console\Command;
use Modules\Marketing\Support\PackagistDownloads;
use Modules\Marketing\Support\ProjectKind;
use Spatie\ResponseCache\Facades\ResponseCache;

class SyncPackagistDownloadsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'packagist:sync';

    /**
     * @var string
     */
    protected $description = 'Fetch the monthly Packagist installs shown on the homepage package cards';

    public function handle(): void
    {
        $packages = Project::query()
            ->get()
            ->filter(fn (Project $project): bool => ProjectKind::isPackage($project));

        $refreshed = $packages->filter(fn (Project $project): bool => PackagistDownloads::refresh($project));

        // The homepage is response-cached, so it would otherwise keep
        // showing the previous counts until the next docs:sync.
        if ($refreshed->isNotEmpty()) {
            ResponseCache::clear(['marketing']);
        }

        $this->info("Refreshed {$refreshed->count()} of {$packages->count()} packages.");
    }
}
