<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

final class TermsController
{
    public function __invoke(): Response
    {
        return Inertia::render('TermsPage');
    }
}
