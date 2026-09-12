<?php

declare(strict_types=1);

use App\Api\Authentication\Controllers\HomeController;
use App\Api\Playlists\Controllers\PlaylistManifestController;
use App\Api\Tags\Controllers\TagController;
use App\Api\Videos\Controllers\VideoSessionController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->prefix('v1')->group(function () {
    // Authentication
    Route::get('/', HomeController::class)->name('home');

    // Tags
    Route::apiResource('tags', TagController::class)->only('index');
});
