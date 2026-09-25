<?php

declare(strict_types=1);

namespace Domain\Users\Collections;

use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * @template TKey of array-key
 * @template TModel of User
 *
 * @extends Collection<TKey, TModel>
 */
class UserCollection extends Collection
{
    //
}
