<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Core\Rules\NotInPasswordHistory;
use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => [...$this->passwordRules(), new NotInPasswordHistory($user)],
        ])->validate();

        $user->forceFill([
            'password' => $input['password'],
        ])->save();

        PasswordHistory::record($user->id, $user->password);
    }
}
