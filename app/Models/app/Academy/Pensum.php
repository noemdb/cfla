<?php

namespace App\Models\app\Academy;

use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Instrument\DiagQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pensum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pestudio_id',
        'grado_id',
        'asignatura_id',
        'status_component',
        'status_active',
        'status_active_diagnostic',
        'observations',
    ];

    protected $casts = [
        'status_active' => 'boolean',
        'status_active_diagnostic' => 'boolean',
    ];

    protected $table = 'pensums';

    const COLUMN_COMMENTS = [
        'pestudio_id' => 'Plan Estudio',
        'grado_id' => 'Grado',
        'asignatura_id' => 'Asignatura',
        'status_component' => 'Contiene componentes de Formación?',
        'observations' => 'Observación',
        'status_active_diagnostic' => 'Activo para diagnostico',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function pevaluacions()
    {
        return $this->hasMany(Pevaluacion::class, 'pensum_id');
    }

    public function diagCompetencies()
    {
        return $this->hasMany(DiagCompetency::class, 'pensum_id');
    }

    public function diagQuestions()
    {
        return $this->hasMany(DiagQuestion::class, 'pensum_id');
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    public function scopeActive($query, $flag = true)
    {
        return $query->where('pensums.status_active', $flag);
    }

    // ─── ACCESSORS ───────────────────────────────────────────────

    public function getFullNameAttribute()
    {
        $gradoName = $this->grado?->name ?? '?';
        $asignaturaName = $this->asignatura?->code ?? '?';
        return "{$gradoName} - {$asignaturaName}";
    }
}
