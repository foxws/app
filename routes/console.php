<?php

declare(strict_types=1);

use Foxws\Docs\Console\Commands\SyncDocsCommand;
use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Support\Facades\Schedule;
use Modules\Marketing\Commands\SyncPackagistDownloadsCommand;

Schedule::command(PruneStaleTagsCommand::class)
    ->dailyAt('03:00');

Schedule::command(ClearResetsCommand::class)
    ->dailyAt('03:00');

Schedule::command(SyncPackagistDownloadsCommand::class)
    ->dailyAt('03:00');

Schedule::command(SyncDocsCommand::class)
    ->dailyAt('03:00')
    ->withoutOverlapping();
