<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\DocumentDetailProp;
use Modules\Marketing\Http\Requests\ViewDocsRequest;
use Modules\Marketing\Support\DocsNavigation;
use Modules\Marketing\Support\StructuredData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DocumentController
{
    public function __invoke(ViewDocsRequest $request, Project $project, string $document): Response|RedirectResponse
    {
        $project->load('versions');

        $version = $project->versionOrDefault($request->version());

        $documents = $version?->orderedDocuments() ?? collect();

        $current = Document::firstBySlug($documents, $document);

        if (! $current) {
            throw new NotFoundHttpException;
        }

        $overview = $project->indexDocument($documents);

        // Only stamp generated links with ?version= when browsing something
        // other than the default — keeps the common case's URLs clean.
        $versionParam = $version && ! $version->is_default ? $version->name : null;

        // The overview already lives at /{project} — its own /{project}/index
        // (or /about) is never linked to, but redirect a direct visit anyway
        // rather than serving the same content twice at two URLs.
        if ($overview && $current->is($overview)) {
            return redirect()->to(DocsNavigation::projectPath($project, $versionParam));
        }

        return Inertia::render('DocumentView', [
            'document' => fn () => new DocumentDetailProp($project, $current, $documents, $versionParam),
            'crumbs' => fn (): array => [
                ['label' => $project->slug, 'href' => DocsNavigation::projectPath($project, $versionParam)],
                ['label' => $current->slug],
            ],
            'scope' => fn (): string => $project->title,
        ])->withViewData('structuredData', StructuredData::document($project, $current));
    }
}
