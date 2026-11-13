<?php

namespace App\Models\app\Academy;

use App\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DidacticPlanSource extends Model implements Auditable
{
    use HasFactory;

    protected $table = 'didactic_plan_sources';

    protected $fillable = [
        'didactic_plan_id', 'activity_id', 'pevaluacion_id',
        'activity_checksum', 'fields_used', 'excerpt', 'included',
    ];

    protected $casts = [
        'fields_used' => 'array',
        'included' => 'boolean',
    ];

    public function auditableAttributes(): array
    {
        return [
            'id', 'didactic_plan_id', 'activity_id', 'pevaluacion_id',
            'activity_checksum', 'included',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    public function plan()
    {
        return $this->belongsTo(DidacticPlan::class, 'didactic_plan_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');
    }
}
