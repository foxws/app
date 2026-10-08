<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Domain\Projects\Models\Project;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\ProjectShowcaseProp;
use Modules\Marketing\Http\Props\SideProjectSummaryProp;

final class ProjectShowcaseController
{
    public function __invoke(Project $project): Response
    {
        abort_if($project->isPackage(), 404);

        $project->loadExists('documents');

        return Inertia::render('ProjectShowcase', [
            'project' => fn () => new ProjectShowcaseProp($project),
            'moreProjects' => fn () => new SideProjectSummaryProp(Project::orderBy('title')
                ->get()
                ->reject(fn (Project $other): bool => $other->isPackage() || $other->is($project))
                ->values()),
            'crumbs' => fn (): array => [
                ['label' => 'projects', 'href' => route('home', absolute: false).'#projects'],
                ['label' => $project->slug],
            ],
        ]);
    }
}
