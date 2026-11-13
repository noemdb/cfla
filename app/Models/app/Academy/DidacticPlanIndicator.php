<?php

namespace App\Models\app\Academy;

use App\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DidacticPlanIndicator extends Model implements Auditable
{
    use HasFactory;

    protected $table = 'didactic_plan_indicators';

    protected $fillable = [
        'didactic_plan_id', 'didactic_plan_session_id',
        'name', 'weighting', 'is_quantitative', 'origin',
        'source_achievement_id', 'sort_order',
    ];

    protected $casts = [
        'weighting' => 'decimal:0',
        'is_quantitative' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function auditableAttributes(): array
    {
        return [
            'id', 'didactic_plan_id', 'didactic_plan_session_id', 'name',
            'weighting', 'is_quantitative', 'origin', 'source_achievement_id',
            'sort_order',
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

    public function session()
    {
        return $this->belongsTo(DidacticPlanSession::class, 'didactic_plan_session_id');
    }

    public function sourceAchievement()
    {
        return $this->belongsTo(Achievement::class, 'source_achievement_id');
    }
}
