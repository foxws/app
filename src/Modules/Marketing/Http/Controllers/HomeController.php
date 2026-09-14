<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Home', [
            'packages' => array_values(config('packages')),
        ]);
    }
}
