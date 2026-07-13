<?php

namespace Modules\Notification\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasPublicId;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends BaseModel
{
    use HasPublicId;

    protected $fillable = [
        'type',
        'title',
        'message',
        'level',
        'notifiable_type',
        'notifiable_id',
        'causer_type',
        'causer_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /** @return MorphTo<Model, $this> */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return MorphTo<Model, $this> */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param  Builder<Notification>  $query
     * @return Builder<Notification>
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * @param  Builder<Notification>  $query
     * @return Builder<Notification>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $q) use ($userId) {
            $q->whereNull('notifiable_id')
                ->orWhere(function (Builder $q2) use ($userId) {
                    $q2->where('notifiable_type', User::class)
                        ->where('notifiable_id', $userId);
                });
        });
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }
}
