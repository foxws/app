<?php

declare(strict_types=1);

use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Support\Facades\Schedule;
use Laravel\Horizon\Console\SnapshotCommand;
use Laravel\Sanctum\Console\Commands\PruneExpired;

Schedule::command(PruneStaleTagsCommand::class)
    ->dailyAt('01:30')
    ->runInBackground();

Schedule::command(ClearResetsCommand::class)
    ->dailyAt('02:00')
    ->runInBackground();

Schedule::command(SnapshotCommand::class)
    ->dailyAt('02:30')
    ->runInBackground();

Schedule::command(PruneExpired::class, ['--hours=24'])
    ->withoutOverlapping()
    ->dailyAt('02:30')
    ->runInBackground();
