<?php

declare(strict_types=1);

namespace Integrations\GitHub;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * A repository's README, as raw markdown. `github:sync-readmes` fetches it
 * on a schedule, so a visit only ever reads the cache, and a failed fetch
 * keeps the last known README rather than erasing it.
 */
final class GitHubReadme
{
    public static function cached(string $repository): ?string
    {
        $readme = Cache::get(self::cacheKey($repository));

        return is_string($readme) && trim($readme) !== '' ? $readme : null;
    }

    /**
     * Fetch and store the latest README, returning whether that succeeded.
     * A repository without one forgets what was stored before.
     */
    public static function refresh(string $repository): bool
    {
        try {
            $response = Http::timeout(5)
                ->when(config('docs.github.token'), fn (PendingRequest $http, string $token) => $http->withToken($token))
                ->accept('application/vnd.github.raw+json')
                ->get("https://api.github.com/repos/{$repository}/readme");
        } catch (ConnectionException) {
            return false;
        }

        if ($response->notFound()) {
            Cache::forget(self::cacheKey($repository));

            return true;
        }

        if (! $response->successful()) {
            return false;
        }

        Cache::forever(self::cacheKey($repository), $response->body());

        return true;
    }

    private static function cacheKey(string $repository): string
    {
        return "github-readme:{$repository}";
    }
}
