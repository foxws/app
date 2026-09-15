<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
use Illuminate\Support\Collection;

/**
 * Builds the cross-reference map DocumentHeadings::extract() needs to
 * rewrite relative markdown filenames (e.g. `installation.md`) into each
 * document's real page, shared between the project overview and document
 * pages.
 */
final class DocumentLinks
{
    /**
     * @param  Collection<int, Document>  $documents
     * @return array<string, string>
     */
    public static function build(Project $project, Collection $documents): array
    {
        $overview = $project->indexDocument($documents);

        return $documents->flatMap(function (Document $document) use ($project, $overview) {
            $path = DocsNavigation::pathFor($project, $document, $overview);

            return [
                "{$document->slug}.md" => $path,
                $document->slug => $path,
                basename((string) $document->source_path) => $path,
            ];
        })->all();
    }
}
