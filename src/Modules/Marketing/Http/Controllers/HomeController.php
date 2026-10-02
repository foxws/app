<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Project;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\PackageGroupsProp;
use Modules\Marketing\Http\Props\SideProjectSummaryProp;
use Modules\Marketing\Support\ProjectKind;
use Modules\Marketing\Support\StructuredData;

final class HomeController
{
    public function __invoke(): Response
    {
        return Inertia::render('HomePage', [
            'packageGroups' => Inertia::once(fn () => new PackageGroupsProp($this->projectsByKind()->get('packages', collect()))),
            'sideProjects' => Inertia::once(fn () => new SideProjectSummaryProp($this->projectsByKind()->get('sideProjects', collect()))),
        ])->withViewData('structuredData', StructuredData::home());
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
