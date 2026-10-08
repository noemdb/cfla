<?php

namespace App\Models\app\Inicial;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Proyecto de aula de Educación Inicial (cabecera).
 *
 * Eje articulador del proceso educativo del año/ciclo: a diferencia de las
 * planificaciones, aquí `finicial` y `ffinal` admiten NULL en el DDL (el
 * proyecto se puede crear y luego fechar).
 *
 * Tres sub-entidades:
 *  - {@see Eiprojectreview}  — la revisión del proyecto (temas de interés, qué
 *    saben los niños, qué necesitan, quién apoya).
 *  - {@see Eiprojectsummary} — el resumen por área de aprendizaje.
 *  - {@see Eiprojectkstrategy} — las estrategias en la rejilla día × momento.
 *
 * Nota: NO tiene columna `eiprojectk_id` (es la cabecera), ni columna
 * `lapso_id` (a diferencia de `eievaluationks`).
 *
 * @property int $profesor_id
 * @property int $grado_id
 * @property int $seccion_id
 * @property int|null $pensum_id
 * @property string|null $finicial
 * @property string|null $ffinal
 * @property int $tiempo_ejecucion
 * @property string|null $diagnostico
 * @property string|null $observacion
 */
class Eiprojectk extends Model
{
    use HasFactory;

    protected $fillable = [
        'profesor_id',
        'grado_id',
        'seccion_id',
        'pensum_id',
        'finicial',
        'ffinal',
        'tiempo_ejecucion',
        'diagnostico',
        'observacion',
    ];

    /**
     * `finicial` y `ffinal` son columnas `date` en el esquema. Sin estos casts
     * Eloquent las devuelve como CADENA y hay que envolver cada lectura en
     * `Carbon::parse(...)` —un rodeo que se pagaba en cada vista del módulo y
     * que hacía imposible ordenarlas o compararlas como fechas.
     */
    protected $casts = [
        'finicial' => 'date',
        'ffinal' => 'date',
    ];

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'grado_id' => 'Grado/Año',
        'seccion_id' => 'Sección',
        'pensum_id' => 'Área de aprendizaje',
        'finicial' => 'Inicio',
        'ffinal' => 'Culminación',
        'tiempo_ejecucion' => 'Cant.Semanas',
        'diagnostico' => 'Diagnóstico inicial',
        'observacion' => 'Observación',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function eiprojectreviews()
    {
        return $this->hasMany(Eiprojectreview::class, 'eiprojectk_id');
    }

    public function eiprojectsummaries()
    {
        return $this->hasMany(Eiprojectsummary::class, 'eiprojectk_id');
    }

