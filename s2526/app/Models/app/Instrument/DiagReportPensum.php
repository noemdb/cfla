<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use App\Models\app\Pescolar\Pensum;

class DiagReportPensum extends Model
{
    protected $table = 'diag_report_pensums';

    protected $fillable = [
        'report_id',
        'pensum_id',
        'total_answered_questions',
        'precision',
        'open_ended_level',
    ];

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }
}
