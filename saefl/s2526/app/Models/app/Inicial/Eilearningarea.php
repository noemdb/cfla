<?php

namespace App\Models\app\Inicial;

use App\Models\app\Pescolar\Grado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eilearningarea extends Model
{
    use HasFactory;

    protected $table = 'eilearningareas';

    const COLUMN_COMMENTS = [
        'grado_id' => 'Grupo de edad: Grupo 1, 2, 3',
        'name' => 'Nombre del área de aprendizaje',
        'description' => 'Descripción del área aprendizaje'
    ];

    protected $fillable = [
        'grado_id',
        'name',
        'description'
    ];

    protected $casts = [
        'grado_id' => 'integer',
    ];

    // 🔁 Relaciones
    /**
     * Obtiene el grado al que pertenece esta área de aprendizaje
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    /**
     * Obtiene las expectativas de aprendizaje asociadas a esta área
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expectations()
    {
        return $this->hasMany(Eilearningexpectation::class, 'eilearningarea_id');
    }

    // 🔍 Scopes
    /**
     * Filtra las áreas por grado
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $gradoId ID del grado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }

    /**
     * Busca áreas por texto en el nombre o descripción
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search Texto a buscar
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Filtra las áreas que tienen expectativas asociadas
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereHas('expectations');
    }

    // 📝 Accessors & Mutators
    /**
     * Obtiene el nombre completo del área incluyendo el grado
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->name} - Grupo {$this->grado->name}";
    }

    /**
     * Obtiene el número total de expectativas asociadas
     * @return int
     */
    public function getExpectationsCountAttribute()
    {
        return $this->expectations()->count();
    }

    // 🔄 Métodos de utilidad
    /**
     * Verifica si el área tiene expectativas asociadas
     * @return bool
     */
    public function hasExpectations()
    {
        return $this->expectations()->exists();
    }

    /**
     * Obtiene las expectativas filtradas por grado
     * @param int $gradoId ID del grado
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpectationsByGrado($gradoId)
    {
        return $this->expectations()
            ->whereHas('area', function ($query) use ($gradoId) {
                $query->where('grado_id', $gradoId);
            })
            ->get();
    }

    /**
     * Obtiene las expectativas que tienen descripción
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveExpectations()
    {
        return $this->expectations()
            ->whereNotNull('description')
            ->get();
    }

    /**
     * Obtiene los informes finales relacionados con esta área
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedEifinalks()
    {
        return Eifinalk::whereHas('expectations', function ($query) {
            $query->where('eilearningarea_id', $this->id);
        })->get();
    }
}
