<?php

namespace Modules\AuditLog\Services;

use App\Core\Services\BaseService;
use Modules\AuditLog\Models\AuditLog;

class AuditLogService extends BaseService
{
    /**
     * Record an immutable audit entry. Request metadata (ip, user agent, url)
     * is captured automatically when available.
     *
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<int, string>|null  $tags
     */
    public function record(
        string $event,
        ?string $auditableType = null,
        ?int $auditableId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
        ?array $tags = null,
    ): AuditLog {
        $request = request();

        return AuditLog::create([
            'user_id' => $userId ?? auth()->id(),
            'event' => $event,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'tags' => $tags,
        ]);
    }
}
