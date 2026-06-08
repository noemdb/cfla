<?php
namespace App\Models\app\Pescolar;

use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Functions\Pensum\Lists;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\app\Profesor\ProfesorGestable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Pensum extends Model
{
    use SoftDeletes;
    use Lists;

    protected $fillable = [
        'pestudio_id',
        'grado_id',
        'asignatura_id',
        'status_component',
        'status_active',
        'status_active_diagnostic',
        'observations',
    ];

    const COLUMN_COMMENTS = [
        'pestudio_id'              => 'Plan Estudio',
        'grado_id'                 => 'Grado',
        'asignatura_id'            => 'Asignatura',
        'status_component'         => 'Contiene componentes de Formación?',
        'status_active'            => 'Activo',
        'status_active_diagnostic' => 'Activo para diagnostico',
        'observations'             => 'Observación',
    ];

    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
    public function grado()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Grado');
    }
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function boletins()
    {
        return $this->hasMany('App\Models\app\Estudiante\Boletin');
    }
    public function pevaluacions()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion');
    }
    public function baremos()
    {
        return $this->hasMany('App\Models\app\Pescolar\Baremo');
    }
    public function boletin_revisions()
    {
        return $this->hasMany('App\Models\app\Estudiante\BoletinRevision');
    }

    public function questions()
    {
        return $this->hasMany(DebateQuestion::class, 'pensum_id');
    }

    public function diag_questions()
    {
        return $this->hasMany(DiagQuestion::class, 'pensum_id');
    }

    /*****************************************************************************************/

    public function scopeActive($query, $flag = true)
    {
        return $query->where('pensums.status_active', $flag);
    }

    public function getFullNameAttribute()
    {
        $asignaturaName = $this->asignatura ? $this->asignatura->name : 'Asignatura Desconocida';
        $gradoName      = $this->grado ? $this->grado->name : 'Grado Desconocido';
        return "{$gradoName} - {$asignaturaName}";
    }

    public function getGrupoEstablesAttribute()
    {
        $grupo_estables = GrupoEstable::select('grupo_estables.*', 'profesors.id as profesor_id', 'profesors.name as profesor_name')
            ->join('profesor_gestables', 'grupo_estables.id', '=', 'profesor_gestables.grupo_estable_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'profesor_gestables.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('profesors', 'profesors.id', '=', 'profesor_gestables.profesor_id')

            ->where('pensums.id', $this->id)

            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->orderBy('pevaluacions.created_at', 'desc')

            ->get();
        return $grupo_estables;
    }

    public function getGrupoEstables($seccion_id = null)
    {
        $grupo_estables = GrupoEstable::select('grupo_estables.*', 'profesors.id as profesor_id', 'profesors.name as profesor_name', 'seccions.name as seccion_name')
            ->join('profesor_gestables', 'grupo_estables.id', '=', 'profesor_gestables.grupo_estable_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'profesor_gestables.pevaluacion_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('profesors', 'profesors.id', '=', 'profesor_gestables.profesor_id')

            ->where('pensums.id', $this->id)

            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->orderBy('pevaluacions.created_at', 'desc');

        $grupo_estables = ($seccion_id) ? $grupo_estables->where('seccions.id', $seccion_id) : $grupo_estables;

        $grupo_estables = $grupo_estables->get(); //dd($grupo_estables,$seccion_id);

        return $grupo_estables;
    }

    public function getProfesorTraining($seccion_id = null)
    {
        $profesors = Profesor::select('profesors.*', 'lapsos.name as lapso_name', 'seccions.name as seccion_name')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('grupo_estables', 'grupo_estables.id', '=', 'pevaluacions.grupo_estable_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')

            ->where('pensums.id', $this->id)

            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

        // ->orderBy('profesors.name','desc')
            ->groupBy('pevaluacions.id');

        $profesors = ($seccion_id) ? $profesors->where('seccions.id', $seccion_id) : $profesors;

        $profesors = $profesors->get(); //dd($profesors,$seccion_id);

        return $profesors;
    }

    public function getProfesorGestables($seccion_id = null)
    {
        $profesor_gestables = ProfesorGestable::select('profesor_gestables.*', 'lapsos.name as lapso_name', 'seccions.name as seccion_name')
            ->join('grupo_estables', 'grupo_estables.id', '=', 'profesor_gestables.grupo_estable_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'profesor_gestables.pevaluacion_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')

            ->where('pensums.id', $this->id)

            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->orderBy('profesor_gestables.created_at', 'desc');

        $profesor_gestables = ($seccion_id) ? $profesor_gestables->where('seccions.id', $seccion_id) : $profesor_gestables;

        $profesor_gestables = $profesor_gestables->get();

        return $profesor_gestables;
    }

    public function getProfesorGestablesAttribute()
    {
        $profesor_gestables = ProfesorGestable::select('profesor_gestables.*')
            ->join('grupo_estables', 'grupo_estables.id', '=', 'profesor_gestables.grupo_estable_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'profesor_gestables.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')

            ->where('pensums.id', $this->id)

            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->orderBy('profesor_gestables.created_at', 'desc')

            ->get();
        return $profesor_gestables;
    }

    public function evaluacions_corte($lapso_id, $seccion_id)
    {
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->where('pensums.id', $this->id)
            ->where('seccions.id', $seccion_id)
            ->where('lapsos.id', $lapso_id)
            ->wherenull('pevaluacions.deleted_at')
            ->orderBy('pevaluacions.created_at', 'desc')
            ->get();
        //if ($this->id == 142) dd($evaluacions);

        return $evaluacions;
    }

    public function profesor($seccion_id, $lapso_id)
    {
        $profesor = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pensums.id', $this->id)
            ->where('pevaluacions.seccion_id', $seccion_id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->wherenull('pevaluacions.deleted_at')
            ->orderBy('pevaluacions.created_at', 'desc')
            ->first();
        return $profesor;
    }

    public function getProfesorsSeccionLapso($seccion_id, $lapso_id)
    {
        $profesor = Profesor::select('profesors.*', 'grupo_estables.name as grupo_estable_name')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->leftjoin('grupo_estables', 'grupo_estables.id', '=', 'pevaluacions.grupo_estable_id')
            ->where('pensums.id', $this->id)
            ->where('seccions.id', $seccion_id)
            ->where('lapsos.id', $lapso_id)
            ->wherenull('pevaluacions.deleted_at')
            ->orderBy('pevaluacions.created_at', 'desc')
            ->get();
        return $profesor;
    }

    public function getEnableAcademicIndexAttribute()
    {
        $asignatura            = ($this->asignatura) ? $this->asignatura : null;
        $enable_academic_index = ($asignatura) ? $asignatura->enable_academic_index : false;
        return $enable_academic_index;
    }
    public function getProfesors($seccion_id)
    {
        $profesors = Profesor::select('profesors.*', 'lapsos.code as lapso_code')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->where('seccions.id', $seccion_id)
            ->where('pevaluacions.pensum_id', $this->id)
            ->whereNull('pevaluacions.deleted_at')
            ->get();
        return $profesors;
    }

    public function getPevaluacionID($lapso_id, $seccion_id)
    {
        $pevaluacion = Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.pensum_id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->where('pevaluacions.seccion_id', $seccion_id)
            ->first();

        return ($pevaluacion) ? $pevaluacion->id : null;
    }

    public function getCountPevaluacions($lapso_id = null, $seccion_id = null)
    {
        $pevaluacions =
        DB::table('pevaluacions')
            ->select('pevaluacions.*')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.pensum_id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pevaluacions.id')
            ->wherenull('evaluacions.deleted_at');

        $pevaluacions = (! empty($lapso_id)) ? $pevaluacions->where('pevaluacions.lapso_id', $lapso_id) : $pevaluacions;
        $pevaluacions = (! empty($seccion_id)) ? $pevaluacions->where('pevaluacions.seccion_id', $seccion_id) : $pevaluacions;

        $pevaluacions = $pevaluacions->get();

        return ($pevaluacions->count()) ? $pevaluacions->count() : null;
    }

    public function getProfesorsAttribute()
    {
        $profesors = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pensums.id', $this->id)
            ->GroupBy('pevaluacions.profesor_id')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->get();
        return $profesors;
    }

    public function getCountNotas($lapso_id)
    {
        $lapso   = Lapso::findOrFail($lapso_id);
        $boletin = Boletin::select('pensums.id', DB::raw('count(boletins.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('pevaluacions.pensum_id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->whereDate('inscripcions.created_at', '>=', $lapso->finicial)
            ->whereDate('inscripcions.created_at', '<=', $lapso->ffinal)
            ->where('estudiants.status_active', 'true')
            ->wherenotnull('boletins.nota')
            ->wherenull('pensums.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pevaluacions.pensum_id')
            ->first();
        return (! empty($boletin)) ? $boletin->value : 0;
    }

    public function getCountEvaluacions($lapso_id)
    {
        $total    = 0;
        $seccions = $this->grado->seccions;
        foreach ($seccions as $seccion) {
            $evaluacion = Evaluacion::select('seccions.id', DB::raw('count(evaluacions.id) as value'))
                ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
                ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
                ->join('grados', 'grados.id', '=', 'pensums.grado_id')
                ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
                ->where('pevaluacions.seccion_id', $seccion->id)
                ->where('pevaluacions.lapso_id', $lapso_id)
                ->where('pensums.id', $this->id)
                ->wherenull('pensums.deleted_at')
                ->wherenull('evaluacions.deleted_at')
                ->wherenull('pevaluacions.deleted_at')
                ->groupby('seccions.id')
                ->first();
            $evaluacions = (! empty($evaluacion)) ? $evaluacion->value * $seccion->getEstudiants($lapso_id)->count() : null;
            $total       = $total + $evaluacions;
        }

        return ($total) ? $total : null;
    }

    public function getEstudiantsInAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->join('pensums', 'grados.id', '=', 'pensums.grado_id')

            ->Where('pensums.id', '=', $this->id)

            ->wherenull('inscripcions.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')

            ->Where('estudiants.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')

            ->orderby('estudiants.ci_estudiant')
            ->get();
        // ->value;
        return ($estudiants) ? $estudiants : 0;
    }

    public function getPorcAprobados($lapso_id)
    {
        $boletin =
        Boletin::select(
            DB::raw('count(boletins.id) as value'),
            DB::raw('sum(boletins.nota) as sum_nota')
        )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pensums.id', $this->id)
        // ->where('pevaluacions.lapso_id',$lapso_id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->groupby('pensums.id');
        // ->first();

        $boletin = ($lapso_id) ? $boletin->where('pevaluacions.lapso_id', $lapso_id) : $boletin;

        $boletin = $boletin->first();

        return ($boletin) ? round(($boletin->sum_nota / $boletin->value), 2) : null;
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
            ->join('pemsuns', 'pemsuns.id', '=', 'pevaluacions.pemsuns_id')
            ->where('pemsuns.id', $this->id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pevaluacions.id')
            ->first();
        return ($count) ? round(($count->sum_nota / $count->value), 2) : null;
    }

    // public static function getValoracion($pestudio_id, $nota)
    // {
    //     $baremo = Baremo::select('baremos.valoracion', 'baremos.description')
    //         ->join('pestudios', 'pestudios.id', '=', 'baremos.pestudio_id')
    //         ->where('pestudios.id', $pestudio_id)
    //         ->Where('baremos.minimo', '<=', $nota)
    //         ->Where('baremos.maxima', '>=', $nota)
    //         ->first();
    //     return $baremo;
    // }

    public static function getValoracion($pestudio_id, $nota, $lapso_id = null)
    {
        return Baremo::getValoracion($pestudio_id, $nota, $lapso_id);
    }

    public function GetNota($estudiant_id, $seccion_id, $lapso_id, $round = 2)
    {
        $notas = Boletin::select('boletins.*')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.pensum_id', $this->id)
            ->where('pevaluacions.seccion_id', $seccion_id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->where('boletins.estudiant_id', $estudiant_id)
            ->whereNotnull('boletins.nota')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->get();

        return ($notas->IsNotEmpty()) ? round(($notas->sum('nota') / $notas->count()), $round) : null;
    }

    public static function pevaluacion_complete($pensum_id, $lapso_id)
    {
        $pensum            = Pensum::findOrFail($pensum_id);
        $count_seccion     = $pensum->grado->seccions->count();
        $count_pevaluacion = $pensum->pevaluacions->where('lapso_id', $lapso_id)->count();
        return ($count_seccion == $count_pevaluacion) ? true : false;
    }

    public static function count_evaluacion($pensum_id, $lapso_id, $seccion_id)
    {
        $count = Pevaluacion::select(DB::raw('count(evaluacions.id) as value'))
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.pensum_id', $pensum_id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->where('pevaluacions.seccion_id', $seccion_id)
            ->wherenull('evaluacions.deleted_at')
            ->groupby('pevaluacions.id')
            ->first();
        return ($count) ? $count->value : null;
    }

    public static function count_notas($pensum_id, $lapso_id, $seccion_id)
    {
        $count = Boletin::select('pevaluacions.*', DB::raw('count(evaluacions.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.pensum_id', $pensum_id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->where('pevaluacions.seccion_id', $seccion_id)
            ->whereNotnull('boletins.nota')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->groupby('pevaluacions.pensum_id')
            ->first();
        return ($count) ? $count->value : null;
    }

    public static function notas($pensum_id, $lapso_id, $seccion_id)
    {
        $notas = Boletin::select('boletins.*')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.pensum_id', $pensum_id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->where('pevaluacions.seccion_id', $seccion_id)
            ->whereNotnull('boletins.nota')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->get();
        return ($notas) ? $notas : null;
    }

    public static function p_notas_c($pensum_id, $lapso_id, $seccion_id)
    {
        $pensum         = Pensum::find($pensum_id);
        $count_eva      = $pensum->count_evaluacion($pensum->id, $lapso_id, $seccion_id);
        $count_std      = $pensum->grado->seccions->where('id', $seccion_id)->first()->estudiants_in->count();
        $count_not_goal = $count_std * $count_eva;
        $count_not      = $pensum->count_notas($pensum->id, $lapso_id, $seccion_id);
        $total          = (! empty($count_not_goal)) ? round(($count_not / $count_not_goal * 100), 1) : '';
        return ($total) ? $total : null;
    }

    public static function exist_seccion($pensum_id, $lapso_id, $seccion_id)
    {
        $pevaluacions = Pevaluacion::where('pensum_id', $pensum_id)->where('lapso_id', $lapso_id)->where('seccion_id', $seccion_id)->get();
        return ($pevaluacions->isempty()) ? false : true;
    }

    /**
     * Calculate precision percentage for multiple-choice questions in this pensum
     * Formula: (100 * correct_answers / total_answered_questions)
     * Only considers multiple-choice questions (tipo_pregunta = 'multiple')
     *
     * @return float|null Precision percentage or null if no answers found
     */
    public function getPrecisionByPensumAttribute()
    {
        // Get all diagnostic questions for this pensum that are multiple-choice
        $multipleChoiceQuestions = $this->diag_questions()
            ->where('tipo_pregunta', 'multiple')
            ->pluck('id');

        if ($multipleChoiceQuestions->isEmpty()) {
            return null;
        }

        // Get all answers for these questions
        $totalAnswers = DiagAnswer::whereIn('question_id', $multipleChoiceQuestions)
            ->count();

        if ($totalAnswers == 0) {
            return null;
        }

        // Count correct answers using the isCorrect method
        $correctAnswers = DiagAnswer::whereIn('question_id', $multipleChoiceQuestions)
            ->get()
            ->filter(function ($answer) {
                return $answer->isCorrect();
            })
            ->count();

        // Calculate precision: (100 * correct_answers / total_answered_questions)
        $precision = ($correctAnswers / $totalAnswers) * 100;

        return round($precision, 2);
    }

    /**
     * Static method to calculate precision for a specific pensum ID
     *
     * @param int $pensumId
     * @return float|null
     */
    public static function calculatePrecisionByPensum($pensumId)
    {
        $pensum = self::find($pensumId);
        return $pensum ? $pensum->precision_by_pensum : null;
    }

    public function getActivities($lapso_id = null)
    {
        $activities =
        Activity::query()
            ->select('activities.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->where('pensums.id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->where('seccions.status_active', "true")
            ->where('seccions.status_inscription_affects', 'true')
            ->where('grados.status_active', "true")
            ->groupby('pevaluacions.id');

        $activities = ($lapso_id) ? $activities->where('pevaluacions.lapso_id', $lapso_id) : $activities;

        $activities = $activities->get(); //dd($activities,$this->name,$lapso_id);

        return $activities;
    }
}
