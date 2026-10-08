<?php

namespace App\Models\app\Inicial;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Planificación semanal de Educación Inicial (cabecera).
 *
 * Es la entidad P0 del módulo: el docente de Inicial planifica una semana
 * (fechas, diagnóstico, tiempo de ejecución) y luego la llena de estrategias
 * en la rejilla día × momento de la rutina diaria ({@see Eiplanningwstrategy})
 * y de resúmenes por área de aprendizaje ({@see Eiplanningwsummary}).
 *
 * Spec: blueprint/inicial · D1 (sin SoftDeletes, fiel al DDL clonado).
 *
 * @property int $profesor_id
 * @property int $grado_id
 * @property int $seccion_id
 * @property int|null $eiprojectk_id
 * @property string $finicial
 * @property string $ffinal
 * @property int $tiempo_ejecucion
 * @property string|null $diagnostico
 * @property string|null $observacion
 */
class Eiplanningwk extends Model
{
    use HasFactory;

    protected $fillable = [
        'profesor_id',
        'grado_id',
        'seccion_id',
        'eiprojectk_id',
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
        'finicial' => 'Inicio',
        'ffinal' => 'Culminación',
        'tiempo_ejecucion' => 'Cant.Semanas',
        'diagnostico' => 'Diagnóstico inicial',
        'observacion' => 'Observación',
        'lapso_id' => 'Momento',
        'eiprojectk_id' => 'Proyecto vinculado',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function eiplanningwsummaries()
    {
        return $this->hasMany(Eiplanningwsummary::class, 'eiplanningwk_id');
    }

    public function eiplanningwstrategies()
    {
        return $this->hasMany(Eiplanningwstrategy::class, 'eiplanningwk_id');
    }

    public function eiprojectk()
    {
        return $this->belongsTo(Eiprojectk::class, 'eiprojectk_id');
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

    // ─── CONTEXTO ACADÉMICO ───────────────────────────────────────

    /**
     * Período educativo (Peducativo) al que pertenece el grado del plan.
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
     * Usuario coordinador del período educativo del grado del plan.
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
     * Carga académica (áreas de aprendizaje) de la sección del plan, con un
     * alias legible `fullname_lg` para los select del módulo.
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
     * Devuelve SIEMPRE una `Collection`, nunca `[]`: la propiedad
     * `EiplanningwkComponent::$listPevaluacion` está tipeada como `Collection`
     * y asignarle un array lanzaba un `TypeError` cada vez que un plan no
     * tenía pevaluaciones para su sección (el modal de resúmenes se cerraba en
     * silencio).
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

    /**
     * Estrategias agrupadas por momento de rutina y día. `order` es palabra
     * reservada en MySQL y admite NULL, así que el NULLS-LAST se hace con
     * `CASE WHEN` (patrón ya probado en producción legacy).
     */
    public function getOrderedStrategies()
    {
        return $this->eiplanningwstrategies()
            ->orderBy('momento_rutina_diaria') // Primero agrupar por momento de rutina
            ->orderBy('day_of_week') // Luego ordenar por día de la semana dentro de cada grupo
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END') // Priorizar registros con order definido
            ->orderBy('order') // Ordenar por el campo order (si existe)
            ->orderBy('created_at') // Orden final por fecha de creación
            ->get();
    }

    public function getOrderedSummaries()
    {
        return $this->eiplanningwsummaries()
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END') // Primero los que tienen order no nulo
            ->orderBy('order') // Luego ordenar por order (si no es nulo)
            ->orderBy('created_at') // Finalmente ordenar por created_at
            ->get();
    }

    /**
     * Recupera la estrategia de una celda concreta de la rejilla.
     *
     * @param  string  $momento_rutina_diaria  Clave de {@see Eiplanningwstrategy::LIST_MOMENT}.
     * @param  string  $day_of_week  Clave de {@see Eiplanningwstrategy::WEEK_DAYS}.
     * @return Eiplanningwstrategy|null
     */
    public function getStrategyByMomentAndDay($momento_rutina_diaria, $day_of_week)
    {
        return $this->eiplanningwstrategies()
            ->where('momento_rutina_diaria', $momento_rutina_diaria)
            ->where('day_of_week', $day_of_week)
            ->first();
    }

    // ─── ACCESORS ────────────────────────────────────────────────

    public function getListMomentAttribute()
    {
        return Eiplanningwstrategy::LIST_MOMENT;
    }

    public function getWeekDaysAttribute()
    {
        return Eiplanningwstrategy::WEEK_DAYS;
    }
}
