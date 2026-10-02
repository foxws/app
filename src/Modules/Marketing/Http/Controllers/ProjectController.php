<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Domain\Projects\Models\Project;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\ProjectDetailProp;
use Modules\Marketing\Http\Requests\ViewDocsRequest;

final class ProjectController
{
    public function __invoke(ViewDocsRequest $request, Project $project): Response
    {
        $project->load('versions');

        abort_unless($project->documents()->exists(), 404);

        return Inertia::render('ProjectView', [
            'project' => fn () => new ProjectDetailProp($project, $request->version()),
            'crumbs' => fn (): array => [['label' => $project->slug]],
            'scope' => fn (): string => $project->title,
        ]);
    }
}
