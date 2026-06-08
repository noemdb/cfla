<?php

namespace App\Models\app\Profesor;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Estudiant;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinAjuste;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\Asignatura;
use App\Models\app\Pescolar\Escala;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Pevaluacion extends Model
{
    use SoftDeletes;
    protected $fillable = ['profesor_id', 'lapso_id', 'seccion_id', 'pensum_id', 'grupo_estable_id', 'status_baremo', 'status_official', 'status_note_report', 'nota_type', 'escala_id', 'objetivo', 'description', 'observations', 'category', 'deleted_at'];

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'lapso_id' => 'Momento',
        'seccion_id' => 'Sección',
        'pensum_id' => 'Área de Formación',
        'grupo_estable_id' => 'Grupo Estable',
        'status_baremo' => 'Baremo',
        'status_official' => 'En documentos oficiales',
        'nota_type' => 'Tipo de noata',
        'escala_id' => 'Escala',
        'objetivo' => 'Objetivo',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'category' => 'Category',
        'deleted_at' => 'Fecha de Eliminación',
        'grado_id' => 'Grado/Año',
        'pestudio_id' => 'Plan Estudio',
        'status_note_report' => 'En Informe de Notas',
    ];

    protected $dates = ['deleted_at'];

    public function pensum()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pensum');
    }
    public function seccion()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Seccion');
    }
    public function profesor()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Profesor');
    }
    public function lapso()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Lapso');
    }

    public function grupo_estable()
    {
        return $this->belongsTo(GrupoEstable::class, 'grupo_estable_id');
    }
    public function evaluacions()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion\Evaluacion');
    }
    public function activities()
    {
        return $this->hasMany('App\Models\app\Profesor\Activity');
    }

    /**
     * Obtener achievements con carga eager loading optimizada
     */
    public function achievements()
    {
        return $this->hasManyThrough(
            'App\Models\app\Profesor\Achievement', // Modelo final
            'App\Models\app\Profesor\Activity',    // Modelo intermedio
            'pevaluacion_id', // FK en activities table
            'activity_id',    // FK en achievements table  
            'id',             // PK en pevaluacions table
            'id'              // PK en activities table
        );
    }
    
    public function escala()
    {
        return $this->belongsTo('App\Models\app\Profesor\Pevaluacion\Escala');
    }
    public function boletin_ajustes()
    {
        return $this->hasMany('App\Models\app\Estudiante\BoletinAjuste');
    }
    public function profesor_gestables()
    {
        return $this->hasMany('App\Models\app\Profesor\ProfesorGestable');
    }

    public function eifinalks()
    {
        return $this->hasMany(Eifinalk::class);
    }


    public function debates()
    {
        return $this->hasMany(Debate::class, 'pevaluacion_id');
    }




    public function getAllQuestions()
    {
        return DebateQuestion::whereHas('debate', function ($query) {
            $query->where('pevaluacion_id', $this->id);
        })->get();
    }

    public function getQuestionsAttribute()
    {
        if (!$this->relationLoaded('seccion.grado.debates.questions')) {
            $this->load('seccion.grado.debates.questions');
        }

        return $this->seccion->grado->debates->flatMap(function ($debate) {
            return $debate->questions;
        });
    }

    public function getLessonsAttribute()
    {
        $lessons = Lesson::query()
            ->select('lessons.*')
            ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->get();
        return $lessons;
    }

    public function getLessonsForProfesor($profesor_id)
    {
        $lessons = Lesson::select('lessons.*')
            ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->where('pevaluacions.profesor_id', $profesor_id)
            ->get();
        return $lessons;
    }

    public static function getPevaluacionsManagerId($manager_id)
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.manager_id', $manager_id)
            ->get();
        return $pevaluacions;
    }

    public function getPestudioAttribute()
    {
        $pestudio = Pestudio::select('pestudios.*')
            ->join('pensums', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->first();
        return $pestudio;
    }

    public function getFullNameAttribute()
    {
        $lapso_code_sm = ($this->lapso) ? $this->lapso->code_sm : null;
        $grado_code = ($this->grado) ? $this->grado->code : null;
        $seccion_name = ($this->seccion) ? $this->seccion->name : null;
        $profesor_sm_name = ($this->profesor) ? $this->profesor->sm_name : null;
        return strtoupper(" [{$this->id}] {$this->description} {$grado_code} {$seccion_name} {$lapso_code_sm} - {$profesor_sm_name} ");
    }

    public function getShortNameAttribute()
    {
        $lapso_code_sm = ($this->lapso) ? $this->lapso->code_sm : null;
        $grado_code = ($this->grado) ? $this->grado->code : null;
        $seccion_name = ($this->seccion) ? $this->seccion->name : null;
        $profesor_sm_name = ($this->profesor) ? $this->profesor->sm_name : null;
        return strtoupper(" {$this->description} {$grado_code} {$seccion_name} {$lapso_code_sm}");
    }

    public function getMicroNameAttribute()
    {
        $lapso_code_sm = ($this->lapso) ? $this->lapso->code_sm : null;
        $grado_code = ($this->grado) ? $this->grado->code : null;
        $seccion_name = ($this->seccion) ? $this->seccion->name : null;
        $profesor_sm_name = ($this->profesor) ? $this->profesor->sm_name : null;
        $asignatura = ($this->pensum) ? $this->pensum->asignatura->name : null;
        return strtoupper(" {$this->asignatura->code} {$grado_code} {$seccion_name} {$lapso_code_sm}");
    }

    public function getAsignaturaNameAttribute()
    {
        $lapso_code_sm = ($this->lapso) ? $this->lapso->code_sm : null;
        $grado_code = ($this->grado) ? $this->grado->code : null;
        $seccion_name = ($this->seccion) ? $this->seccion->name : null;
        $asignatura = ($this->pensum) ? $this->pensum->asignatura->name : null;
        return strtoupper("[{$grado_code} {$seccion_name}] - {$asignatura}");
    }

    public function getAsignaturaNameSmAttribute()
    {
        $grado_code = ($this->grado) ? $this->grado->code : null;
        $asignatura = ($this->pensum) ? $this->pensum->asignatura->name : null;
        return strtoupper("{$grado_code} {$asignatura}");
    }

    public function getFullSeccionAttribute()
    {
        $lapso_code_sm = ($this->lapso) ? $this->lapso->code_sm : null;
        $grado_code = ($this->grado) ? $this->grado->code : null;
        $seccion_name = ($this->seccion) ? $this->seccion->name : null;
        return strtoupper(" {$grado_code} {$seccion_name}");
    }

    public function getGrupoEstablesAttribute()
    {
        $grupo_estables = GrupoEstable::select('grupo_estables.*')
            ->join('profesor_gestables', 'grupo_estables.id', '=', 'profesor_gestables.grupo_estable_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'profesor_gestables.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->get();
        return $grupo_estables;
    }

    public function getEvaluacionGestables($profesor_gestable_id)
    {
        $evaluacion_gestables = Evaluacion::select('evaluacions.*')
            ->join('evaluacion_gestables', 'evaluacions.id', '=', 'evaluacion_gestables.evaluacion_id')
            ->join('profesor_gestables', 'profesor_gestables.id', '=', 'evaluacion_gestables.profesor_gestable_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'profesor_gestables.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->where('profesor_gestables.profesor_id', $profesor_gestable_id)
            ->get();
        return $evaluacion_gestables;
    }

    //******************* PROFESOR *********************************** */
    public static function count_evaluacion_prof_lapso($profesor_id, $lapso_id)
    {
        // dd($this,$lapso_id);
        $count = Pevaluacion::select(DB::raw('count(evaluacions.id) as value'))
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.profesor_id', $profesor_id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->wherenull('evaluacions.deleted_at')
            ->groupby('pevaluacions.lapso_id')
            ->first();
        return ($count) ? $count->value : 0;
    }

    public function getGrupoEstables($profesor_id)
    {
        $grupo_estables = GrupoEstable::select('grupo_estables.*')
            ->join('profesor_gestables', 'grupo_estables.id', '=', 'profesor_gestables.grupo_estable_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'profesor_gestables.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->where('profesor_gestables.profesor_id', $profesor_id)
            ->whereNull('pevaluacions.deleted_at')
            ->get();
        return $grupo_estables;
    }

    //******************* CONTRO DE ESTUDIOS ************************ */
    public function getStatusCargaNotasAttribute()
    {
        $fecha = Carbon::now()->format('Y-m-d');
        $lapso = Lapso::find($this->lapso_id);
        return ($fecha >= $lapso->finicial && $fecha <= $lapso->ffinal) ? true : false;
    }

    public function getAjuste($estudiant_id)
    {
        $boletin_ajuste =
            BoletinAjuste::where('pevaluacion_id', $this->id)
            ->where('estudiant_id', $estudiant_id)
            ->first();
        return ($boletin_ajuste) ? $boletin_ajuste : null;
    }

    public function getPromedioAttribute()
    {
        $count =
            Boletin::select(
                DB::raw('count(boletins.id) as value'),
                DB::raw('sum(boletins.nota) as sum_nota')
            )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pevaluacions.id')
            ->first();
        return ($count) ? round(($count->sum_nota / $count->value), 2) : null;
    }

    public function getGoalNotasCargadasAttribute()
    {
        $seccions = Seccion::select('seccions.id', DB::raw('count(evaluacions.id) as value'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('seccions.id')
            ->get();
        $total = 0;
        foreach ($seccions as $seccion) {
            $total = $total + $seccion->value * $seccion->estudiants_in->count();
        }
        return ($total) ? $total : 0;
    }
    public function getRealNotasCargadasAttribute()
    {
        $count = Boletin::select(DB::raw('count(boletins.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('pevaluacions.id', $this->id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pevaluacions.id')
            // ->groupby('estudiants.id')
            // ->groupby('evaluacions.id')
            ->first();
        return ($count) ? $count->value : 0;
    }

    public function goal_notas_load($lapso_id)
    {
        $seccions = Seccion::select('seccions.id', DB::raw('count(evaluacions.id) as value'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('seccions.id')
            ->get();
        $total = 0;
        foreach ($seccions as $seccion) {
            $total = $total + $seccion->value * $seccion->estudiants_in->count();
        }
        return ($total) ? $total : 0;
    }

    public function count_evaluacion_lapso($lapso_id)
    {
        $count = Pevaluacion::select(DB::raw('count(evaluacions.id) as value'))
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->wherenull('evaluacions.deleted_at')
            ->groupby('pevaluacions.id')
            ->first();
        return ($count) ? $count->value : null;
    }

    public static function count_evaluacion($pensum_id, $lapso_id)
    {
        $count = Pevaluacion::select(DB::raw('count(evaluacions.id) as value'))
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.pensum_id', $pensum_id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->wherenull('evaluacions.deleted_at')
            ->groupby('pevaluacions.id')
            ->first();
        return ($count) ? $count->value : null;
    }

    public function getNotaTotalAttribute()
    {
        $nota_total = Evaluacion::select('evaluacions.id', 'escalas.*')
            ->join('escalas', 'escalas.id', '=', 'evaluacions.escala_id')
            ->where('evaluacions.pevaluacion_id', $this->id)
            ->sum('escalas.maximo');
        return $nota_total;
    }

    public function getStatusEvaCompleteAttribute()
    {
        // $nota_max = $this->escala->maximo;
        $nota_max = ($this->escala) ? $this->escala->maximo : 0;

        $nota_total = $this->nota_total;

        return ($nota_total == $nota_max && $this->nota_type == 'ACUMULATIVA') ? true : false;
    }

    public function getAsignaturaAttribute()
    {
        $asignatura = Asignatura::select('asignaturas.*')
            ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.id', $this->id)
            ->first();
        return $asignatura;
    }

    public function getGradoAttribute()
    {
        $grado = Grado::select('grados.*')
            ->join('pensums', 'grados.id', '=', 'pensums.grado_id')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->wherenull('pevaluacions.deleted_at')
            ->where('pevaluacions.id', $this->id)
            ->first();
        return $grado;
    }

    public function getEstudiantsAttribute()
    {
        $lapso = Lapso::findOrFail($this->lapso_id);
        $estudiants = Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->wherenull('inscripcions.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            ->Where('inscripcions.seccion_id', '=', $this->seccion_id)
            // ->whereDate('inscripcions.created_at', '>=', $lapso->finicial)
            ->whereDate('inscripcions.created_at', '<=', $lapso->ffinal)
            ->Where('estudiants.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->orderby('estudiants.ci_estudiant');

        $estudiants = $estudiants->get();
        return $estudiants;
    }

    public static function pevalaucion_tipo_list()
    {
        return array('PROMEDIADA' => 'PROMEDIADA');
    }

    public static function list_pevaluacion($profesor_id)
    {
        return self::select('pevaluacions.id')
            ->select('pevaluacions.id')
            ->selectRaw("CONCAT(lapsos.name, ' | ',grados.name, ' ', seccions.name, ' | ',  asignaturas.name) as pname")
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->where('pevaluacions.profesor_id', $profesor_id)
            ->whereNull('pevaluacions.deleted_at')
            ->orderBy('asignaturas.name')
            ->pluck('pname', 'id');
    }

    public function pevaluacion_list_nota()
    {
        $escala = Escala::select('escalas.*')
            ->join('pevaluacions', 'escalas.id', '=', 'pevaluacions.escala_id')
            ->where('pevaluacions.id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->first();
        $minimo = ($escala) ? $escala->minimo : null;
        $maximo = ($escala) ? $escala->maximo : null;
        // $list_nota[] = null;
        $list_nota[0] = 'I';
        for ($i = $minimo; $i <= $maximo; $i++) {
            $list_nota[$i] = $i;
        } //dd($list_nota);
        return $list_nota;
    }


    public static function getPevaluacionsForGrado($grado_id = null)
    {
        $lapso = Lapso::current();
        $pevaluacions =
            Pevaluacion::query()
            ->select('pevaluacions.*', 'asignaturas.name as asignatura_name', 'grados.name as grado_name')
            ->selectRaw("CONCAT(lapsos.name, ' || ',grados.name, ' || ', seccions.name, ' || ',  asignaturas.name) as full_name")
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->where('grados.id', $grado_id)
            ->where('pevaluacions.lapso_id', $lapso->id)
            ->wherenull('pevaluacions.deleted_at')
            ->where('seccions.status_active', "true")
            ->where('seccions.status_inscription_affects', 'true')
            ->where('grados.status_active', "true")
            ->groupby('pevaluacions.id');

        $pevaluacions = $pevaluacions->get();

        return $pevaluacions;
    }
}
