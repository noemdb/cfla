<?php

namespace App\Models\app\Academy;

use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Profesor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id','ti_teacher','ci_profesor','lastname','name','gender','date_birth','city_birth','town_hall_birth',
        'state_birth','country_birth','dir_address','phone','cellphone','email','gsemail','gspassword','status_census_taker','status_active'
    ];

    public static function getProfesorsAcademic()
    {
        return DB::table('profesors')
        ->select('profesors.id','profesors.ci_profesor', 'profesors.name', 'profesors.lastname','profesors.date_birth')
        ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
        ->where('profesors.status_active', "true")
        ->wherenull('pevaluacions.deleted_at')
        ->orderBy('profesors.name')
        ->groupBy('profesors.id','profesors.ci_profesor','profesors.name', 'profesors.lastname','profesors.date_birth')
        ->get();
    }

    /**
     * Retorna profesores activos filtrados por pestudio, formateados para select.
     */
    public static function list_profesors_pestudio($pestudio_id)
    {
        return self::select('profesors.id')
            ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $pestudio_id)
            ->where('profesors.status_active', true)
            ->whereNull('pevaluacions.deleted_at')
            ->orderBy('profesors.lastname')
            ->distinct()
            ->pluck('profesor_fullname', 'id');
    }

    /**
     * Scope: profesores visibles para un líder de planificación.
     */
    public static function getProfesorForLeaderId($userId)
    {
        return self::where('profesors.user_id', $userId)->get();
    }

    // ─── RELATIONSHIPS ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function pevaluacions()
    {
        return $this->hasMany(Pevaluacion::class, 'profesor_id');
    }

    public function profesor_guias()
    {
        return $this->hasMany(ProfesorGuia::class, 'profesor_id');
    }

    public function pensums()
    {
        return $this->belongsToMany(Pensum::class, 'pevaluacions', 'profesor_id', 'pensum_id')
            ->select('pensums.*')
            ->selectRaw("CONCAT(grados.code_sm, ' - ', asignaturas.name) as asignatura_name")
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->whereNull('pevaluacions.deleted_at')
            ->groupBy('pensums.id');
    }

    // ─── ACCESSORS ───────────────────────────────────────────────

    public function getFullNameAttribute()
    {
        return trim("{$this->lastname} {$this->name}");
    }

    /**
     * Secciones donde el profesor es guía/tutor.
     */
    public function getSeccionGuiasAttribute()
    {
        $seccions = Seccion::select('seccions.*', 'lapsos.name as lapso_name')
            ->join('profesor_guias', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('lapsos', 'lapsos.id', '=', 'profesor_guias.lapso_id')
            ->where('profesor_guias.profesor_id', $this->id)
            ->groupBy('seccions.id')
            ->get();

        return $seccions;
    }

    // ─── PENSUMS (para el módulo de Competencias) ──────────────

    /**
     * Obtiene los pensums (áreas de formación) asignados al profesor
     * a través de su carga académica (pevaluacions), incluyendo
     * el conteo de preguntas de competencias asociadas.
     */
    public function getPensumsName()
    {
        return Pensum::select('pensums.*')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->where('pensums.status_active', true)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->with(['asignatura', 'grado.pestudio'])
            ->distinct()
            ->get();
    }

    /**
     * Lista los grados donde el profesor dicta clases.
     */
    public static function list_grado($profesorId)
    {
        return Grado::select('grados.*')
            ->join('pensums', 'pensums.grado_id', '=', 'grados.id')
            ->join('pevaluacions', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.profesor_id', $profesorId)
            ->where('grados.status_active', true)
            ->whereNull('pensums.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->distinct()
            ->get();
    }

    // ═══════════════════════════════════════════════════════════════
    //  KPI: PESTUDIO/LAPSO QUERIES
    // ═══════════════════════════════════════════════════════════════

    public function getPevaluacionsPestudioLapso($pestudioId = null, $lapsoId = null)
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->whereNull('pensums.deleted_at')
            ->whereNull('pestudios.deleted_at');

        $pevaluacions = ($pestudioId) ? $pevaluacions->where('pestudios.id', $pestudioId) : $pevaluacions;
        $pevaluacions = ($lapsoId) ? $pevaluacions->where('pevaluacions.lapso_id', $lapsoId) : $pevaluacions;

        return $pevaluacions->get();
    }

    public function getActivitiesPestudioLapso($pestudioId = null, $lapsoId = null)
    {
        $activities = Activity::select('activities.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('seccions.deleted_at')
            ->whereNull('grados.deleted_at')
            ->whereNull('pestudios.deleted_at');

        $activities = ($pestudioId) ? $activities->where('pestudios.id', $pestudioId) : $activities;
        $activities = ($lapsoId) ? $activities->where('pevaluacions.lapso_id', $lapsoId) : $activities;

        return $activities->get();
    }

    public function getBoletins($lapsoId = null, $pestudioId = null)
    {
        $boletins = Boletin::select('boletins.*')
            ->join('evaluacions', 'boletins.evaluacion_id', '=', 'evaluacions.id')
            ->join('pevaluacions', 'evaluacions.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('profesors', 'pevaluacions.profesor_id', '=', 'profesors.id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('profesors.id', $this->id)
            ->whereNull('pestudios.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('profesors.deleted_at')
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at');

        $boletins = ($lapsoId) ? $boletins->where('pevaluacions.lapso_id', $lapsoId) : $boletins;
        $boletins = ($pestudioId) ? $boletins->where('pestudios.id', $pestudioId) : $boletins;

        return $boletins->get();
    }

    public function getEvaluacions($pestudioId = null, $lapsoId = null)
    {
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('profesors.id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('profesors.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('pestudios.deleted_at');

        $evaluacions = ($pestudioId) ? $evaluacions->where('pestudios.id', $pestudioId) : $evaluacions;
        $evaluacions = ($lapsoId) ? $evaluacions->where('pevaluacions.lapso_id', $lapsoId) : $evaluacions;

        return $evaluacions->get();
    }

    public function getBoletinsPestudioLapso($pestudioId = null, $lapsoId = null)
    {
        return $this->getBoletins($lapsoId, $pestudioId);
    }

    // ═══════════════════════════════════════════════════════════════
    //  IEE — ÍNDICE DE EFICIENCIA EN EVALUACIÓN
    // ═══════════════════════════════════════════════════════════════

    /**
     * Goal (target): evaluacions × students per sección for this profesor.
     */
    public function goal_notas_load($lapsoId = null, $pestudioId = null)
    {
        $lapso = ($lapsoId) ? Lapso::find($lapsoId) : null;

        $seccions = Seccion::select('seccions.id', DB::raw('count(evaluacions.id) as count_evaluacions'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->groupBy('pevaluacions.id');

        $seccions = ($lapso) ? $seccions->where('pevaluacions.lapso_id', $lapso->id) : $seccions;
        $seccions = ($pestudioId) ? $seccions->where('pestudios.id', $pestudioId) : $seccions;

        $seccions = $seccions->get();

        $total = 0;
        foreach ($seccions as $seccion) {
            $estudiants = Inscripcion::where('seccion_id', $seccion->id)->with('estudiant')->get();
            if ($estudiants->isNotEmpty()) {
                $count = $estudiants->count();
                $total += $count * $seccion->count_evaluacions;
            }
        }

        return $total ?: 0;
    }

    /**
     * Real: boletins count (notas cargadas realmente) for this profesor.
     */
    public function real_notas_load($lapsoId = null, $pestudioId = null)
    {
        $count = Boletin::select(DB::raw('count(boletins.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->where('estudiants.status_active', 'true')
            ->whereNotNull('boletins.nota')
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->groupBy('pevaluacions.profesor_id');

        $count = ($lapsoId) ? $count->where('pevaluacions.lapso_id', $lapsoId) : $count;
        $count = ($pestudioId) ? $count->where('pestudios.id', $pestudioId) : $count;

        // Filtrar por fecha fin del lapso
        if ($lapsoId) {
            $lapso = Lapso::find($lapsoId);
            if ($lapso && $lapso->ffinal) {
                $count = $count->where('inscripcions.created_at', '<=', $lapso->ffinal);
                $count = $count->where('boletins.created_at', '<=', $lapso->ffinal);
            }
        }

        $result = $count->first();

        return $result ? (int) $result->value : 0;
    }

    /**
     * IEE = min(1, real / goal) → ratio de notas cargadas vs esperadas.
     */
    public function getProfesorIEE($lapsoId = null, $pestudioId = null)
    {
        $goal = $this->goal_notas_load($lapsoId, $pestudioId);
        $real = $this->real_notas_load($lapsoId, $pestudioId);
        return ($goal > 0) ? min(1, $real / $goal) : 0;
    }

    // ═══════════════════════════════════════════════════════════════
    //  IEE-CN — IEE con CORTE DE NOTAS
    // ═══════════════════════════════════════════════════════════════

    public function goal_notas_load_corte($lapsoId = null, $pestudioId = null)
    {
        $lapso = Lapso::findOrFail($lapsoId);
        $dateCutnote = $lapso->date_cutnote;

        $seccions = Seccion::select('seccions.id', DB::raw('count(evaluacions.id) as count_evaluacions'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->where('evaluacions.fecha', '<=', $dateCutnote)
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->groupBy('pevaluacions.id');

        $seccions = ($lapso) ? $seccions->where('pevaluacions.lapso_id', $lapso->id) : $seccions;
        $seccions = ($pestudioId) ? $seccions->where('pestudios.id', $pestudioId) : $seccions;

        $seccions = $seccions->get();

        $total = 0;
        foreach ($seccions as $seccion) {
            $estudiants = Inscripcion::where('seccion_id', $seccion->id)->with('estudiant')->get();
            if ($estudiants->isNotEmpty()) {
                $total += $estudiants->count() * $seccion->count_evaluacions;
            }
        }

        return $total ?: 0;
    }

    public function real_notas_load_corte($lapsoId = null, $pestudioId = null)
    {
        $lapso = Lapso::findOrFail($lapsoId);
        $dateCutnote = $lapso->date_cutnote;

        $count = Boletin::select(DB::raw('count(boletins.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->where('estudiants.status_active', 'true')
            ->whereNotNull('boletins.nota')
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->groupBy('pevaluacions.profesor_id');

        $count = $count->where('pevaluacions.lapso_id', $lapso->id);

        if ($dateCutnote) {
            $count = $count->where('inscripcions.created_at', '<=', $dateCutnote);
            $count = $count->where('evaluacions.fecha', '<=', $dateCutnote);
            $count = $count->where('boletins.created_at', '<=', $dateCutnote);
        }

        $count = ($pestudioId) ? $count->where('pestudios.id', $pestudioId) : $count;

        $result = $count->first();

        return $result ? (int) $result->value : 0;
    }

    public function getProfesorIEECN($lapsoId = null, $pestudioId = null)
    {
        if (!$lapsoId) {
            return $this->getProfesorIEE(null, $pestudioId);
        }

        $goal = $this->goal_notas_load_corte($lapsoId, $pestudioId);
        $real = $this->real_notas_load_corte($lapsoId, $pestudioId);
        return ($goal > 0) ? min(1, $real / $goal) : 0;
    }

    // ═══════════════════════════════════════════════════════════════
    //  IRE — ÍNDICE RELATIVO DE RENDIMIENTO
    // ═══════════════════════════════════════════════════════════════

    public function getProfesorIRE($pestudioId, $lapsoId = null)
    {
        $pestudio = Pestudio::find($pestudioId);
        if (!$pestudio) {
            return null;
        }

        $ieePROM = $pestudio->getProfesorsIEEsPROM($lapsoId);
        $boletins = $this->getBoletins($lapsoId, $pestudioId);

        return ($ieePROM > 0) ? round(100 * $boletins->count() / $ieePROM, 1) : null;
    }

    // ═══════════════════════════════════════════════════════════════
    //  GRADE STATS
    // ═══════════════════════════════════════════════════════════════

    public function getPorcAprobados($lapsoId = null, $decimal = 2, $pestudioId = null)
    {
        $boletins = Boletin::select('boletins.nota', 'escalas.minimo', 'escalas.maximo', 'escalas.aprobacion')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('escalas', 'escalas.id', '=', 'pevaluacions.escala_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->whereNotNull('boletins.nota')
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at');

        $boletins = ($lapsoId) ? $boletins->where('pevaluacions.lapso_id', $lapsoId) : $boletins;
        $boletins = ($pestudioId) ? $boletins->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id', $pestudioId) : $boletins;

        $boletins = $boletins->get();

        if ($boletins->isEmpty()) {
            return null;
        }

        $aprobados = $boletins->filter(fn($b) => $b->nota >= $b->aprobacion)->count();

        return round(100 * $aprobados / $boletins->count(), $decimal);
    }

    public function getPromedio($lapsoId = null, $decimal = 2, $pestudioId = null)
    {
        $count = Boletin::select(
            DB::raw('count(boletins.id) as value'),
            DB::raw('sum(boletins.nota) as sum_nota')
        )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->whereNotNull('boletins.nota')
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->groupBy('pevaluacions.profesor_id');

        $count = ($lapsoId) ? $count->where('pevaluacions.lapso_id', $lapsoId) : $count;

        $result = $count->first();

        return $result ? round($result->sum_nota / $result->value, $decimal) : null;
    }
}
