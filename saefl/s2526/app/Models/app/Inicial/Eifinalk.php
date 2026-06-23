<?php

namespace App\Models\app\Inicial;

use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Eifinalk extends Model
{
    use HasFactory;

    const COLUMN_COMMENTS = [
        'order' => 'Orden',
        'pevaluacion_id' => 'Plan de evaluación',
        'estudiant_id' => 'Estudiante',
        'title' => 'Título del informe',
        'context_group' => 'Apreciación del estudiante, características, necesidades',
        'planing_eject' => 'Resumen de la planificación ejecutada',
        'featured_project' => 'Descripción del proyecto más significativo',
        'special_activities' => 'Eventos especiales',
        'achievements' => 'Logros del estudiante',
        'individual_observations' => 'Observaciones socioafectivas',
        'specialist_observation' => 'Observación de los Especialistas',
        'family_participation' => 'Participación familiar',
        'conclusions' => 'Reflexión final del docente',
        'recommendations' => 'Sugerencias a la familia y equipo docente',
        'expected_learnings' => 'Aprendizajes Esperados'
    ];

    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'order',
        'pevaluacion_id',
        'title',
        'estudiant_id',
        'context_group',
        'planing_eject',
        'featured_project',
        'special_activities',
        'achievements',
        'individual_observations',
        'specialist_observation',
        'recommendations',
        'expected_learnings',
        'family_participation',
        'conclusions',
    ];

    /**
     * Casts para tipos nativos
     */
    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'estudiantes' => 'array', // Guardado como JSON
    ];

    /**
     * Relación con la evaluación del pensum (Pevaluacion)
     */
    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id', 'id');
    }

    /**
     * Relación con el estudiante
     */
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }

    public function expectations()
    {
        return $this->belongsToMany(Eilearningexpectation::class, 'eifinalk_expectation')
            ->withPivot('eilearningarea_id', 'pevaluacion_id')
            ->withTimestamps();
    }

    /**
     * Accede al profesor a través de la evaluación
     */
    public function profesor()
    {
        return $this->pevaluacion?->profesor;
    }

    /**
     * Accede a la sección a través de la evaluación
     */
    public function seccion()
    {
        return $this->pevaluacion?->seccion;
    }

    /**
     * Accede al lapso a través de la evaluación
     */
    public function lapso()
    {
        return $this->pevaluacion?->lapso;
    }

    /**
     * Accede al pensum a través de la evaluación
     */
    public function pensum()
    {
        return $this->pevaluacion?->pensum;
    }

    /**
     * Scope para filtrar por lapso y sección (desde Pevaluacion)
     */
    public function scopeByLapsoYSeccion($query, $lapso_id, $seccion_id)
    {
        return $query->whereHas('pevaluacion', function ($q) use ($lapso_id, $seccion_id) {
            $q->where('lapso_id', $lapso_id)
                ->where('seccion_id', $seccion_id);
        });
    }

    /**
     * Scope para filtrar por profesor
     */
    public function scopeByProfesor($query, $profesor_id)
    {
        return $query->whereHas('pevaluacion', function ($q) use ($profesor_id) {
            $q->where('profesor_id', $profesor_id);
        });
    }

    /**
     * Accesor para resumen del título
     */
    public function getResumenTituloAttribute()
    {
        return Str::limit($this->title, 40);
    }

    /**
     * Accede al profesor desde la evaluación.
     *
     * @return \App\Models\app\Profesor|null
     */
    public function getProfesorAttribute()
    {
        return $this->pevaluacion?->profesor;
    }

    /**
     * Accede a la sección desde la evaluación.
     *
     * @return \App\Models\app\Seccion|null
     */
    public function getSeccionAttribute()
    {
        return $this->pevaluacion?->seccion;
    }

    /**
     * Accede al lapso desde la evaluación.
     *
     * @return \App\Models\app\Lapso|null
     */
    public function getLapsoAttribute()
    {
        return $this->pevaluacion?->lapso;
    }

    /**
     * Accede al pensum desde la evaluación.
     *
     * @return \App\Models\app\Pensum|null
     */
    public function getPensumAttribute()
    {
        return $this->pevaluacion?->pensum;
    }


}
