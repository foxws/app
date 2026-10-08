<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Domain\Projects\Models\Project;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\PackageGroupsProp;
use Modules\Marketing\Http\Props\ProjectSummaryProp;

final class HomeController
{
    public function __invoke(): Response
    {
        return Inertia::render('HomePage', [
            'packageGroups' => Inertia::once(fn () => new PackageGroupsProp($this->projectsByKind()->get('packages', collect()))),
            'projects' => Inertia::once(fn () => new ProjectSummaryProp($this->projectsByKind()->get('projects', collect()))),
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
            ->orderBy('title')
            ->get()
            ->groupBy(fn (Project $project): string => $project->isPackage() ? 'packages' : 'projects'));
    }
}
