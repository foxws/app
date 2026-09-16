<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Illuminate\Support\Str;

/**
 * Resolves a document's meta description: front matter wins, cascading
 * from the document's own `seo.description` to the project's, and
 * finally to an excerpt of the rendered body.
 */
final class DocumentDescription
{
    public static function resolve(?string $documentSeoDescription, ?string $projectSeoDescription, string $html): string
    {
        if (filled($documentSeoDescription)) {
            return $documentSeoDescription;
        }

        if (filled($projectSeoDescription)) {
            return $projectSeoDescription;
        }

        return Str::of($html)->stripTags()->squish()->limit(160)->toString();
    }
}
