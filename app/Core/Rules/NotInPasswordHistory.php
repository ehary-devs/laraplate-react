<?php

namespace App\Core\Rules;

use App\Models\PasswordHistory;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class NotInPasswordHistory implements ValidationRule
{
    public function __construct(private readonly User $user) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $keepCount = config('security.password.history_count', 3);

        $histories = PasswordHistory::where('user_id', $this->user->id)
            ->latest('created_at')
            ->limit($keepCount)
            ->get();

        foreach ($histories as $history) {
            if (Hash::check($value, $history->password)) {
                $fail("Password tidak boleh sama dengan {$keepCount} password terakhir.");

                return;
            }
        }
    }
}
