<?php

declare(strict_types=1);

namespace Modules\Marketing\Commands;

use Domain\Projects\Models\Project;
use Illuminate\Console\Command;
use Integrations\GitHub\GitHubReadme;
use Spatie\ResponseCache\Facades\ResponseCache;

class SyncGitHubReadmesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'github:sync-readmes';

    /**
     * @var string
     */
    protected $description = 'Fetch the READMEs the project pages use as their introduction';

    public function handle(): void
    {
        $repositories = Project::query()
            ->get()
            ->reject(fn (Project $project): bool => $project->isPackage())
            ->map(fn (Project $project): ?string => $project->githubRepository())
            ->filter();

        $refreshed = $repositories->filter(fn (string $repository): bool => GitHubReadme::refresh($repository));

        // Project pages are response-cached, so they would otherwise keep
        // showing the previous README until the next docs:sync.
        if ($refreshed->isNotEmpty()) {
            ResponseCache::clear(['marketing']);
        }

        $this->info("Refreshed {$refreshed->count()} of {$repositories->count()} READMEs.");
    }
}
