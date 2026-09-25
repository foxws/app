<?php

declare(strict_types=1);

use Foundation\Providers\AppServiceProvider;
use Foundation\Providers\AuthServiceProvider;
use Foundation\Providers\FortifyServiceProvider;
use Foundation\Providers\InertiaServiceProvider;
use Foundation\Providers\RouteServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    FortifyServiceProvider::class,
    InertiaServiceProvider::class,
    RouteServiceProvider::class,
];