    public function eiprojectkstrategies()
    {
        return $this->hasMany(Eiprojectkstrategy::class, 'eiprojectk_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    /**
     * Área de aprendizaje del grado (pensum: asignatura × grado), opcional.
     */
    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    // ─── CONTEXTO ACADÉMICO ───────────────────────────────────────

    /**
     * Período educativo (Peducativo) al que pertenece el grado del proyecto.
     */
    public function getPeducativoAttribute()
    {
        return Peducativo::query()
            ->select('peducativos.*')
            ->join('pestudios', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
            ->where('grados.id', $this->grado_id)
            ->groupBy('peducativos.manager_id')
            ->orderBy('peducativos.id')
            ->first();
    }

    /**
     * Usuario coordinador del período educativo del grado del proyecto.
     */
    public function getManagerAttribute()
    {
        return User::query()
            ->select('users.*')
            ->join('peducativos', 'users.id', '=', 'peducativos.manager_id')
            ->join('pestudios', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
            ->where('grados.id', $this->grado_id)
            ->groupBy('peducativos.manager_id')
            ->orderBy('peducativos.id')
            ->first();
    }

    /**
     * Carga académica (áreas de aprendizaje) de la sección del proyecto.
     *
     * @param  int|null  $profesor_id  Filtra por profesor.
     * @param  int|null  $lapso_id  Filtra por momento.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPevaluacions($profesor_id = null, $lapso_id = null)
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->selectRaw('CONCAT(asignaturas.name, " [",asignaturas.code, "] ",grados.code, " ",seccions.name, " ", lapsos.code_sm) as fullname_lg')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->where('seccions.id', $this->seccion_id)
            ->whereNull('pensums.deleted_at')
            ->whereNull('pevaluacions.deleted_at');

        $pevaluacions = ($profesor_id) ? $pevaluacions->where('pevaluacions.profesor_id', $profesor_id) : $pevaluacions;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id', $lapso_id) : $pevaluacions;

        return $pevaluacions->get();
    }

    /**
     * Igual que {@see getPevaluacions()} pero como lista `id => fullname_lg`
     * para poblar un `<select>`.
     *
     * Devuelve SIEMPRE una `Collection`, nunca `[]`: las propiedades
     * `$listPevaluacion` de los componentes están tipeadas como `Collection` y
     * asignarles un array lanzaba un `TypeError` que el `catch (\Throwable)` de
     * `openModal()` convertía en un modal cerrado en silencio.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPevaluacionsList($profesor_id = null, $lapso_id = null)
    {
        $pevaluacions = $this->getPevaluacions($profesor_id, $lapso_id);

        return $pevaluacions->count()
            ? $pevaluacions->pluck('fullname_lg', 'id')
            : collect();
    }

    // ─── ORDEN ───────────────────────────────────────────────────

    public function getOrderedSummaries()
    {
        return $this->eiprojectsummaries()
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END') // Primero los que tienen order no nulo
            ->orderBy('order') // Luego ordenar por order (si no es nulo)
            ->orderBy('created_at') // Finalmente ordenar por created_at
            ->get();
    }

    /**
     * Revisiones del proyecto ordenadas. OJO: este método solo tiene sentido en
     * contexto `Eiprojectk` — el falso positivo documentado en el blueprint
     * (I3) fue asumir que faltaba en la cabecera quincenal.
     */
    public function getOrderedViews()
    {
        return $this->eiprojectreviews()
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END') // Primero los que tienen order no nulo
            ->orderBy('order') // Luego ordenar por order (si no es nulo)
            ->orderBy('created_at') // Finalmente ordenar por created_at
            ->get();
    }

    public function getOrderedStrategies()
    {
        return $this->eiprojectkstrategies()
            ->orderBy('momento_rutina_diaria')
            ->orderBy('day_of_week')
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('order')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * @return Eiprojectkstrategy|null
     */
    public function getStrategyByMomentAndDay($momento_rutina_diaria, $day_of_week)
    {
        return $this->eiprojectkstrategies()
            ->where('momento_rutina_diaria', $momento_rutina_diaria)
            ->where('day_of_week', $day_of_week)
            ->first();
    }

    // ─── LISTAS AUXILIARES ───────────────────────────────────────

    /**
     * Proyectos disponibles para el select "Proyecto vinculado" de las
     * planificaciones, como `"id: diagnóstico (truncado)"`.
     *
     * @param  int|null  $profesor_id  Filtra por profesor.
     * @return \Illuminate\Support\Collection
     */
    public static function getForProfesorIdList($profesor_id = null)
    {
        $query = Eiprojectk::query();

        if ($profesor_id) {
            $query->where('profesor_id', $profesor_id);
        }

        $results = $query->pluck('diagnostico', 'id');

        // Concatenar 'id' con 'diagnostico' acotado a 50 caracteres
        return $results->map(function ($diagnostico, $id) {
            return $id.': '.Str::limit($diagnostico, 50, ' ...');
        });
    }

    // ─── ACCESORS ────────────────────────────────────────────────

    public function getListMomentAttribute()
    {
        return Eiprojectkstrategy::LIST_MOMENT;
    }

    public function getWeekDaysAttribute()
    {
        return Eiprojectkstrategy::WEEK_DAYS;
    }
}
