<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Illuminate\Support\Str;

/**
 * Wraps a search query's matches in <mark> for the command palette's
 * highlighted result text, rendered client-side via v-html.
 */
final class SearchHighlighter
{
    /**
     * Case-insensitive, UTF-8 aware. Returns null when there's nothing to
     * highlight, so callers can fall back to rendering $text as plain text
     * instead of paying for a v-html render that would look identical.
     */
    public static function highlight(string $text, ?string $query): ?string
    {
        $query = (string) Str::of((string) $query)->stripTags()->squish();

        if (blank($query) || blank($text)) {
            return null;
        }

        // Splits on the query with PREG_SPLIT_DELIM_CAPTURE so the
        // odd-indexed pieces are the matches themselves — each piece is
        // escaped on its own, then only the matches get wrapped, which
        // keeps this correct regardless of what HTML-sensitive characters
        // the query or surrounding text contain.
        $pieces = preg_split('/('.preg_quote($query, '/').')/ui', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        if (! is_array($pieces) || count($pieces) === 1) {
            return null;
        }

        return collect($pieces)
            ->map(fn (string $piece, int $index) => $index % 2 === 1
                ? '<mark>'.e($piece).'</mark>'
                : e($piece))
            ->implode('');
    }
}
