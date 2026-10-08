<?php

namespace App\Models\app\Inicial;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Plan de evaluación de Educación Inicial (cabecera).
 *
 * Es la ÚNICA cabecera del módulo con `lapso_id` (el resto se liga al período
 * por la `pevaluacion` de sus resúmenes) y SIN `tiempo_ejecucion`.
 *
 * ⚠️ El DDL tiene DOS columnas de observación: `observaciones` (la del docente,
 * la única `fillable` y la que usa la UI) y `observacion` (huérfana, nunca se
 * escribe). No portar `observacion` al fillable.
 *
 * `@property string|null $recomendacion  Lo escribe el Coordinador de Evaluación
 *                                        desde la perspectiva de revisión, no el
 *                                        docente (regla `min:5` en F5).
 *
 * @property int $profesor_id
 * @property int $grado_id
 * @property int $lapso_id
 * @property int $seccion_id
 * @property int|null $pensum_id
 * @property string $finicial
 * @property string $ffinal
 * @property string|null $observaciones
 * @property string|null $recomendacion
 * @property string|null $asistencia
 */
class Eievaluationk extends Model
{
    use HasFactory;

    protected $fillable = [
        'profesor_id',
        'grado_id',
        'lapso_id',
        'seccion_id',
        'pensum_id',
        'finicial',
        'ffinal',
        'observaciones',
        'recomendacion',
        'asistencia',
    ];

    protected $casts = [
        'finicial' => 'date',
        'ffinal' => 'date',
    ];

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'grado_id' => 'Grado',
        'lapso_id' => 'Momento',
        'seccion_id' => 'Sección',
        'pensum_id' => 'Área de aprendizaje',
        'finicial' => 'Fecha inicial',
        'ffinal' => 'Fecha final',
        'observaciones' => 'Observaciones del docente',
        'recomendacion' => 'Recomendación del Coord. de Evaluación',
        'asistencia' => 'Control de Asistencia',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function eievaluationps()
    {
        return $this->hasMany(Eievaluationp::class, 'eievaluationk_id');
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

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
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
     * Carga académica (áreas de aprendizaje) de la sección del plan.
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

    // ─── POSICIONES POR ÁREA ──────────────────────────────────────

    /**
     * Posiciones (ítems de evaluación) de un área de aprendizaje concreta,
     * para la vista de tabs por área.
     *
     * @param  int  $id  pevaluacion_id del área.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPositionsForArea($id)
    {
        return Eievaluationp::select('eievaluationps.*')
            ->join('eievaluationks', 'eievaluationks.id', '=', 'eievaluationps.eievaluationk_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'eievaluationps.pevaluacion_id')
            ->where('eievaluationks.id', $this->id)
            ->where('pevaluacions.id', $id)
            ->orderByRaw('CASE WHEN `eievaluationps`.`order` IS NOT NULL THEN 0 ELSE 1 END') // Primero los que tienen order no nulo
            ->orderBy('eievaluationps.order') // Luego ordenar por order (si no es nulo)
            ->orderBy('eievaluationps.created_at') // Finalmente ordenar por created_at
            ->get();
    }

    /**
     * Igual que {@see getPositionsForArea()} pero descartando las posiciones
     * vacías (solo las que tienen algún dato de la actividad registrada).
     *
     * @param  int  $id  pevaluacion_id del área.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPositionsForAreaFilter($id)
    {
        return Eievaluationp::where('eievaluationk_id', $this->id)
            ->where('pevaluacion_id', $id)
            ->where(function ($query) {
                $query->whereNotNull('fecha')
                    ->orWhereNotNull('nombre_ninos')
                    ->orWhereNotNull('aprendizaje_alcanzado')
                    ->orWhereNotNull('indicadores')
                    ->orWhereNotNull('instrumento')
                    ->orWhereNotNull('observacion');
            })
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('order')
            ->orderBy('created_at')
            ->get();
    }

    public function getOrderedEvaluationps()
    {
        return $this->eievaluationps()
            ->orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END') // Primero los que tienen order no nulo
            ->orderBy('order') // Luego ordenar por order (si no es nulo)
            ->orderBy('created_at') // Finalmente ordenar por created_at
            ->get();
    }
}
