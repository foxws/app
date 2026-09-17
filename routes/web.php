<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Marketing\Http\Controllers\DocumentController;
use Modules\Marketing\Http\Controllers\HomeController;
use Modules\Marketing\Http\Controllers\ProjectController;
use Modules\Marketing\Http\Controllers\TermsController;

Route::get('/', HomeController::class)->name('home');
Route::get('/terms', TermsController::class)->name('terms');
Route::get('/{project}', ProjectController::class)->name('project');
Route::get('/{project}/{document}', DocumentController::class)->name('document');
