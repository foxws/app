<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProjectController
{
    public function __invoke(Request $request, string $project): Response
    {
        $data = config("packages.{$project}");

        if (! $data) {
            throw new NotFoundHttpException;
        }

        return Inertia::render('Project', [
            'project' => [...$data, 'key' => $project],
            'crumbs' => [$project],
            'scope' => $data['name'],
        ]);
    }
}
