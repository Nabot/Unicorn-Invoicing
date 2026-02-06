<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * Log an action.
     *
     * @param  array<string, mixed>|null  $metadata
     */
    public function log(
        ?int $actorId,
        string $action,
        Model $entity,
        ?array $metadata = null
    ): AuditLog {
        return AuditLog::create([
            'actor_id' => $actorId,
            'action' => $action,
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
            'metadata' => $metadata,
        ]);
    }
}
