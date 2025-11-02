<?php

namespace App\Traits;

use App\Models\SuperAdmin\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity to the audit log.
     */
    protected function logActivity(string $action, string $module): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
        ]);
    }
}
