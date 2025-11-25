<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogger
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $properties = null
    ): void {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a login event
     */
    public static function logLogin(): void
    {
        self::log(
            'login',
            auth()->user()->name . ' logged in',
            null,
            null,
            null
        );
    }

    /**
     * Log a logout event
     */
    public static function logLogout(): void
    {
        self::log(
            'logout',
            auth()->user()->name . ' logged out',
            null,
            null,
            null
        );
    }

    /**
     * Log a create event
     */
    public static function logCreate(string $modelType, int $modelId, string $modelName): void
    {
        self::log(
            'created',
            auth()->user()->name . ' created ' . class_basename($modelType) . ': ' . $modelName,
            $modelType,
            $modelId,
            null
        );
    }

    /**
     * Log an update event
     */
    public static function logUpdate(
        string $modelType,
        int $modelId,
        string $modelName,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        self::log(
            'updated',
            auth()->user()->name . ' updated ' . class_basename($modelType) . ': ' . $modelName,
            $modelType,
            $modelId,
            [
                'old' => $oldValues,
                'new' => $newValues,
            ]
        );
    }

    /**
     * Log a delete event
     */
    public static function logDelete(string $modelType, int $modelId, string $modelName): void
    {
        self::log(
            'deleted',
            auth()->user()->name . ' deleted ' . class_basename($modelType) . ': ' . $modelName,
            $modelType,
            $modelId,
            null
        );
    }
}
