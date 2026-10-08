<?php

declare(strict_types=1);

use Foxws\Docs\Console\Commands\SyncDocsCommand;
use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Support\Facades\Schedule;
use Modules\Marketing\Commands\SyncGitHubReadmesCommand;
use Modules\Marketing\Commands\SyncPackagistDownloadsCommand;

Schedule::command(PruneStaleTagsCommand::class)
    ->dailyAt('01:30')
    ->runInBackground();

Schedule::command(ClearResetsCommand::class)
    ->dailyAt('02:00')
    ->runInBackground();

Schedule::command(SyncDocsCommand::class)
    ->dailyAt('03:00')
    ->runInBackground();

Schedule::command(SyncPackagistDownloadsCommand::class)
    ->dailyAt('03:30')
    ->runInBackground();

Schedule::command(SyncGitHubReadmesCommand::class)
    ->dailyAt('03:45')
    ->runInBackground();
