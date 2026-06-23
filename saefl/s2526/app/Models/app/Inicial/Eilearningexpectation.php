<?php

namespace App\Models\app\Inicial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eilearningexpectation extends Model
{
    use HasFactory;

    const COLUMN_COMMENTS = [
        'eilearningarea_id' => 'Área de aprendizaje',
        'description' => 'Descripción del aprendizaje esperado',
        'observations' => 'Observaciones'
    ];

    protected $table = 'eilearningexpectations';

    protected $fillable = [
        'eilearningarea_id',
        'description',
        'observations'
    ];

    protected $casts = [
        'eilearningarea_id' => 'integer',
    ];

    // 🔁 Relaciones
    /**
     * Obtiene el área de aprendizaje a la que pertenece esta expectativa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(Eilearningarea::class, 'eilearningarea_id');
    }

    /**
     * Obtiene los informes finales (Eifinalk) relacionados con esta expectativa
     * Incluye información adicional del pivot: eilearningarea_id y pevaluacion_id
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function eifinalks()
    {
        return $this->belongsToMany(Eifinalk::class, 'eifinalk_expectation')
            ->withPivot('eilearningarea_id', 'pevaluacion_id')
            ->withTimestamps();
    }

    // 🔍 Scopes
    /**
     * Filtra las expectativas por área de aprendizaje
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $areaId ID del área de aprendizaje
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByArea($query, $areaId)
    {
        return $query->where('eilearningarea_id', $areaId);
    }

    /**
     * Busca expectativas por texto en la descripción u observaciones
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search Texto a buscar
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('description', 'like', "%{$search}%")
            ->orWhere('observations', 'like', "%{$search}%");
    }

    /**
     * Filtra las expectativas que tienen observaciones
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithObservations($query)
    {
        return $query->whereNotNull('observations');
    }

    /**
     * Filtra las expectativas por grado a través de la relación con el área
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $gradoId ID del grado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByGrado($query, $gradoId)
    {
        return $query->whereHas('area', function ($q) use ($gradoId) {
            $q->where('grado_id', $gradoId);
        });
    }

    // 📝 Accessors & Mutators
    /**
     * Obtiene el nombre completo de la expectativa incluyendo el área
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->description} - {$this->area->name}";
    }

    /**
     * Obtiene el número total de informes finales relacionados
     * @return int
     */
    public function getEifinalksCountAttribute()
    {
        return $this->eifinalks()->count();
    }

    /**
     * Verifica si la expectativa tiene observaciones
     * @return bool
     */
    public function getHasObservationsAttribute()
    {
        return !empty($this->observations);
    }

    // 🔄 Métodos de utilidad
    /**
     * Verifica si la expectativa tiene informes finales relacionados
     * @return bool
     */
    public function hasEifinalks()
    {
        return $this->eifinalks()->exists();
    }

    /**
     * Obtiene los informes finales relacionados con un período de evaluación específico
     * @param int $pevaluacionId ID del período de evaluación
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEifinalksByPevaluacion($pevaluacionId)
    {
        return $this->eifinalks()
            ->wherePivot('pevaluacion_id', $pevaluacionId)
            ->get();
    }

    /**
     * Obtiene las áreas de aprendizaje relacionadas con esta expectativa
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedAreas()
    {
        return EiLearningarea::whereHas('expectations', function ($query) {
            $query->where('id', $this->id);
        })->get();
    }

    /**
     * Obtiene los informes finales activos (que tienen evaluación)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveEifinalks()
    {
        return $this->eifinalks()
            ->whereHas('pevaluacion')
            ->get();
    }

    /**
     * Obtiene el grado a través de la relación con el área
     * @return \App\Models\app\Pescolar\Grado
     */
    public function getGradoAttribute()
    {
        return $this->area->grado;
    }
}
