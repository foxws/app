<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Marketing\Http\Controllers\DocumentController;
use Modules\Marketing\Http\Controllers\HomeController;
use Modules\Marketing\Http\Controllers\ProjectController;
use Modules\Marketing\Http\Controllers\SitemapController;
use Modules\Marketing\Http\Controllers\TermsController;
use Spatie\ResponseCache\Middlewares\CacheResponse;

Route::middleware(CacheResponse::for(tags: 'marketing'))->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/terms', TermsController::class)->name('terms');
    Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
    Route::get('/{project}', ProjectController::class)->name('project');
    Route::get('/{project}/{document}', DocumentController::class)->name('document');
});
