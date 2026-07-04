<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;


// use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar\Functions\Pestudio\Lists;
use App\Models\app\Pescolar\Functions\Pestudio\Inscripcions;
use App\Models\app\Pescolar\Functions\Pestudio\Preinscripcions;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollMain;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\Functions\Pestudio\ActivitiesTrait;
use App\Models\app\Pescolar\Functions\Pestudio\EvaluacionTrait;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;

class Pestudio extends Model
{
    use SoftDeletes;
    use Lists;
    use Inscripcions;
    use Preinscripcions;
    use EvaluacionTrait;
    use ActivitiesTrait;

    protected $fillable = [
        'peducativo_id',
        'code',
        'code_oficial',
        'manager_id',
        'name',
        'order',
        'description',
        'description_aux',
        'mention',
        'status_build_promotion',
        'title',
        'scale',
        'profile',
        'color',
        'show_hr',
        'status_a_cualitative',
        'status_baremo',
        'status_carga_notas',
        'status_active',
        'status_inscripcion_active',
        'remision_resumen_final',
        'fecha_informe_final',
        'fecha_certificacion',
        'fecha_descriptivo',
        'fecha_promocion',
        'fecha_prosecucion',
        'description_trainig_component',
        'show_quantitative_indicators',
        'status_socials',
        'planning_module',
        'activities_avr',
    ];

    protected $dates = ['created_at', 'updated_at'];

