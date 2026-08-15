<?php

namespace App\Models\app\Instrument;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DiagReportAuditLog extends Model implements \App\Contracts\Auditable
{
    protected $table = 'diag_report_audit_logs';

    protected $fillable = [
        'report_id',
        'user_id',
        'action',
        'details',
        'ip_address',
        'user_agent',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * details (texto libre) excluido por volumen.
     */
    public function auditableAttributes(): array
    {
        return ['id', 'report_id', 'user_id', 'action', 'ip_address', 'user_agent'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
