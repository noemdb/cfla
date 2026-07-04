<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use App\Models\app\Pescolar\Pensum;

class DiagReportIndicatorResult extends Model
{
    protected $table = 'diag_report_indicator_results';

    protected $fillable = [
        'report_id',
        'pensum_id',
        'indicator_id',
        'expected_level',
        'observed_level',
        'gap_value',
        'gap_label',
        'teacher_observation',
    ];

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function indicator()
    {
        return $this->belongsTo(DiagIndicator::class, 'indicator_id');
    }
}
