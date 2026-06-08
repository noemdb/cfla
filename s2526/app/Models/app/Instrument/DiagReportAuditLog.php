<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use App\User; // Assuming User model is here, or standard App\Models\User. Checking logs.

class DiagReportAuditLog extends Model
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

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function user()
    {
        // Try standard location first, user will adjust if namespace is different
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
