<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'password', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Save a new password to history, pruning to keep only the most recent N entries.
     */
    public static function record(int $userId, string $hashedPassword): void
    {
        $keepCount = config('security.password.history_count', 3);

        static::create([
            'user_id' => $userId,
            'password' => $hashedPassword,
            'created_at' => now(),
        ]);

        $idsToKeep = static::where('user_id', $userId)
            ->latest('created_at')
            ->limit($keepCount)
            ->pluck('id');

        static::where('user_id', $userId)
            ->whereNotIn('id', $idsToKeep)
            ->delete();
    }
}
