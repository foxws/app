<?php

declare(strict_types=1);

use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(PruneStaleTagsCommand::class)
    ->dailyAt('01:30')
    ->runInBackground();

Schedule::command(ClearResetsCommand::class)
    ->dailyAt('02:00')
    ->runInBackground();
