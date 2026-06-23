<?php

namespace App\Observers;

use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagReportAuditLog;
use Illuminate\Support\Facades\Auth;

class DiagReportObserver
{
    /**
     * Handle the DiagReport "updated" event.
     */
    public function updated(DiagReport $report)
    {
        $this->logAction($report, 'updated', [
            'changes' => $report->getDirty(),
        ]);
    }

    /**
     * Handle custom "validated" event.
     * Call this manually: $report->fireModelEvent('validated');
     */
    public function validated(DiagReport $report)
    {
        $this->logAction($report, 'validated', [
            'validated_at' => $report->validated_at,
        ]);
    }

    /**
     * Handle custom "signed" event.
     */
    public function signed(DiagReport $report)
    {
        $this->logAction($report, 'signed', [
            'status' => $report->status,
        ]);
    }

    /**
     * Log an action to audit trail.
     */
    protected function logAction(DiagReport $report, string $action, array $details = [])
    {
        if (!Auth::check()) {
            return; // Don't log if no authenticated user
        }

        DiagReportAuditLog::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
