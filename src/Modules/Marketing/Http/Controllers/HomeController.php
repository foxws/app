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
                    ->orderBy('name')
                    ->get()
            ),
        ]);
    }
}
