<?php

namespace App\Models\app\Inicial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Expectativa (logro esperado) de aprendizaje de un área de Educación Inicial.
 *
 * Es la unidad que el docente marca en el informe final del estudiante
 * (pivote `eifinalk_expectation`). Lo siembra `EILearningSeeder`: 5
 * expectativas por área × 27 áreas = 135.
 *
 * @property int $eilearningarea_id
 * @property string $description
 * @property string|null $observations
 */
class Eilearningexpectation extends Model
{
    use HasFactory;

    protected $table = 'eilearningexpectations';

    protected $fillable = [
        'eilearningarea_id',
        'description',
        'observations',
    ];

    protected $casts = [
        'eilearningarea_id' => 'integer',
    ];

    const COLUMN_COMMENTS = [
        'eilearningarea_id' => 'Área de aprendizaje',
        'description' => 'Descripción del aprendizaje esperado',
        'observations' => 'Observaciones',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function area()
    {
        return $this->belongsTo(Eilearningarea::class, 'eilearningarea_id');
    }

    /**
     * Informes finales que han marcado esta expectativa, vía
     * `eifinalk_expectation`.
     */
    public function eifinalks()
    {
        return $this->belongsToMany(Eifinalk::class, 'eifinalk_expectation')
            ->withPivot('eilearningarea_id', 'pevaluacion_id')
            ->withTimestamps();
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    public function scopeByArea($query, $areaId)
    {
        return $query->where('eilearningarea_id', $areaId);
    }

    /**
     * Búsqueda por descripción u observaciones.
     *
     * Agrupado en closure: el legacy dejaba el `orWhere` suelto, lo que
     * anulaba cualquier `where` previo del scope llamador.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
                ->orWhere('observations', 'like', "%{$search}%");
        });
    }

    public function scopeWithObservations($query)
    {
        return $query->whereNotNull('observations');
    }

    /**
     * Expectativas de un grupo de edad, resuelto a través del área.
     */
    public function scopeByGrado($query, $gradoId)
    {
        return $query->whereHas('area', function ($q) use ($gradoId) {
            $q->where('grado_id', $gradoId);
        });
    }

    // ─── ACCESORS ────────────────────────────────────────────────

    /**
     * Descripción de la expectativa con el nombre de su área.
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->description} - {$this->area?->name}";
    }

    public function getEifinalksCountAttribute()
    {
        return $this->eifinalks()->count();
    }

    public function getHasObservationsAttribute()
    {
        return ! empty($this->observations);
    }

    /**
     * Grupo de edad (grado) al que pertenece la expectativa, vía su área.
     */
    public function getGradoAttribute()
    {
        return $this->area?->grado;
    }

    // ─── MÉTODOS ─────────────────────────────────────────────────

    public function hasEifinalks()
    {
        return $this->eifinalks()->exists();
    }

    /**
     * Informes finales que marcaron esta expectativa en una carga académica
     * concreta.
     *
     * @param  int  $pevaluacionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEifinalksByPevaluacion($pevaluacionId)
    {
        return $this->eifinalks()
            ->wherePivot('pevaluacion_id', $pevaluacionId)
            ->get();
    }

    /**
     * Informes finales con evaluación asociada.
     */
    public function getActiveEifinalks()
    {
        return $this->eifinalks()
            ->whereHas('pevaluacion')
            ->get();
    }
}
