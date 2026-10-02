<?php

declare(strict_types=1);

namespace Modules\Marketing\Commands;

use Domain\Projects\Models\Project;
use Illuminate\Console\Command;
use Integrations\Packagist\PackagistDownloads;
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
            ->filter(fn (Project $project): bool => $project->isPackage())
            ->map(fn (Project $project): ?string => $project->packagistName())
            ->filter();

        $refreshed = $packages->filter(fn (string $package): bool => PackagistDownloads::refresh($package));

        // The homepage is response-cached, so it would otherwise keep
        // showing the previous counts until the next docs:sync.
        if ($refreshed->isNotEmpty()) {
            ResponseCache::clear(['marketing']);
        }

        $this->info("Refreshed {$refreshed->count()} of {$packages->count()} packages.");
    }
}
