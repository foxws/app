<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Project;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\ProjectSummaryProp;
use Modules\Marketing\Http\Props\SideProjectSummaryProp;
use Modules\Marketing\Support\ProjectKind;

final class HomeController
{
    public function __invoke(): Response
    {
        return Inertia::render('HomePage', [
            'packages' => Inertia::once(fn () => new ProjectSummaryProp($this->projectsByKind()->get('packages', collect()))),
            'sideProjects' => Inertia::once(fn () => new SideProjectSummaryProp($this->projectsByKind()->get('sideProjects', collect()))),
        ]);
    }

    /**
     * Memoized so both props share one query, and only run it when Inertia
     * actually resolves one of them.
     *
     * @return Collection<array-key, EloquentCollection<int, Project>>
     */
    private function projectsByKind(): Collection
    {
        return once(fn (): Collection => Project::with('versions')
            ->withExists('documents')
            ->orderBy('title')
            ->get()
            ->groupBy(fn (Project $project): string => ProjectKind::isPackage($project) ? 'packages' : 'sideProjects'));
    }
}
