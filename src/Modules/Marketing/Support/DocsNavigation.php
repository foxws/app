<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
use Illuminate\Support\Collection;

/**
 * Builds the `navigation` array UContentNavigation expects: top-level
 * groups (by document section) whose `children` link to each document's
 * real page, shared between the project overview and document pages.
 */
final class DocsNavigation
{
    /**
     * @param  Collection<int, Document>  $documents
     * @return array<int, array{title: string, children: array<int, array{title: string, path: string}>}>
     */
    public static function build(Project $project, Collection $documents): array
    {
        return self::groups($documents)
            ->map(fn ($items, $group) => [
                'title' => $group,
                'children' => $items->map(fn ($document) => [
                    'title' => $document->title,
                    'path' => route('document', [$project->slug, $document->slug], absolute: false),
                ])->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * Documents in the same order the sidebar presents them — grouped by
     * section, sections in order of first appearance — so prev/next can
     * walk this list. The raw `order` column alone isn't enough: it only
     * ranks documents within their own section, so ties across different
     * sections sort by database order and interleave unrelated sections.
     *
     * @param  Collection<int, Document>  $documents
     * @return Collection<int, Document>
     */
    public static function flatten(Collection $documents): Collection
    {
        return self::groups($documents)->flatten(1)->values();
    }

    /**
     * The first document in the same reading order flatten() walks — the
     * one a "Get started" link should point to.
     *
     * @param  Collection<int, Document>  $documents
     */
    public static function firstDocument(Collection $documents): ?Document
    {
        return self::flatten($documents)->first();
    }

    /**
     * @param  Collection<int, Document>  $documents
     * @return Collection<string, Collection<int, Document>>
     */
    private static function groups(Collection $documents): Collection
    {
        return $documents->groupBy(fn ($document) => $document->section ?: 'Docs');
    }
}
