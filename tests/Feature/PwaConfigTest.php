<?php

declare(strict_types=1);

use Foxws\Pwa\Pwa;
use Illuminate\Support\Env;

afterEach(function () {
    Env::getRepository()->clear('PWA_IGNORE_PATHS');
});

test('reads the ignored paths as a comma-separated list', function () {
    Env::getRepository()->set('PWA_IGNORE_PATHS', '/api/,/admin/');

    config(['pwa' => require config_path('pwa.php')]);

    expect(Pwa::ignorePaths())->toBe('"/api/", "/admin/"');
});
