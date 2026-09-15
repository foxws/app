<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\ProjectDetailProp;

final class ProjectController
{
    public function __invoke(Request $request, Project $project): Response
    {
        $project->load('versions');

        return Inertia::render('ProjectView', [
            'project' => fn () => new ProjectDetailProp($project),
            'crumbs' => fn (): array => [['label' => $project->slug]],
            'scope' => fn (): string => $project->title,
        ]);
    }
}
