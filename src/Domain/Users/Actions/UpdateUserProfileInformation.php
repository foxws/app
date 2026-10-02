<?php

declare(strict_types=1);

namespace Domain\Users\Actions;

use Domain\Users\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user)],
        ])->validateWithBag('updateProfileInformation');

        if ($validated['email'] !== $user->email) {
            $this->updateVerifiedUser($user, $validated);

            return;
        }

        $user->forceFill($validated)->save();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function updateVerifiedUser(User $user, array $validated): void
    {
        $user->forceFill([
            ...$validated,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
