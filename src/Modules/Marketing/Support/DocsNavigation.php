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
        return $documents
            ->groupBy(fn ($document) => $document->section ?: 'Docs')
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
}
