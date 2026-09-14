<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\ProjectDetailProp;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProjectController
{
    public function __invoke(Request $request, string $project): Response
    {
        $model = Project::with('versions')->where('slug', $project)->first();

        if (! $model) {
            throw new NotFoundHttpException;
        }

        return Inertia::render('Project', [
            'project' => new ProjectDetailProp($model),
            'crumbs' => [['label' => $model->slug]],
            'scope' => $model->title,
        ]);
    }
}
