<?php

namespace App\Models\app\Academy;

use App\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DidacticPlan extends Model implements Auditable
{
    use HasFactory, SoftDeletes;

    protected $table = 'didactic_plans';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_SUPERSEDED = 'superseded';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'pestudio_id', 'grado_id', 'seccion_id', 'lapso_id',
        'period_start', 'period_end',
        'axis', 'project_title', 'objective', 'ods',
        'status', 'version', 'source_mode',
        'filter_snapshot', 'filter_hash', 'universe_hash',
        'filtered_count', 'selected_count',
        'notes', 'pdf_notes', 'checksum', 'lock_version', 'approved_key',
        'created_by', 'updated_by', 'approved_by', 'approved_at', 'superseded_by_id',
    ];

    protected $casts = [
        'period_start' => 'date:Y-m-d',
        'period_end' => 'date:Y-m-d',
        'ods' => 'array',
        'filter_snapshot' => 'array',
        'version' => 'integer',
        'filtered_count' => 'integer',
        'selected_count' => 'integer',
        'lock_version' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function auditableAttributes(): array
    {
        return [
            'id', 'pestudio_id', 'grado_id', 'seccion_id', 'lapso_id',
            'period_start', 'period_end', 'axis', 'project_title', 'status',
            'version', 'source_mode', 'filter_hash', 'universe_hash',
            'filtered_count', 'selected_count', 'approved_by', 'approved_at',
            'superseded_by_id',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function sessions()
    {
        return $this->hasMany(DidacticPlanSession::class, 'didactic_plan_id')
            ->orderBy('date')
            ->orderBy('sort_order');
    }

    public function indicators()
    {
        return $this->hasMany(DidacticPlanIndicator::class, 'didactic_plan_id')
            ->orderBy('sort_order');
    }

    public function sources()
    {
        return $this->hasMany(DidacticPlanSource::class, 'didactic_plan_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function supersededBy()
    {
        return $this->belongsTo(self::class, 'superseded_by_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isDownloadable(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_SUPERSEDED], true);
    }

    public function isEditable(): bool
    {
        return $this->isDraft();
    }

    public static function approvedKeyFor(int $seccionId, ?int $lapsoId, string $periodStart, string $periodEnd): string
    {
        return implode(':', [$seccionId, $lapsoId ?? 'null', $periodStart, $periodEnd]);
    }
}
