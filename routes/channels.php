<?php

declare(strict_types=1);

use App\Api\Media\Broadcasting\MediaChannel;
use App\Api\Users\Broadcasting\UserChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('users.{user}', UserChannel::class);
Broadcast::channel('media.{media}', MediaChannel::class);
