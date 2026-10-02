<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Foxws\Docs\Enums\ProjectDriver;
use Foxws\Docs\Models\Project;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * A package's monthly installs from Packagist, for the homepage cards. The
 * composer name is the project's GitHub repository — the same one the
 * install command uses. Counts are served stale while they refresh, and a
 * failed lookup returns null (and isn't cached) so the card just omits it.
 */
final class PackagistDownloads
{
    public static function monthly(Project $project): ?int
    {
        if ($project->driver !== ProjectDriver::Github || blank($project->github_repository)) {
            return null;
        }

        $name = $project->github_repository;

        return Cache::flexible("packagist-downloads:{$name}", [now()->addHours(12), now()->addDays(2)], function () use ($name): ?int {
            try {
                $response = Http::timeout(3)->get("https://packagist.org/packages/{$name}/stats.json");
            } catch (ConnectionException) {
                return null;
            }

            $monthly = $response->json('downloads.monthly');

            return $response->successful() && is_int($monthly) ? $monthly : null;
        });
    }
}
