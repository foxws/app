<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Marketing\Http\Props\ProjectSummaryProp;

final class HomeController
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('HomePage', [
            'packages' => fn () => new ProjectSummaryProp(
                Project::with('versions')
                    ->withMax('versions', 'last_synced_at')
                    // Postgres sorts NULLS FIRST on DESC by default — a
                    // project with no synced version yet should read as
                    // "never updated", not "just updated".
                    ->orderByRaw('versions_max_last_synced_at DESC NULLS LAST')
                    ->get()
            ),
        ]);
    }
}
