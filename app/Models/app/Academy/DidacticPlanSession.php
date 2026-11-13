<?php

namespace App\Models\app\Academy;

use App\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DidacticPlanSession extends Model implements Auditable
{
    use HasFactory;

    protected $table = 'didactic_plan_sessions';

    protected $fillable = [
        'didactic_plan_id', 'date', 'day_index', 'sort_order',
        'title', 'thematic', 'objective',
        'start', 'development', 'close', 'resources',
        'evaluation_instrument', 'evaluation_description', 'observations', 'ods',
        'is_non_teaching',
        'source_activity_id', 'source_field', 'source_excerpt', 'source_checksum', 'confidence',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'day_index' => 'integer',
        'sort_order' => 'integer',
        'ods' => 'array',
        'is_non_teaching' => 'boolean',
        'confidence' => 'decimal:2',
    ];

    public function auditableAttributes(): array
    {
        return [
            'id', 'didactic_plan_id', 'date', 'day_index', 'sort_order', 'title',
            'thematic', 'objective', 'evaluation_instrument', 'is_non_teaching',
            'source_activity_id',
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

    public function indicators()
    {
        return $this->hasMany(DidacticPlanIndicator::class, 'didactic_plan_session_id')
            ->orderBy('sort_order');
    }

    public function sourceActivity()
    {
        return $this->belongsTo(Activity::class, 'source_activity_id');
    }
}
