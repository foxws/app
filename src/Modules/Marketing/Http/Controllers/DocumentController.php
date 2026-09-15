<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\DocumentDetailProp;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DocumentController
{
    public function __invoke(Request $request, string $project, string $document): Response|RedirectResponse
    {
        $model = Project::with('versions')->where('slug', $project)->first();

        if (! $model) {
            throw new NotFoundHttpException;
        }

        $version = $model->defaultVersion();

        $documents = $version?->orderedDocuments() ?? collect();

        $current = $documents->first(fn ($d) => $d->slug === $document);

        if (! $current) {
            throw new NotFoundHttpException;
        }

        $overview = $model->indexDocument($documents);

        // The overview already lives at /{project} — its own /{project}/index
        // (or /about) is never linked to, but redirect a direct visit anyway
        // rather than serving the same content twice at two URLs.
        if ($overview && $current->is($overview)) {
            return redirect()->route('project', $model->slug);
        }

        return Inertia::render('Document', [
            'document' => fn () => new DocumentDetailProp($model, $current, $documents),
            'crumbs' => fn (): array => [
                ['label' => $model->slug, 'href' => route('project', $model->slug, absolute: false)],
                ['label' => $current->slug],
            ],
            'scope' => fn (): string => $model->title,
        ]);
    }
}
