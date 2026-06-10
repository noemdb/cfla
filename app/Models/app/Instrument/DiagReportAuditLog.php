<?php

namespace App\Models\app\Instrument;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(User::class, 'user_id');
    }
}
