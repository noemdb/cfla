<?php

namespace App\Models\app\Academy;

use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pestudio extends Model
{
    use HasFactory;

    protected $fillable = [
        'peducativo_id', 'code', 'code_oficial', 'manager_id', 'name', 'order',
        'description', 'description_aux', 'mention', 'status_build_promotion',
        'title', 'scale', 'profile', 'color', 'show_hr', 'status_a_cualitative',
        'activities_avr', 'status_active', 'show_quantitative_indicators',
        'status_inscripcion_active', 'status_carga_notas', 'status_baremo',
        'status_socials', 'planning_module', 'paper',
        'remision_resumen_final', 'fecha_prosecucion', 'description_trainig_component',
        'fecha_promocion', 'fecha_descriptivo', 'fecha_certificacion', 'fecha_informe_final',
    ];

    public function peducativo()
    {
        return $this->belongsTo(Peducativo::class, 'peducativo_id');
    }

    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    public function escala()
    {
        return $this->belongsTo(Escala::class, 'scale');
    }

    public function grados()
    {
        return $this->hasMany(Grado::class, 'pestudio_id');
    }

    public function getGradosActive()
    {
        return $this->grados->sortBy('code_sm')->where('status_active','true');
    }

    public function getGradosActiveWithPensum()
    {
        return $this->grados()
            ->where('status_active', 'true')
            ->has('pensums')
            ->get()
            ->sortBy('code_sm');
    }

    public function scopeActive($query, $flag='true') {
        return $query->where('pestudios.status_active', $flag);
    }

    public function pensums()
    {
        return $this->hasMany(Pensum::class, 'pestudio_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->code} {$this->name}";
    }

    // ─── KPI / INDICATOR METHODS ──────────────────────────────────

    /**
     * Colección de secciones que tienen pevaluacions en este pestudio.
     */
    public function getSeccions($lapsoId = null)
    {
        $seccions = Seccion::select('seccions.*')
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('seccions.deleted_at')
            ->distinct();

        if ($lapsoId) {
            $seccions = $seccions->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $seccions->get();
    }

    /**
     * Profesores con pevaluacions en este pestudio.
     */
    public function getProfesors($lapsoId = null)
    {
        $profesors = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->distinct();

        if ($lapsoId) {
            $profesors = $profesors->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $profesors->get();
    }

    /**
     * Pevaluacions en este pestudio (con grado, asignatura).
     */
    public function getPevaluacions($lapsoId = null)
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->with(['seccion', 'seccion.grado', 'pensum.asignatura'])
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $pevaluacions = $pevaluacions->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $pevaluacions->get();
    }

    /**
     * Actividades en este pestudio.
     */
    public function getActivities($lapsoId = null)
    {
        $activities = Activity::select('activities.*')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $activities = $activities->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $activities->get();
    }

    /**
     * Evaluacions en este pestudio.
     */
    public function getEvaluacions($lapsoId = null)
    {
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions', 'evaluacions.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $evaluacions = $evaluacions->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $evaluacions->get();
    }

    /**
     * Boletins en este pestudio.
     */
    public function getBoletins($lapsoId = null)
    {
        $boletins = Boletin::select('boletins.*')
            ->join('evaluacions', 'boletins.evaluacion_id', '=', 'evaluacions.id')
            ->join('pevaluacions', 'evaluacions.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $boletins = $boletins->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $boletins->get();
    }

    /**
     * Goal (target) para carga de notas: evaluacions × estudiantes por sección.
     */
    public function goal_notas_load($lapsoId = null)
    {
        $seccions = Seccion::select('seccions.id', DB::raw('count(evaluacions.id) as count_evaluacions'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->groupBy('pevaluacions.id');

        if ($lapsoId) {
            $seccions = $seccions->where('pevaluacions.lapso_id', $lapsoId);
        }

        $seccions = $seccions->get();

        $total = 0;
        foreach ($seccions as $seccion) {
            $estudiants = $seccion->inscripcions;
            if ($estudiants->isNotEmpty()) {
                $total += $estudiants->count() * $seccion->count_evaluacions;
            }
        }

        return $total ?: 0;
    }

    /**
     * Real carga de notas: boletins count (notas cargadas realmente).
     */
    public function real_notas_load($lapsoId = null)
    {
        $count = Boletin::select(DB::raw('count(boletins.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNotNull('boletins.nota')
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $lapso = Lapso::find($lapsoId);
            $count = $count->where('pevaluacions.lapso_id', $lapsoId);
            if ($lapso && $lapso->ffinal) {
                $count = $count->where('boletins.created_at', '<=', $lapso->ffinal);
            }
        }

        $result = $count->first();

        return $result ? (int) $result->value : 0;
    }

    /**
     * IEE promedio (ieePROM) — promedio de boletins por profesor en este pestudio.
     * Se usa como denominador para IRE.
     */
    public function getProfesorsIEEsPROM($lapsoId = null)
    {
        $profesors = $this->getProfesors($lapsoId);

        if ($profesors->isEmpty()) {
            return 0;
        }

        $totalBoletins = 0;
        foreach ($profesors as $profesor) {
            $totalBoletins += $profesor->getBoletins($lapsoId, $this->id)->count();
        }

        return $totalBoletins / $profesors->count();
    }

    /**
     * Promedio de actividades por área de formación (cobertura curricular).
     */
    public function getAvgActivitiesPerPlan($lapsoId = null)
    {
        $pevIds = Pevaluacion::whereNull('pevaluacions.deleted_at')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->when($lapsoId, fn($q) => $q->where('pevaluacions.lapso_id', $lapsoId))
            ->pluck('pevaluacions.id');

        $totalPevaluacions = $pevIds->count();

        if ($totalPevaluacions === 0) {
            return 0;
        }

        $totalActivities = Activity::whereIn('pevaluacion_id', $pevIds)->count();

        return round($totalActivities / $totalPevaluacions, 2);
    }

    /**
     * Cantidad de profesores con actividades en este pestudio.
     */
    public function getActiveTeachersCount($lapsoId = null)
    {
        $query = Profesor::select('profesors.id')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->whereExists(function ($q) use ($lapsoId) {
                $q->select(DB::raw(1))
                    ->from('activities')
                    ->whereColumn('activities.pevaluacion_id', 'pevaluacions.id');
            });

        if ($lapsoId) {
            $query = $query->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $query->distinct('profesors.id')->count('profesors.id');
    }

    /**
     * Cantidad total de profesores con carga académica en este pestudio.
     */
    public function getTeachersCount($lapsoId = null)
    {
        $query = Profesor::select('profesors.id')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $query = $query->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $query->distinct('profesors.id')->count('profesors.id');
    }

    /**
     * Porcentaje de aprobados en este pestudio (basado en boletins).
     */
    public function getPorcAprobados($lapsoId = null, $decimal = 2)
    {
        $boletins = Boletin::select('boletins.nota', 'escalas.minimo', 'escalas.maximo', 'escalas.aprobacion')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('escalas', 'escalas.id', '=', 'pevaluacions.escala_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNotNull('boletins.nota')
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $boletins = $boletins->where('pevaluacions.lapso_id', $lapsoId);
        }

        $boletins = $boletins->get();

        if ($boletins->isEmpty()) {
            return null;
        }

        $aprobados = $boletins->filter(fn($b) => $b->nota >= $b->aprobacion)->count();

        return round(100 * $aprobados / $boletins->count(), $decimal);
    }

    /**
     * Promedio de notas en este pestudio.
     */
    public function getPromedio($lapsoId = null, $decimal = 2)
    {
        $count = Boletin::select(
            DB::raw('count(boletins.id) as value'),
            DB::raw('sum(boletins.nota) as sum_nota')
        )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNotNull('boletins.nota')
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->groupBy('pevaluacions.profesor_id');

        if ($lapsoId) {
            $count = $count->where('pevaluacions.lapso_id', $lapsoId);
        }

        $result = $count->first();

        return $result ? round($result->sum_nota / $result->value, $decimal) : null;
    }

    /**
     * Count of enrolled students (inscritos) for this pestudio.
     */
    public function getInscritosCount(?int $lapsoId = null): int
    {
        $query = Estudiant::where('estudiants.status_active', 'true')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->where('grados.pestudio_id', $this->id)
            ->whereNull('inscripcions.deleted_at');

        if ($lapsoId) {
            $lapso = Lapso::find($lapsoId);
            if ($lapso && $lapso->ffinal) {
                $query->where('inscripcions.created_at', '<=', $lapso->ffinal);
            }
        }

        return $query->count();
    }

    /**
     * Count of pevaluacions (evaluation plans) for this pestudio.
     */
    public function getPevaluacionsCount(?int $lapsoId = null): int
    {
        $query = Pevaluacion::whereNull('pevaluacions.deleted_at')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id);

        if ($lapsoId) {
            $query->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $query->count();
    }

    /**
     * Count of activities for this pestudio.
     */
    public function getActivitiesCount(?int $lapsoId = null): int
    {
        $query = Pevaluacion::whereNull('pevaluacions.deleted_at')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->where('pensums.pestudio_id', $this->id);

        if ($lapsoId) {
            $query->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $query->count();
    }

    /**
     * Count of distinct profesors who have pevaluacions in this pestudio.
     */
    public function getProfesoresConCargaCount(?int $lapsoId = null): int
    {
        $query = Profesor::join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('profesors.status_active', 'true')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $query->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $query->distinct('profesors.id')->count('profesors.id');
    }

    /**
     * Profesors who have pevaluacions in this pestudio, with full KPI data.
     * Uses real IEE/IEE-CN/IRE formulas via Profesor model methods.
     */
    public function getProfesorsWithKPIs(?int $lapsoId = null): \Illuminate\Support\Collection
    {
        $profesors = Profesor::where('profesors.status_active', 'true')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->when($lapsoId, fn($q) => $q->where('pevaluacions.lapso_id', $lapsoId))
            ->select('profesors.id', 'profesors.name', 'profesors.lastname', 'profesors.ci_profesor')
            ->distinct()
            ->get();

        // Pre-calculate ieePROM for IRE
        $ieePROM = $this->getProfesorsIEEsPROM($lapsoId);

        return $profesors->map(function ($profesor) use ($lapsoId, $ieePROM) {
            $fullProfesor = Profesor::find($profesor->id);

            $pevIds = Pevaluacion::where('profesor_id', $profesor->id)
                ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                ->where('pensums.pestudio_id', $this->id)
                ->when($lapsoId, fn($q) => $q->where('pevaluacions.lapso_id', $lapsoId))
                ->pluck('pevaluacions.id');

            $pevCount = $pevIds->count();

            $activitiesCount = Activity::whereIn('pevaluacion_id', $pevIds)->count();
            $approvedActivities = Activity::whereIn('pevaluacion_id', $pevIds)
                ->where('status', true)
                ->count();

            $approvalRate = $activitiesCount > 0 ? round(($approvedActivities / $activitiesCount) * 100, 1) : 0;

            // Real IEE/IEE-CN formulas via Profesor model
            $iee = $fullProfesor ? $fullProfesor->getProfesorIEE($lapsoId, $this->id) : 0;
            $ieeCN = $fullProfesor ? $fullProfesor->getProfesorIEECN($lapsoId, $this->id) : 0;
            $ieePct = $iee !== null ? round(min(100, $iee * 100), 1) : 0;
            $ieeCNPct = $ieeCN !== null ? round(min(100, $ieeCN * 100), 1) : 0;

            // Real IRE (relative to ieePROM)
            $boletinsCount = $fullProfesor ? $fullProfesor->getBoletins($lapsoId, $this->id)->count() : 0;
            $ire = ($ieePROM > 0) ? round(100 * $boletinsCount / $ieePROM, 1) : 0;

            return (object) [
                'id' => $profesor->id,
                'full_name' => trim("{$profesor->lastname} {$profesor->name}"),
                'ci_profesor' => $profesor->ci_profesor,
                'profesor' => $fullProfesor,
                'pevaluacions_count' => $pevCount,
                'activities_count' => $activitiesCount,
                'boletins_count' => $boletinsCount,
                'approval_rate' => $approvalRate,
                'iee' => $ieePct,
                'iee_raw' => $iee,
                'iee_cn' => $ieeCNPct,
                'ire' => $ire,
            ];
        });
    }

    /**
     * Activity indicators for this pestudio — matching the original blueprint.
     */
    public function getActivityIndicators(?int $lapsoId = null): object
    {
        $pevQuery = Pevaluacion::whereNull('pevaluacions.deleted_at')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $this->id);

        if ($lapsoId) {
            $pevQuery->where('pevaluacions.lapso_id', $lapsoId);
        }

        $pevIds = $pevQuery->pluck('pevaluacions.id');
        $totalPevaluacions = $pevIds->count();

        $activities = Activity::whereIn('pevaluacion_id', $pevIds)->get();
        $totalActivities = $activities->count();

        // 1. Total de actividades planificadas
        // $totalActivities (above)

        // 2. Cobertura Curricular: avg activities per plan
        $coberturaCurricular = $totalPevaluacions > 0
            ? round($totalActivities / $totalPevaluacions, 2)
            : 0;

        // 3. Participación: % profesors with activities vs all with carga
        $totalProfesores = $this->getTeachersCount($lapsoId);
        $activeProfesores = $this->getActiveTeachersCount($lapsoId);
        $participacion = $totalProfesores > 0
            ? round(($activeProfesores / $totalProfesores) * 100, 1)
            : 0;

        // 4. Seguimiento: % activities with comments
        $withComments = $activities->filter(fn($a) => !empty($a->comments))->count();
        $seguimiento = $totalActivities > 0
            ? round(($withComments / $totalActivities) * 100, 1)
            : 0;

        // 5. Aprobación: % activities with status = true
        $approved = $activities->where('status', true)->count();
        $aprobacion = $totalActivities > 0
            ? round(($approved / $totalActivities) * 100, 1)
            : 0;

        // 6. Supervisión: % pevaluacions with observations
        $pevWithObs = Pevaluacion::whereIn('id', $pevIds)
            ->whereNotNull('observations')
            ->where('observations', '<>', '')
            ->count();
        $supervision = $totalPevaluacions > 0
            ? round(($pevWithObs / $totalPevaluacions) * 100, 1)
            : 0;

        return (object) [
            'total_activities' => $totalActivities,
            'cobertura_curricular' => $coberturaCurricular,
            'participacion' => $participacion,
            'seguimiento' => $seguimiento,
            'aprobacion' => $aprobacion,
            'supervision' => $supervision,
        ];
    }

}
