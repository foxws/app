<?php

declare(strict_types=1);

namespace Integrations\Packagist;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * A package's monthly installs from Packagist. `packagist:sync` fetches the
 * counts on a schedule, so a visit only ever reads the cache, and a failed
 * fetch keeps the last known count rather than erasing it.
 */
final class PackagistDownloads
{
    public static function monthly(string $package): ?int
    {
        // A count of zero isn't worth showing either, so it reads as "none".
        return Cache::integer(self::cacheKey($package), 0) ?: null;
    }

    /**
     * Fetch and store the latest count, returning whether that succeeded.
     */
    public static function refresh(string $package): bool
    {
        try {
            $response = Http::timeout(5)->get("https://packagist.org/packages/{$package}/stats.json");
        } catch (ConnectionException) {
            return false;
        }

        $monthly = $response->json('downloads.monthly');

        if (! $response->successful() || ! is_int($monthly)) {
            return false;
        }

        Cache::forever(self::cacheKey($package), $monthly);

        return true;
    }

    private static function cacheKey(string $package): string
    {
        return "packagist-downloads:{$package}";
    }
}
