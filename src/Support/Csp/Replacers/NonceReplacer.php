<?php

declare(strict_types=1);

namespace Support\Csp\Replacers;

use Spatie\ResponseCache\Replacers\Replacer;
use Symfony\Component\HttpFoundation\Response;

/**
 * The CSP header is added outside the response cache, so a cached page would
 * otherwise carry the nonce of the request that warmed it, while its header
 * carries a fresh one, and the browser blocks every nonced script.
 */
class NonceReplacer implements Replacer
{
    protected string $replacementString = '<laravel-responsecache-csp-nonce-here>';

    public function prepareResponseToCache(Response $response): void
    {
        $content = $response->getContent();

        if (! $content || ! app()->resolved('csp-nonce')) {
            return;
        }

        $nonce = app('csp-nonce');

        if (! $nonce || ! str_contains($content, $nonce)) {
            return;
        }

        $response->setContent(str_replace($nonce, $this->replacementString, $content));
    }

    public function replaceInCachedResponse(Response $response): void
    {
        $content = $response->getContent();

        if (! $content || ! str_contains($content, $this->replacementString)) {
            return;
        }

        $response->setContent(str_replace($this->replacementString, app('csp-nonce'), $content));
    }
}
