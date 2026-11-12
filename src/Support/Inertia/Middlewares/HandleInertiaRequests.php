<?php

declare(strict_types=1);

namespace Support\Inertia\Middlewares;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'nonce' => fn (): string => app('csp-nonce'),
        ]);
    }

    /**
     * @see https://inertiajs.com/docs/v3/data-props/shared-data#sharing-once-props
     *
     * @return array<string, mixed>
     */
    public function shareOnce(Request $request): array
    {
        return array_merge(parent::shareOnce($request), [
            'app' => fn (): string => Config::string('app.name', 'Laravel'),
        ]);
    }
}
