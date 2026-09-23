<?php

declare(strict_types=1);

namespace Support\ResponseCache;

use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;

/**
 * DefaultHasher keys purely on host/path/query/method/user, so a full-page
 * document (cached from a direct visit) and an Inertia XHR navigation to
 * the same URL hash identically. CacheResponse's read path doesn't check
 * shouldCacheRequest() before serving a hit — that rule only ever gated
 * writes — so an Inertia navigation could be served someone else's cached
 * HTML document instead of a page object, which Inertia then renders
 * inside its non-Inertia-response overlay. Disabling the cache outright
 * for these requests skips both the read and the write, rather than just
 * routing them to a bucket that (today) happens to always stay empty.
 */
final class InertiaAwareCacheProfile extends CacheAllSuccessfulGetRequests
{
    public function enabled(Request $request): bool
    {
        if ($request->header('X-Inertia')) {
            return false;
        }

        return parent::enabled($request);
    }
}
