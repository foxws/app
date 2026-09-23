<?php

declare(strict_types=1);

namespace Support\ResponseCache;

use Illuminate\Http\Request;
use Spatie\ResponseCache\Hasher\DefaultHasher;

/**
 * DefaultHasher keys purely on host/path/query/method/user, so a full-page
 * document (cached from a direct visit) and an Inertia XHR navigation to
 * the same URL hash identically. CacheResponse's read path doesn't consult
 * the cache profile before serving a hit, so an Inertia visit can be
 * served someone else's cached HTML document instead of a page object —
 * Inertia then renders that raw HTML in its non-Inertia-response overlay.
 * Folding X-Inertia into the hash keeps the two responses apart.
 */
final class InertiaAwareHasher extends DefaultHasher
{
    public function getHashFor(Request $request): string
    {
        $strings = [
            'responsecache',
            $request->getHost(),
            $this->getNormalizedRequestUri($request),
            $request->getMethod(),
            $this->getCacheNameSuffix($request),
            $request->header('X-Inertia') ? 'inertia' : 'document',
        ];

        return hash('xxh128', implode('-', $strings));
    }
}
