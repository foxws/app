<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\ProjectSummaryProp;
use Modules\Marketing\Http\Props\SideProjectSummaryProp;

final class HomeController
{
    public function __invoke(Request $request): Response
    {
        $projects = Project::with('versions')
            ->withMax('versions', 'last_synced_at')
            ->orderBy('title')
            ->get();

        return Inertia::render('HomePage', [
            'packages' => Inertia::once(fn () => new ProjectSummaryProp($projects)),
            'sideProjects' => Inertia::once(fn () => new SideProjectSummaryProp($projects)),
        ]);
    }
}
