<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Marketing\Http\Controllers\HomeController;
use Modules\Marketing\Http\Controllers\ProjectController;

Route::get('/', HomeController::class)->name('home');
Route::get('/{project}', ProjectController::class)->name('project');
