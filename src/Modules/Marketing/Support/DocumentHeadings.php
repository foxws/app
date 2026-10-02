<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;

/**
 * The docs package renders markdown to plain HTML with no heading ids, but
 * UContentToc needs both — an id on every h2/h3 to scroll/highlight against,
 * and a nested {id, text, children} list built from the same headings.
 */
final class DocumentHeadings
{
    /**
     * @param  array<string, string>  $links  Map of markdown source filenames (e.g. `installation.md`) to the real URL they should point to.
     * @return array{html: string, toc: array<int, array{id: string, text: string, children: array<int, array{id: string, text: string}>}>}
     */
    public static function extract(string $html, string $title = '', array $links = []): array
    {
        if (trim($html) === '') {
            return ['html' => $html, 'toc' => []];
        }

        $dom = new DOMDocument;

        // Process-wide setting — restore it so it doesn't leak into later
        // requests handled by the same Octane worker.
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?><div id="__docs_root__">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $dom->getElementById('__docs_root__');

        if (! $root) {
            return ['html' => $html, 'toc' => []];
        }

        self::removeLeadingTitle($root, $title);
        self::rewriteLinks($dom, $links);

        $slugs = [];
        $toc = [];

        foreach (iterator_to_array($dom->getElementsByTagName('h2')) as $heading) {
            /** @var DOMElement $heading */
            $toc[] = ['id' => self::assignId($heading, $slugs), 'text' => trim($heading->textContent), 'children' => []];
        }

        foreach (iterator_to_array($dom->getElementsByTagName('h3')) as $heading) {
            /** @var DOMElement $heading */
            $id = self::assignId($heading, $slugs);
            $text = trim($heading->textContent);

            $parent = self::precedingH2Index($heading, $toc);

            if ($parent !== null && isset($toc[$parent])) {
                $toc[$parent]['children'][] = ['id' => $id, 'text' => $text];
            }
        }

        $inner = '';

        foreach (iterator_to_array($root->childNodes) as $child) {
            $inner .= $dom->saveHTML($child);
        }

        return ['html' => $inner, 'toc' => $toc];
    }

    /**
     * The rendered body carries its own leading `<h1>` (the markdown's `#
     * Title` line), but the page already renders the document title
     * separately — drop it so the title doesn't appear twice.
     */
    private static function removeLeadingTitle(DOMElement $root, string $title): void
    {
        $first = $root->firstChild;

        while ($first !== null && ! $first instanceof DOMElement) {
            $first = $first->nextSibling;
        }

        if ($first instanceof DOMElement && $first->tagName === 'h1' && trim($first->textContent) === trim($title)) {
            $first->parentNode?->removeChild($first);
        }
    }

    /**
     * Cross-references between docs are written as relative markdown
     * filenames (e.g. `href="installation.md"`), which only resolve on
     * GitHub — rewrite them to the sibling document's real page.
     *
     * @param  array<string, string>  $links
     */
    private static function rewriteLinks(DOMDocument $dom, array $links): void
    {
        if ($links === []) {
            return;
        }

        foreach (iterator_to_array($dom->getElementsByTagName('a')) as $anchor) {
            /** @var DOMElement $anchor */
            $href = ltrim($anchor->getAttribute('href'), './');

            if (isset($links[$href])) {
                $anchor->setAttribute('href', $links[$href]);
            }
        }
    }

    /**
     * @param  array<string, int>  $slugs
     */
    private static function assignId(DOMElement $heading, array &$slugs): string
    {
        $base = Str::slug($heading->textContent) ?: 'section';
        $slug = $base;

        while (isset($slugs[$slug])) {
            $slugs[$base]++;
            $slug = "{$base}-{$slugs[$base]}";
        }

        $slugs[$slug] = $slugs[$slug] ?? 0;
        $heading->setAttribute('id', $slug);

        return $slug;
    }

    /**
     * Finds the most recent h2 (by document position) that precedes this h3,
     * so the h3 nests under the right parent in the flat $toc array.
     *
     * @param  array<int, array{id: string, text: string, children: array<int, array{id: string, text: string}>}>  $toc
     */
    private static function precedingH2Index(DOMElement $h3, array $toc): ?int
    {
        if ($toc === []) {
            return null;
        }

        $h2Count = 0;
        $lastIndex = null;
        $node = $h3->parentNode?->firstChild;

        while ($node !== null) {
            if ($node === $h3) {
                break;
            }

            if ($node instanceof DOMElement && $node->tagName === 'h2') {
                $lastIndex = $h2Count;
                $h2Count++;
            }

            $node = $node->nextSibling;
        }

        return $lastIndex;
    }
}
