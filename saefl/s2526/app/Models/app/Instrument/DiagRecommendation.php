<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use App\Models\app\Pescolar\Pensum;

class DiagRecommendation extends Model
{
    protected $table = 'diag_recommendations';

    protected $fillable = [
        'report_id',
        'pensum_id',
        'type',
        'recommendation',
        'priority',
        'suggested_frequency',
        'active',
        'assigned_to',
        'started_at',
        'completed_at',
    ];

    protected $dates = [
        'started_at',
        'completed_at',
    ];

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(\App\User::class, 'assigned_to');
    }
}