    const COLUMN_COMMENTS = [
        'peducativo_id' => 'Plan Educativo',
        'code' => 'Código',
        'code_oficial' => 'Código oficial',
        'manager_id' => 'Coordinador',
        'name' => 'Nombre',
        'order' => 'Orden de presentación',
        'description' => 'Descripción',
        'description_aux' => 'Descripción auxiliar',
        'mention' => 'Mención',
        'status_build_promotion' => 'Genera promoción',
        'title' => 'Descripción completa del titulo que se otorga',
        'scale' => 'Escala de evaluación',
        'profile' => 'Perfil',
        'color' => 'Color',
        'show_hr' => 'Mostrar en cuadro de honor',
        'status_a_cualitative' => 'Recibe Evaluaciones Descriptivas',
        'status_baremo' => 'Nota final literal',
        'status_carga_notas' => 'Permite carga de notas extenporáneas',
        'status_active' => 'Estado',
        'status_inscripcion_active' => 'Activo para la matriculación escolar',
        'remision_resumen_final' => 'Fecha de remisión de documentos/Resumen Final de Revisión',
        'fecha_informe_final' => 'Fecha Final Informe de Notas',
        'fecha_certificacion' => 'Fecha entrega de cerificación',
        'fecha_descriptivo' => 'Fecha entrega de informe descriptivo',
        'fecha_promocion' => 'Fecha entrega del certificado de promoción',
        'fecha_prosecucion' => 'Fecha entrega del certificado de prosecución',
        'description_trainig_component' => 'Descripción del Componente de Formación',
        'show_quantitative_indicators' => 'Indique si se muestran o no los indicadores cuantitativos',
        'status_socials' => 'Requiere una cantidad determinada de Horas cumplidas en Acciones Comunitarias.',
        'planning_module' => 'Permite registro de actividades de planificación',
        'activities_avr' => 'Cantidad de palabras promedio por actividad',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function peducativo()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Peducativo');
    }
    public function grados()
    {
        return $this->hasMany(Grado::class);
    }
    public function asignaturas()
    {
        return $this->hasMany('App\Models\app\Pescolar\Asignatura');
    }
    public function pensums()
    {
        return $this->hasMany('App\Models\app\Pescolar\Pensum');
    }
    public function baremos()
    {
        return $this->hasMany('App\Models\app\Pescolar\Baremo');
    }
    public function escala()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Escala', 'scale', 'id');
    }
    public function grupo_estables()
    {
        return $this->hasMany('App\Models\app\Estudiante\GrupoEstable');
    }
    public function pevaluacions()
    {
        return $this->hasManyThrough('App\Models\app\Profesor\Pevaluacion', 'App\Models\app\Pescolar\Pensum');
    }
    public function registro_titulos()
    {
        return $this->hasMany('App\Models\app\RegistroTitulo');
    }
    /**********************************************************************************/

    /**
     * Obtener baremos asociados al plan de estudio, filtrados por lapso.
     * Prioriza baremos específicos del lapso, con fallback a baremos generales (lapso_id = NULL).
     *
     * @param int|null $lapsoId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBaremos($lapsoId = null)
    {
        $query = $this->baremos(); // Relación hasMany definida en el modelo

        if ($lapsoId) {
            // Priorizar baremos del lapso específico, incluir generales como fallback
            $query->where(function($q) use ($lapsoId) {
                $q->where('lapso_id', $lapsoId)
                  ->orWhereNull('lapso_id');
            });
        } else {
            // Si no se pasa lapso, retornar solo baremos generales
            $query->whereNull('lapso_id');
        }

        return $query->orderBy('minimo', 'asc')->get();
    }

    /**
     * Obtener baremos por pestudio_id y lapso_id (versión estática)
     *
     * @param int $pestudioId
     * @param int|null $lapsoId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBaremosByPestudio($pestudioId, $lapsoId = null)
    {
        $query = Baremo::where('pestudio_id', $pestudioId);

        if ($lapsoId) {
            $query->where(function($q) use ($lapsoId) {
                $q->where('lapso_id', $lapsoId)
                  ->orWhereNull('lapso_id');
            });
        } else {
            $query->whereNull('lapso_id');
        }

        return $query->orderBy('minimo', 'asc')->get();
    }

    public static function getIndicadores()
    {

        $pestudios = Pestudio::Orderby('id', 'asc')->where('status_active', 'true')->where('show_quantitative_indicators', 'true')->get();
        $indicadores = collect([]);
        $lapsos = Lapso::all();
        foreach ($pestudios as $pestudio) {
            $lessons = Lesson::getForPestudioId($pestudio->id); //dd($lessons);
            $activities = Activity::getForPestudioId($pestudio->id); //dd($lessons);
            $pevaluacions = $pestudio->pevaluacions; //dd($pevaluacions);
            $count_pevaluacions = ($pevaluacions->IsNotEmpty()) ? $pevaluacions->count() : 0;
            $pensums = $pestudio->pensums;
            $porc_pevaluacions = ($pensums->count() > 0) ?  round((100 * $count_pevaluacions / ($pensums->count() * $lapsos->count() * 2)), 1) : null;
            // $porc_pevaluacions = round ( (100 * $count_pevaluacions / ($pestudio->pensums->count() * $lapsos->count() * 2)),1) ;

            $evaluacions = $pestudio->getEvaluacions();
            $profesors = $pestudio->getProfesors();
            $pevaluacions = $pestudio->pevaluacions();
            $pensums = $pestudio->getPensums();
            $count_evaluacions = ($evaluacions->IsNotEmpty()) ? $evaluacions->count() : 0;

            $goal_notas = $pestudio->goal_notas_load();
            $real_notas = $pestudio->real_notas_load();
            $porc_notas_load = ($goal_notas > 0) ? round((100 * ($real_notas / $goal_notas)), 1) : 0;

            $promedio = round($pestudio->getPromedio(), 1);
            $porc_aprobados = round($pestudio->getPorcAprobados(), 1);
            $indicador = collect([
                'id' => $pestudio->id,
                'name' => $pestudio->name,
                'code' => $pestudio->code,
                'escala_name' => $pestudio->escala->name,
                'count_pevaluacions' => $count_pevaluacions,
                'porc_pevaluacions' => $porc_pevaluacions,
                'count_evaluacions' => $count_evaluacions,
                'porc_notas_load' => $porc_notas_load,
                'promedio' => $promedio,
                'porc_aprobados' => $porc_aprobados,
                'evaluacions' => $evaluacions,
                'lessons' => $lessons,
                'count_lessons' => $lessons->count(),
                'activities' => $activities,
                'count_activities' => $activities->count(),
                'profesors' => $profesors,
                'pevaluacions' => $pevaluacions,
                'pensums' => $pensums,
            ]);
            $indicadores->push($indicador);
        }
        return $indicadores;
    }

    public function getAreaConocimientosAttribute()
    {
        $area_conocimientos = AreaConocimiento::select('area_conocimientos.*')
            ->join('campo_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id', $this->id)
            ->groupby('area_conocimientos.id')
            ->get();
        return $area_conocimientos;
    }

    public function goal_notas_load($lapso_id = null)
    {
        $seccions = Seccion::select('seccions.id', DB::raw('count(evaluacions.id) as value'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id', $this->id)
            ->where('seccions.status_active', 'true')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('seccions.id');
        // ->get();

        $seccions = (!empty($lapso_id)) ? $seccions->where('pevaluacions.lapso_id', $lapso_id) : $seccions;

        $seccions = $seccions->get(); // dd($seccions);
        $total = 0;
        foreach ($seccions as $seccion) {
            $count = $seccion->getEstudiants($lapso_id)->count();
            $total = $total + $seccion->value * $count; //dd($seccion,$count);
        }
        return ($total) ? $total : 0;
    }

    public function real_notas_load($lapso_id = null)
    {
        $count = Boletin::query()
            ->selectRaw('COUNT(boletins.id) as value')
            ->selectRaw("CONCAT(evaluacions.id,' - ' ,grados.id, ' - ', grados.name, ' - ', asignaturas.name, ' - ', evaluacions.description) as evaluacion_grado_asignatura_name")
            ->selectRaw("CONCAT(grados.name, ' - ', asignaturas.name, ' - ', evaluacions.description) as grado_asignatura_name")

            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')

            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')

            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('pestudios.id', $this->id)
            ->where('estudiants.status_active', 'true')

            // ->whereNotNull('boletins.nota')

            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('inscripcions.deleted_at')

            // ->orderBy('grado_asignatura_name')
            // ->groupBy('evaluacions.id');

            ->groupBy('pestudios.id');

        // if ($this->id==2) dd($count->pluck('value','grado_asignatura_name'));

        if (!empty($lapso_id)) {
            $lapso = Lapso::findOrFail($lapso_id); //dd($lapso->finicial);
            $count = $count->where('pevaluacions.lapso_id', $lapso->id)
                // ->whereDate('inscripcions.created_at', '>=', $lapso->finicial.' 00:00:00')
                ->whereDate('inscripcions.created_at', '<=', $lapso->ffinal);
        }

        //if ($this->id==2) dd($count->pluck('value','grado_asignatura_name'));        

        $count = $count->first();

        return ($count) ? $count->value : 0;
    }

    public function getGradosActive()
    {
        return $this->grados->where('status_active', 'true');
    }

    public function getProfesorsIEEsPROM($lapso_id = null)
    {
        $promedio =
            Boletin::join('evaluacions', 'boletins.evaluacion_id', '=', 'evaluacions.id')
            ->join('pevaluacions', 'evaluacions.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('profesors', 'pevaluacions.profesor_id', '=', 'profesors.id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id', $this->id)
            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('profesors.id')
            ->selectRaw('COUNT(*) as cnt');
        $promedio = (!empty($lapso_id)) ? $promedio->where('pevaluacions.lapso_id', $lapso_id) : $promedio;
        $promedio = $promedio->get()->avg('cnt'); //dd($promedio);
        return $promedio;
    }

    public function getProfesors($lapso_id = null)
    {
        $profesors =
            Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->where('pestudios.id', $this->id)
            ->wherenull('pestudios.deleted_at')
            ->wherenull('grados.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('profesors.id');

        $profesors = (!empty($lapso_id)) ? $profesors->where('pevaluacions.lapso_id', $lapso_id) : $profesors;

        $profesors = $profesors->get(); //dd($profesors);

        return $profesors;
    }
    public function getProfesorsCount($lapso_id = null)
    {
        return $this->getProfesors($lapso_id)->count();
    }

    public function getProfesorGuia($lapso_id = null)
    {
        $profesor_guias =
            ProfesorGuia::query()
            ->select('profesors.*')
            ->join('profesors', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('grados', 'grados.id', '=', 'profesor_guias.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')

            ->where('pestudios.id', $this->id)

            ->where('seccions.status_active', "true")
            ->where('grados.status_active', "true")

            ->wherenull('pestudios.deleted_at')
            ->wherenull('grados.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->groupby('seccions.id');

        $profesor_guias = (!empty($lapso_id)) ? $profesor_guias->where('profesor_guias.lapso_id', $lapso_id) : $profesor_guias;

        $profesor_guias = $profesor_guias->get(); //dd($profesors);

        //if ($this->id == 4) dd($profesor_guias->pluck('name'));

        return $profesor_guias;
    }

    public function getSeccions($lapso_id = null)
    {
        $seccions =
            Seccion::select('seccions.*')
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id', $this->id)
            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('seccions.id');
        $seccions = (!empty($lapso_id)) ? $seccions->where('pevaluacions.lapso_id', $lapso_id) : $seccions;
        $seccions = $seccions->get();
        return $seccions;
    }

    public function getPevaluacionsPensums($lapso_id = null)
    {
        $lapso = (empty($lapso_id)) ? Lapso::first() : Lapso::find($lapso_id);
        $pevaluacions =
            Pevaluacion::select('pevaluacions.*', 'asignaturas.name as asignatura_name', 'grados.name as grado_name')
            ->selectRaw("CONCAT(grados.id, ' - ', grados.name, ' - ', asignaturas.name) as grado_asignatura_name")
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('pestudios.id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso->id)
            ->where('pevaluacions.status_official', true)

            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')

            ->groupby('pensums.id')
            // ->groupby('pevaluacions.id')
            ->get();
        return $pevaluacions;
    }

    public function getPevaluacions($lapso_id = null)
    {
        $lapso = (empty($lapso_id)) ? Lapso::first() : Lapso::find($lapso_id);
        $pevaluacions =
            Pevaluacion::select('pevaluacions.*', 'asignaturas.name as asignatura_name', 'grados.name as grado_name')
            ->selectRaw("CONCAT(grados.id, ' - ', grados.name, ' - ', asignaturas.name) as grado_asignatura_name")
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('pestudios.id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso->id)
            ->where('pevaluacions.status_official', true)

            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')

            ->groupby('pevaluacions.id')
            // ->groupby('pevaluacions.id')
            ->get(); //dd($pevaluacions->pluck('asignatura_name'));
        //if ($this->id == 2) dd($pevaluacions->pluck('grado_asignatura_name'));
        return $pevaluacions;
    }

    public function getPevaluacionComponents($lapso_id = null)
    {
        $lapso = (empty($lapso_id)) ? Lapso::first() : Lapso::find($lapso_id);
        $pevaluacions =
            Pevaluacion::select('pevaluacions.*', 'asignaturas.name as asignatura_name', 'grados.name as grado_name')
            ->selectRaw("CONCAT(grados.id, ' - ', grados.name, ' - ', asignaturas.name, ' - ', grupo_estables.name) as grado_asignatura_name")
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('grupo_estables', 'grupo_estables.id', '=', 'pevaluacions.grupo_estable_id')

            ->where('pestudios.id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso->id)
            // ->where('pevaluacions.status_official',false)
            ->whereNotNull('pevaluacions.grupo_estable_id')

            ->whereNull('pestudios.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('pevaluacions.deleted_at')

            ->groupby('grupo_estables.id')
            ->get();

        //if ($this->id == 2) dd($pevaluacions->pluck('grado_asignatura_name'));
        return $pevaluacions;
    }

    public function getEvaluacions($lapso_id = null)
    {
        $lapso = (empty($lapso_id)) ? Lapso::first() : Lapso::find($lapso_id);

        $evaluacions =
            Evaluacion::select('evaluacions.*')
            ->selectRaw("CONCAT(grados.id, ' - ', grados.name, ' - ', asignaturas.name, ' - ', evaluacions.description) as grado_asignatura_name")
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('pestudios.id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso->id)

            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')

            ->groupby('evaluacions.id');

        // $evaluacions = ($lapso_id) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions ;

        $evaluacions = $evaluacions->get();

        return $evaluacions;
    }

    public function getBoletins($lapso_id = null)
    {
        $lapso = (empty($lapso_id)) ? Lapso::first() : Lapso::find($lapso_id);
        $boletins =
            Boletin::select('boletins.*')
            ->selectRaw("CONCAT(grados.id, ' - ', grados.name, ' - ', asignaturas.name, ' - ', evaluacions.description) as grado_asignatura_name")
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('pestudios.id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso->id)

            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')

            ->groupby('boletins.id')
            ->get();
        //if ($this->id == 2) dd($evaluacions->pluck('grado_asignatura_name'));
        return $boletins;
    }

    public function getPensumsAllSeccion()
    {
        $pensums =
            Pensum::select('pensums.*', 'asignaturas.name as asignatura_name', 'seccions.id as seccion_id')
            ->selectRaw('CONCAT(pensums.grado_id, "_",seccions.id,"_",pensums.id ) as grados_seccions_pensum')
            ->selectRaw('CONCAT(pensums.grado_id, "_",seccions.id,"_",pensums.id, grados.id, " - ", grados.name) as grados_seccions_asignatura_name')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            // ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'seccions.id', '=', 'seccions.grado_id')
            // ->leftjoin('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')

            ->where('pestudios.id', $this->id)
            ->where('pestudios.status_active', "true")
            ->where('seccions.status_active', "true")
            ->where('grados.status_active', "true")

            ->wherenull('pestudios.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            // ->groupBy('seccions.id')

            ->groupby('grados_seccions_pensum');

        $pensums = $pensums->get();

        //if ($this->id == 2) dd($pensums->pluck('grados_seccions_asignatura_name'));

        return $pensums;
    }

    public function getPensums()
    {
        $pensums =
            Pensum::select('pensums.*')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id', $this->id)
            ->wherenull('pestudios.deleted_at')
            ->groupby('pensums.id')
            ->groupby('pevaluacions.seccion_id');
        $pensums = $pensums->get(); //dd($pensums);

        return $pensums;
    }

    public function getPorcAprobados($lapso_id = null)
    {
        $boletins =
            Boletin::select('boletins.nota', 'escalas.minimo', 'escalas.maximo', 'escalas.aprobacion')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('escalas', 'escalas.id', '=', 'pevaluacions.escala_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id', $this->id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at');

        $boletins = (!empty($lapso_id)) ? $boletins->where('pevaluacions.lapso_id', $lapso_id) : $boletins;

        $boletins = $boletins->get();

        $aprobados = 0;
        foreach ($boletins as $boletin) {
            if ($boletin->nota >= $boletin->aprobacion) {
                $aprobados = $aprobados + 1;
            }
        }

        return ($boletins->IsNotEmpty()) ? round((100 * $aprobados / $boletins->count()), 2) : null;
    }

    public function getPromedio($lapso_id = null)
    {
        $count =
            Boletin::select(
                DB::raw('count(boletins.id) as value'),
                DB::raw('sum(boletins.nota) as sum_nota')
            )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id', $this->id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pestudios.id');

        $count = (!empty($lapso_id)) ? $count->where('pevaluacions.lapso_id', $lapso_id) : $count;

        $count = $count->first();

        return ($count) ? round(($count->sum_nota / $count->value), 2) : null;
    }

    public function getStatusDeleteAttribute()
    {
        return (empty($this->grados->count())) ? true : false;
    }

    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('pestudios.status_active',  $flag = 'true')->orderBy('pestudios.order', 'asc');
    }

    public function getFullNameAttribute()
    {
        return $this->code . ' ' . $this->name;
    }

    public function getEscalaAttribute()
    {
        switch ($this->id) {
            case '1':
                $escala_id = 3;
                break;
            case '2':
                $escala_id = 1;
                break;
            default:
                $escala_id = 1;
                break;
        }
        $escala = Escala::find($escala_id);
        return $escala;
    }

    public function getRgbColorAttribute()
    {
        switch ($this->color) {
            case 'primary':
                $color = '0, 123, 255';
                break;
            case 'info':
                $color = '23, 162, 184';
                break;
            default:
                $color = '0, 0, 0';
                break;
        }
        return $color;
    }

    public function getInscritos($representant_id)
    {
        $inscripcions = Pestudio::select('pestudios.name', DB::raw('count(estudiants.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('estudiants.representant_id', $representant_id)
            ->where('pestudios.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('estudiants.status_active', 'true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        // dd($inscripcions);
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function getPollAnswers($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $arr_id = $poll_main->attendees->pluck('id')->toArray(); //dd($arr_id);
        $poll_answers = collect();
        switch ($poll_main->poll_group_id) {
            case '1':
                // $poll_answers = PollAnswer::select('poll_answers.*','representants.ci_representant')
                $poll_answers = PollAnswer::select('poll_answers.*')
                    ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
                    ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                    ->join('users', 'users.id', '=', 'poll_tokens.user_id')
                    ->join('representants', 'users.id', '=', 'representants.user_id')
                    ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
                    ->where('seccions.id', '<>', 21)
                    ->where('seccions.id', '<>', 22)
                    ->where('poll_mains.id', $poll_main->id)
                    ->where('pestudios.id', $this->id)
                    ->whereIn('users.id', $arr_id)
                    ->groupBy('representants.id')
                    ->get(); //dd($poll_answers,$arr_id);
                break;

            default:
                # code...
                break;
        }

        //dd($poll_answers);

        return $poll_answers;
    }

    public function getPollTokens($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $attendees = $poll_main->attendees->pluck('id')->toArray(); //dd($arr_id);
        $poll_tokens  = collect();
        switch ($poll_main->poll_group_id) {
            case '1':
                $poll_tokens = Pestudio::select('poll_tokens.*')
                    ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
                    ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
                    ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('users', 'users.id', '=', 'representants.user_id')
                    ->join('poll_tokens', 'users.id', '=', 'poll_tokens.user_id')
                    ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                    ->where('seccions.id', '<>', 21)
                    ->where('seccions.id', '<>', 22)
                    ->where('poll_mains.id', $poll_main->id)
                    ->where('pestudios.id', $this->id)
                    ->whereIn('users.id', $attendees)
                    ->whereNull('estudiants.deleted_at')
                    ->whereNull('users.deleted_at')
                    ->whereNull('inscripcions.deleted_at')
                    ->groupBy('poll_tokens.token')
                    ->get();
                break;

            default:
                # code...
                break;
        }

        return $poll_tokens;
    }

    public function getPollGrados($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $attendees = $poll_main->attendees->pluck('id')->toArray(); //dd($arr_id);
        $grados = Grado::select('grados.*')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('users', 'users.id', '=', 'representants.user_id')
            ->join('poll_tokens', 'users.id', '=', 'poll_tokens.user_id')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->where('poll_mains.id', $poll_main->id)
            ->where('pestudios.id', $this->id)
            ->whereIn('users.id', $attendees)
            ->groupBy('grados.id');
        $grados = ($poll_main->status_exclude_last == 'true') ? $grados->where('grados.id', '<>', 11) : $grados;
        $grados = $grados->get();
        return $grados;
    }

    public function getPollSeccions($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $attendees = $poll_main->attendees->pluck('id')->toArray();
        $seccions = Seccion::select('seccions.*')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('users', 'users.id', '=', 'representants.user_id')
            ->join('poll_tokens', 'users.id', '=', 'poll_tokens.user_id')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->where('poll_mains.id', $poll_main->id)
            ->where('pestudios.id', $this->id)
            ->whereIn('users.id', $attendees)
            ->groupBy('seccions.id');
        $seccions = ($poll_main->status_exclude_last == 'true') ? $seccions->where('grados.id', '<>', 11) : $seccions;
        $seccions = $seccions->get();
        return $seccions;
    }
}
