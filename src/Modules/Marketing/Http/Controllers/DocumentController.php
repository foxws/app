<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\DocumentDetailProp;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DocumentController
{
    public function __invoke(Project $project, string $document): Response|RedirectResponse
    {
        $project->load('versions');

        $version = $project->defaultVersion();

        $documents = $version?->orderedDocuments() ?? collect();

        $current = Document::firstBySlug($documents, $document);

        if (! $current) {
            throw new NotFoundHttpException;
        }

        $overview = $project->indexDocument($documents);

        // The overview already lives at /{project} — its own /{project}/index
        // (or /about) is never linked to, but redirect a direct visit anyway
        // rather than serving the same content twice at two URLs.
        if ($overview && $current->is($overview)) {
            return redirect()->route('project', $project->slug);
        }

        return Inertia::render('DocumentView', [
            'document' => fn () => new DocumentDetailProp($project, $current, $documents),
            'crumbs' => fn (): array => [
                ['label' => $project->slug, 'href' => route('project', $project->slug, absolute: false)],
                ['label' => $current->slug],
            ],
            'scope' => fn (): string => $project->title,
        ]);
    }
}
