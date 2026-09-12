<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::name('api.')->prefix('v1')->group(function () {
    // Authentication
    // Route::get('/', HomeController::class)->name('home');

    // Tags
    // Route::apiResource('tags', TagController::class)->only('index');
});
