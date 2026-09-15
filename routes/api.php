<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Marketing\Http\Controllers\SearchController;

Route::name('api.')->prefix('v1')->group(function () {
    // Search
    Route::get('search', SearchController::class)->name('search');
});
