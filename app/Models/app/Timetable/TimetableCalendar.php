<?php

namespace App\Models\app\Timetable;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pescolar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TimetableCalendar extends Model
{
    use HasFactory;

    protected $table = 'timetable_calendars';

    protected $fillable = [
        'lapso_id', 'pescolar_id', 'pestudio_id', 'name', 'status', 'period_minutes',
        'max_subjects_per_period', 'strategy', 'version', 'quality_score', 'preview_payload', 'active_job_id',
    ];

    protected $casts = [
        'pestudio_id' => 'integer',
        'period_minutes' => 'integer',
        'max_subjects_per_period' => 'integer',
        'version' => 'integer',
        'quality_score' => 'decimal:2',
        'preview_payload' => 'array',
    ];

    protected $hidden = [
        'active_lapso_key',
        'active_pestudio_key',
    ];

    const STATUS_DRAFT = 'draft';

    const STATUS_GENERATING = 'generating';

    const STATUS_ACTIVE = 'active';

    const STATUS_ARCHIVED = 'archived';

    public const STRATEGY_OPTIMIZED = 'optimized';

    public const STRATEGY_LEGACY = 'legacy';

    public const DEFAULT_STRATEGY = self::STRATEGY_OPTIMIZED;

    public const STRATEGIES = [
        self::STRATEGY_OPTIMIZED,
        self::STRATEGY_LEGACY,
    ];

    protected $attributes = [
        'strategy' => self::DEFAULT_STRATEGY,
    ];

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function pescolar()
    {
        return $this->belongsTo(Pescolar::class, 'pescolar_id');
    }

    public function pestudio()
    {
        return $this->belongsTo(\App\Models\app\Academy\Pestudio::class, 'pestudio_id');
    }

    public function periods()
    {
        return $this->hasMany(TimetablePeriod::class, 'calendar_id');
    }

    public function lessons()
    {
        return $this->hasMany(TimetableLesson::class, 'calendar_id');
    }

    public function slots()
    {
        return $this->hasMany(TimetableSlot::class, 'calendar_id');
    }

    public function availabilities()
    {
        return $this->hasMany(TimetableTeacherAvailability::class, 'calendar_id');
    }

    public function versions()
    {
        return $this->hasMany(TimetableCalendarVersion::class, 'calendar_id');
    }

    public function changeLogs()
    {
        return $this->hasMany(TimetableChangeLog::class, 'calendar_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    public function scopeForLapso($query, $lapsoId)
    {
        return $query->where('lapso_id', $lapsoId);
    }

    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_ACTIVE]);
    }

    /**
     * PLAN-TIMETABLE-002 I-2 — Activo del plan de estudio.
     */
    public static function activeForLapso($lapsoId, ?int $pestudioId = null): ?self
    {
        return self::query()
            ->forLapso($lapsoId)
            ->when($pestudioId, fn ($query) => $query->where('pestudio_id', $pestudioId))
            ->active()
            ->first();
    }

    public static function activeForPestudio($pestudioId): ?self
    {
        return self::query()
            ->where('pestudio_id', $pestudioId)
            ->active()
            ->first();
    }

    /**
     * PLAN-TIMETABLE-002 §4.4 — Resuelve "el activo del lapso vigente".
     */
    public static function activeForCurrentLapso(): ?self
    {
        $lapso = Lapso::query()
            ->where('finicial', '<=', now())
            ->where('ffinal', '>=', now())
            ->orderBy('finicial', 'desc')
            ->first();

        if ($lapso) {
            return self::activeForLapso($lapso->id);
        }

        return self::query()->active()->latest('id')->first();
    }

    /**
     * PLAN-TIMETABLE-002 §4.2 — Activa este calendario y archiva el activo
     * anterior del mismo lapso (democión atómica, respeta I-2/I-4).
     */
    public function activate(): void
    {
        if (! $this->slots()->exists()) {
            throw new \DomainException('No se puede activar un calendario sin horario generado.');
        }

        DB::transaction(function () {
            TimetableCalendar::query()
                ->where('pestudio_id', $this->pestudio_id)
                ->where('id', '!=', $this->id)
                ->active()
                ->update(['status' => self::STATUS_ARCHIVED]);

            $this->update(['status' => self::STATUS_ACTIVE]);
        });
    }

    /**
     * PLAN-TIMETABLE-002 I-7 — Elimina solo borradores (cascada FK limpia
     * periods/lessons/availability/slots/conflicts).
     */
    public function deleteDraft(): bool
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return false;
        }

        return $this->delete();
    }
}
