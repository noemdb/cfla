<?php

namespace App\Models\app\Inicial;

use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Informe final de Educación Inicial: el documento individualizado que el
 * docente entrega a cada estudiante por área y lapso.
 *
 * Estructura pedagógica (doc 06 §5): información general → contexto educativo
 * → desarrollo individual → conclusiones y proyección.
 *
 * ─────────────────────────────────────────────────────────────────
 * SUBNOMBRE: subsistema GREENFIELD
 * ─────────────────────────────────────────────────────────────────
 * En producción (`s2526`) esta tabla tiene 0 filas: nunca operó, porque el
 * catálogo de áreas/expectaciones jamás se sembró. En cfla se construye
 * completo, y su prerrequisito es `EILearningSeeder` (27 áreas + 135
 * expectativas para los grados 22/23/24).
 *
 * ─────────────────────────────────────────────────────────────────
 * DECISIÓN DE PORT — Item 10 del checklist del blueprint
 * ─────────────────────────────────────────────────────────────────
 * El legacy definía a la vez los MÉTODOS `profesor()/seccion()/lapso()/pensum()`
 * y los ACCESORS `getProfesorAttribute()/getSeccionAttribute()/…`, ambos con
 * cuerpo `$this->pevaluacion?->X`. En Laravel el accessor gana y sombrea la
 * relación, así que `Eifinalk::with(['seccion','lapso','pensum.grado'])` — que
 * el legacy sí hace en `EifinalkComponent:133` — lanzaba `LogicException: Call
 * to undefined relationship`.
 *
 * Aquí se portan SOLO los accesors. `pevaluacion` es la única relación real y
 * hay que precargarla con `pevaluacion.profesor`, `pevaluacion.lapso`,
 * `pevaluacion.seccion.grado`, etc. (no con `profesor` a secas) para evitar
 * N+1. Ver `Evaluacion\Inicial\EifinalkComponent` del legacy, que ya lo
 * hacía bien.
 *
 * @property int|null $order
 * @property int $pevaluacion_id
 * @property int $estudiant_id
 * @property string $title
 * @property string|null $context_group
 * @property string|null $planing_eject Nota: typo real en el DDL ("planing"), preservado.
 * @property string|null $featured_project
 * @property string|null $special_activities
 * @property string|null $achievements
 * @property string|null $individual_observations
 * @property string|null $specialist_observation
 * @property string|null $recommendations
 * @property string|null $expected_learnings
 * @property string|null $family_participation
 * @property string|null $conclusions
 */
class Eifinalk extends Model
{
    use HasFactory;

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

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
        'expected_learnings' => 'Aprendizajes Esperados',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    /**
     * Carga académica (área de aprendizaje) sobre la que se emite el informe.
     * Es el ancla de todo: de aquí se derivan profesor, sección, lapso y pensum.
     */
    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');
    }

    public function expectant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    /**
     * Expectativas de aprendizaje vinculadas al informe, vía la tabla pivote
     * `eifinalk_expectation`.
     *
     * El pivote desnormaliza `eilearningarea_id` y `pevaluacion_id` para poder
     * filtrar el boletín por área y por período sin joins extra.
     */
    public function expectations()
    {
        return $this->belongsToMany(Eilearningexpectation::class, 'eifinalk_expectation')
            ->withPivot('eilearningarea_id', 'pevaluacion_id')
            ->withTimestamps();
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    /**
     * Informes de un lapso y sección dados (se resuelve vía `pevaluacion`).
     */
    public function scopeByLapsoYSeccion($query, $lapso_id, $seccion_id)
    {
        return $query->whereHas('pevaluacion', function ($q) use ($lapso_id, $seccion_id) {
            $q->where('lapso_id', $lapso_id)
                ->where('seccion_id', $seccion_id);
        });
    }

    /**
     * Informes de un profesor dado (se resuelve vía `pevaluacion`).
     */
    public function scopeByProfesor($query, $profesor_id)
    {
        return $query->whereHas('pevaluacion', function ($q) use ($profesor_id) {
            $q->where('profesor_id', $profesor_id);
        });
    }

    // ─── ACCESORS (ver "DECISIÓN DE PORT" en el docblock) ─────────

    /**
     * @return \App\Models\app\Academy\Profesor|null
     */
    public function getProfesorAttribute()
    {
        return $this->pevaluacion?->profesor;
    }

    /**
     * @return \App\Models\app\Academy\Seccion|null
     */
    public function getSeccionAttribute()
    {
        return $this->pevaluacion?->seccion;
    }

    /**
     * @return \App\Models\app\Academy\Lapso|null
     */
    public function getLapsoAttribute()
    {
        return $this->pevaluacion?->lapso;
    }

    /**
     * @return \App\Models\app\Academy\Pensum|null
     */
    public function getPensumAttribute()
    {
        return $this->pevaluacion?->pensum;
    }

    /**
     * Título recortado para listados.
     */
    public function getResumenTituloAttribute()
    {
        return Str::limit($this->title, 40);
    }
}
