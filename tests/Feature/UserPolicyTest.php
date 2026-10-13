<?php

declare(strict_types=1);

use Database\Factories\UserFactory;

test('a user may view their own account', function () {
    $user = UserFactory::new()->create();

    expect($user->can('view', $user))->toBeTrue();
});

test('a user may not view another account', function () {
    $user = UserFactory::new()->create();

    expect($user->can('view', UserFactory::new()->create()))->toBeFalse();
});
