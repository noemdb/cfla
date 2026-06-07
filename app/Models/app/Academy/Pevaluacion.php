<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pevaluacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profesor_id', 'lapso_id', 'seccion_id', 'pensum_id', 'grupo_estable_id',
        'status_baremo', 'status_official', 'status_note_report', 'nota_type',
        'escala_id', 'objetivo', 'description', 'observations', 'category',
    ];

    protected $casts = [
        'status_official' => 'boolean',
        'status_note_report' => 'boolean',
    ];

    protected $table = 'pevaluacions';

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'lapso_id' => 'Momento',
        'seccion_id' => 'Sección',
        'pensum_id' => 'Área de Formación',
        'grupo_estable_id' => 'Grupo Estable',
        'status_baremo' => 'Baremo',
        'status_official' => 'En documentos oficiales',
        'nota_type' => 'Tipo de nota',
        'escala_id' => 'Escala',
        'objetivo' => 'Objetivo',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'category' => 'Category',
        'status_note_report' => 'En Informe de Notas',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function grado()
    {
        return $this->hasOneThrough(
            Grado::class,
            Seccion::class,
            'id',
            'id',
            'seccion_id',
            'grado_id'
        );
    }

    public function pestudio()
    {
        return $this->hasOneThrough(
            Pestudio::class,
            Pensum::class,
            'id',
            'id',
            'pensum_id',
            'pestudio_id'
        );
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'pevaluacion_id');
    }

    public function grupoEstable()
    {
        return $this->belongsTo(GrupoEstable::class, 'grupo_estable_id');
    }

    public function evaluacions()
    {
        return $this->hasMany(Evaluacion::class, 'pevaluacion_id');
    }

    public function escala()
    {
        return $this->belongsTo(Escala::class, 'escala_id');
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    public function scopeActive($query, $flag = true)
    {
        return $query->where('pevaluacions.status_official', $flag);
    }

    public function scopeWithPlanningModule($query)
    {
        return $query->whereHas('pensum.pestudio', function ($q) {
            $q->where('planning_module', true);
        });
    }

    // ─── ACCESSORS ───────────────────────────────────────────────

    public function getFullNameAttribute()
    {
        $asignatura = $this->pensum?->asignatura?->name ?? '?';
        $seccion = $this->seccion?->name ?? '?';
        $lapso = $this->lapso?->name ?? '?';
        return "{$asignatura} - {$seccion} ({$lapso})";
    }

    public function getIsLapsoClosedAttribute()
    {
        return $this->lapso && $this->lapso->ffinal
            ? now()->gt($this->lapso->ffinal)
            : false;
    }
}
