<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;

class DiagResult extends Model
{
    protected $table = 'diag_results';

    protected $fillable = [
        'report_id',
        'total_answered_questions',
        'precision',
        'open_ended_response_level',
    ];

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }
}
