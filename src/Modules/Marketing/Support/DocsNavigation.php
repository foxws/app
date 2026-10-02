<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
use Foxws\Docs\Models\Version;
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
     * @return array<int, array{title: string, children: array<int, array{title: string, path: string, exact: bool}>}>
     */
    public static function build(Project $project, Collection $documents, ?string $version = null): array
    {
        $overview = $project->indexDocument($documents);

        return self::groups($documents)
            ->map(fn ($items, $group) => [
                'title' => $group,
                'children' => $items->map(fn ($document) => [
                    'title' => $document->title,
                    'path' => self::pathFor($project, $document, $overview, $version),
                    // The overview's path is /{project} — a prefix of every
                    // sibling document's own /{project}/{document} URL — so
                    // without exact matching, Nuxt UI's Link would mark it
                    // "active" (and highlight it) on every one of them too.
                    'exact' => (bool) ($overview && $document->is($overview)),
                ])->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * The `?version=` value for links within $version: only set when it
     * isn't the project's default, which keeps the common case's URLs clean.
     */
    public static function versionParam(?Version $version): ?string
    {
        return $version && ! $version->is_default ? $version->name : null;
    }

    /**
     * The project's own overview page, optionally stamped with a `?version=`
     * query string. $version is the version's *name*, not the Version model
     * itself, and should be omitted (null) when it's the project's default —
     * keeps the common case's URLs clean.
     */
    public static function projectPath(Project $project, ?string $version = null): string
    {
        return route('project', array_filter([
            'project' => $project->slug,
            'version' => $version,
        ], fn ($value) => $value !== null), absolute: false);
    }

    /**
     * The route for a single document — the project's own overview page
     * for whichever document is $overview, /{project}/{document} for every
     * other one. Rendering the overview at both /{project} and its own
     * /{project}/index (or /about) would give the same content two URLs;
     * this keeps every generated link pointed at the one that's actually
     * meant to be shared/indexed.
     */
    public static function pathFor(Project $project, Document $document, ?Document $overview, ?string $version = null): string
    {
        if ($overview && $document->is($overview)) {
            return self::projectPath($project, $version);
        }

        return route('document', array_filter([
            'project' => $project->slug,
            'document' => $document->slug,
            'version' => $version,
        ], fn ($value) => $value !== null), absolute: false);
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
        return self::groups($documents)->flatMap(fn (Collection $group): Collection => $group)->values();
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
