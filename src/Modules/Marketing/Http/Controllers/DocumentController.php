<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\DocumentDetailProp;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DocumentController
{
    public function __invoke(Request $request, string $project, string $document): Response
    {
        $model = Project::with('versions')->where('slug', $project)->first();

        if (! $model) {
            throw new NotFoundHttpException;
        }

        $version = $model->versions->firstWhere('is_default', true) ?? $model->versions->first();

        $documents = $version?->documents()->orderBy('order')->get() ?? collect();

        $current = $documents->first(fn ($d) => $d->slug === $document);

        if (! $current) {
            throw new NotFoundHttpException;
        }

        return Inertia::render('Document', [
            'document' => fn () => new DocumentDetailProp($model, $current, $documents),
            'crumbs' => fn (): array => [
                ['label' => $model->slug, 'href' => route('project', $model->slug)],
                ['label' => $current->slug],
            ],
            'scope' => fn (): string => $model->title,
        ]);
    }
}
