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
 * install command uses. `packagist:sync` fetches the counts on a schedule,
 * so a visit only ever reads the cache, and a failed fetch keeps the last
 * known count rather than erasing it.
 */
final class PackagistDownloads
{
    public static function monthly(Project $project): ?int
    {
        $name = self::packageName($project);

        if ($name === null) {
            return null;
        }

        // Redis stores numbers unserialized, so they come back as strings.
        $monthly = Cache::get(self::cacheKey($name));

        return is_numeric($monthly) ? (int) $monthly : null;
    }

    /**
     * Fetch and store the latest count, returning whether that succeeded.
     */
    public static function refresh(Project $project): bool
    {
        $name = self::packageName($project);

        if ($name === null) {
            return false;
        }

        try {
            $response = Http::timeout(5)->get("https://packagist.org/packages/{$name}/stats.json");
        } catch (ConnectionException) {
            return false;
        }

        $monthly = $response->json('downloads.monthly');

        if (! $response->successful() || ! is_int($monthly)) {
            return false;
        }

        Cache::forever(self::cacheKey($name), $monthly);

        return true;
    }

    private static function packageName(Project $project): ?string
    {
        if ($project->driver !== ProjectDriver::Github || blank($project->github_repository)) {
            return null;
        }

        return $project->github_repository;
    }

    private static function cacheKey(string $name): string
    {
        return "packagist-downloads:{$name}";
    }
}
