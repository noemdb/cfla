<?php

namespace App\Models\app\Pescolar;

use App\Models\app\Educational\Debate;
use App\Models\app\Enrollment\CatchmentGroup;
use Illuminate\Database\Eloquent\Model;
// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\Pescolar\Pestudio;
// use App\Models\app\Pescolar\Grado;
// use App\Models\app\Pescolar\Seccion;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Pescolar\Functions\Grado\Lists;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;

use App\Models\app\Pescolar\Functions\Grado\Inscripcions;
use App\Models\app\Pescolar\Functions\Grado\Preinscripcions;
use App\Models\app\Pescolar\Functions\Grado\Indicators;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollMain;
use App\Models\app\SocialAction\CommunityAction;

class Grado extends Model
{
    use SoftDeletes;
    use Lists;
    use Inscripcions;
    use Preinscripcions;
    use Indicators;

    protected $fillable = [
        'pestudio_id','name','code','code_sm','description','status_active','hour_social','total_hour_social','order'
    ];

    const COLUMN_COMMENTS = [
        'pestudio_id' => 'Plan Estudio',
        'name' => 'Nombre',
        'code' => 'Código',
        'code_sm' => 'Código reducido',
        'description' => 'Descripción',
        'status_active' => 'Estado',
        'hour_social' => 'Horas comunitarias',
        'total_hour_social' => 'Horas comunitarias totales',
        'order' => 'Orden de Presentación',
    ];

    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
    public function seccions()
    {
        return $this->hasMany('App\Models\app\Pescolar\Seccion');
    }
    public function pensums()
    {
        return $this->hasMany(Pensum::class);
    }
    public function profesor_guias()
    {
        return $this->hasMany('App\Models\app\Pescolar\ProfesorGuia');
    }

    public function debates()
    {
        return $this->hasMany(Debate::class);
    }

    public function community_actions()
    {
        return $this->hasMany(CommunityAction::class,'grado_id');
    }

    /**
     * Get the active sections for the grade.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function activeSeccions()
    {
        return $this->seccions()->where('status_active', true)->get();
    }


    public function catchment_groups() { return $this->hasMany(CatchmentGroup::class,'grado_id'); }

    //scope
    public function scopeActive($query, $flag) {
        return $query->where('status_active', $flag);
    }

    public function getNumberAttribute()
    {
        return ($this->code_sm) ? $this->code_sm[0] : null;
    }

    public function getShortNameAttribute()
    {
        $arr = explode(' ',$this->name);
        return (count($arr)) ? $arr[0] : null ;
    }

    public function getStatusDeleteAttribute()
    {
        return ( empty($this->seccions->count()) ) ? true : false ;
    }

    public function getSeccionsActive()
    {
        return $this->seccions->where('status_active','true');
    }

    public function getSeccionsActiveInscriptionAffect()
    {
        return $this->seccions->where('status_active','true')->where('status_inscription_affects','true');
    }

    public function getEscalaAttribute()
    {
        $pestudio = $this->pestudio;
        return ($pestudio) ? $pestudio->escala : null ;
    }

    public function getAsignaturas($enable_academic_index = 'true')
    {
        $pensums = Pensum::select('pensums.*')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            // ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->Where('pensums.grado_id', '=', $this->id)
            ->where('asignaturas.enable_academic_index',$enable_academic_index )
            ->wherenull('asignaturas.deleted_at')
            ->OrderBy('asignaturas.order')
            ->get();
        return ($pensums) ? $pensums : [];
    }

    public function getPensumSubAreas($lapso_id)
    {
        $pensums = Pensum::select('pensums.*','grupo_estables.name as grupo_estable_name')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->leftjoin('grupo_estables', 'grupo_estables.id', '=', 'pevaluacions.grupo_estable_id')
            ->Where('grados.id',$this->id)
            ->Where('pevaluacions.lapso_id',$lapso_id)
            // ->where('asignaturas.enable_academic_index','false' )
            ->where('grados.status_active','true' )
            ->whereNotNull('pevaluacions.grupo_estable_id')
            ->whereNull('grados.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('asignaturas.deleted_at')
            ->OrderBy('grupo_estables.name','desc')
            ->groupBy('grupo_estables.id')
            ->get(); //dd($pensums);
        return $pensums;
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
            ->where('pensums.grado_id',$this->id)
            // ->where('pevaluacions.lapso_id',$lapso_id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->groupby('pensums.grado_id');
            // ->first();

        $boletin = ($lapso_id) ? $boletin->where('pevaluacions.lapso_id',$lapso_id) : $boletin;

        $boletin = $boletin->first();

        return ($boletin) ? round(( $boletin->sum_nota / $boletin->value ), 2) : null;

    }

    public function getCountNotas($lapso_id)
    {
        $total = 0;
        $seccions = $this->seccions;
        foreach ($seccions as $seccion) {
            $total = $total + $seccion->getCountNotas($lapso_id);
        }
        return ($total) ? $total:null;
    }

    public function getCountEvaluacions($lapso_id)
    {
        $total = 0;
        $seccions = $this->seccions;
        foreach ($seccions as $seccion) {
            $total = $total + $seccion->getCountEvaluacions($lapso_id);
        }
        return ($total) ? $total:null;
    }

    public function getCountEstudiantsAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->Where('grados.id', '=', $this->id)
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            ->get();
            // ->value;
        return ($estudiants) ? $estudiants : [];
    }


    public function getEstudiantsAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*',DB::raw("CONCAT(estudiants.ci_estudiant,' - ',estudiants.name, ' ',estudiants.lastname) as ci_fullname"))
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->Where('grados.id', '=', $this->id)
            ->Where('estudiants.status_active', '=', 'true')
            ->get();
        return ($estudiants) ? $estudiants : 0;
    }

    public function getEstudiantsLapsos($lapso_id)
    {
        $lapso = Lapso::findOrFail($lapso_id);
        $estudiants = Estudiant::select('estudiants.*',DB::raw("CONCAT(estudiants.ci_estudiant,' - ',estudiants.name, ' ',estudiants.lastname) as ci_fullname"))
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->Where('grados.id', '=', $this->id)
            //->WhereDate('inscripcions.created_at', '>=', $lapso->finicial)
            ->WhereDate('inscripcions.created_at', '<=', $lapso->ffinal)
            ->Where('estudiants.status_active', '=', 'true')
            ->get();
        return $estudiants;
    }

    /*************************************************************************/
        public function getEvaluacions($lapso_id=null)
        {
            $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')

            ->where('grados.id',$this->id)

            ->wherenull('pensums.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at');

            $evaluacions = ($lapso_id) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions ;

            $evaluacions = $evaluacions->get();

            return $evaluacions;
        }

        public function getEstudiants($lapso_id=null)
        {
            $estudiants = Estudiant::select('estudiants.*',DB::raw("CONCAT(estudiants.ci_estudiant,' - ',estudiants.name, ' ',estudiants.lastname) as ci_fullname"))
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
                ->join('grados', 'seccions.grado_id', '=', 'grados.id')
                ->Where('grados.id', '=', $this->id)
                ->Where('estudiants.status_active', '=', 'true')
                ->wherenull('inscripcions.deleted_at');

            if (!empty($lapso_id)) {
                $lapso = Lapso::findOrFail($lapso_id);
                $estudiants = $estudiants->whereDate('inscripcions.created_at', '<=', $lapso->ffinal);
            }

            $estudiants = $estudiants->get();

            return ($estudiants) ? $estudiants : 0;
        }

    /*************************************************************************/

    public static function getCountEstudiants($id)
    {
        $estudiants = Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->Where('grados.id', '=', $id)
            ->get();
        return ($estudiants) ? $estudiants : 0;
    }

    public function getRepresentantsAttribute()
    {
        return Representant::select('representants.*')
            ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->Where('grados.id', '=', $this->id)
            ->Where('estudiants.status_active', '=', 'true')
            ->Where('seccions.status_active', '=', 'true')
            ->Where('grados.status_active', '=', 'true')
            ->get();
    }

    public function getLateIndexAttribute()
    {
        $late_index = 0;

        $representants = $this->representants;
        $goal=$representants->count();

        $representant_debtors = $this->representant_debtors;
        $real = $representant_debtors->count();

        $late_index = ($goal) ? (100 * $real / $goal) : 0;

        return $late_index;
    }

    public function getRepresentantDebtorsAttribute()
    {
        $results = collect();
        $representants = $this->representants;
        foreach ($representants as $representant) {
            $ammount = round($representant->exchange_ammount_expire_bill,2);
            if ($ammount > 0) {
                $results->push($representant);
            }
        }
        return $results;
    }



    /*
        --indigo: #007bff;
        --purple: #6f42c1;
        --pink: #e83e8c;
        --red: #dc3545;
        --orange: #fd7e14;
        --yellow: #ffc107;
        --green: #28a745;
        --teal: #20c997;
        --cyan: #17a2b8;
        --white: #fff;
        --gray: #6c757d;
        --gray-dark: #343a40;
        --primary: #007bff;
        --secondary: #6c757d;
        --success: #28a745;
        --info: #17a2b8;
        --warning: #ffc107;
        --danger: #dc3545;
        --light: #f8f9fa;
        --dark: #343a40;
    */

    //getattribute
    public function getColorAttribute()
    {
        switch ($this->id) {
            case '1': return 'indigo'; break;
            case '2': return 'purple'; break;
            case '3': return 'pink'; break;
            case '4': return 'red'; break;
            case '5': return 'orange'; break;
            case '6': return 'yellow'; break;
            case '7': return 'green'; break;
            case '8': return 'teal'; break;
            case '9': return 'cyan'; break;
            case '10': return 'success'; break;
            case '11': return 'info'; break;
            case '12': return 'green'; break;
            case '13': return 'teal'; break;
            case '14': return 'cyan'; break;
            case '15': return 'success'; break;
            case '16': return 'info'; break;
            default: return 'dark'; break;
        }
    }

    //getattribute
    public function getClassCardColorAttribute()
    {
        switch ($this->id) {
            case '1': return 'bd-callout bd-callout-indigo'; break;
            case '2': return 'bd-callout bd-callout-purple'; break;
            case '3': return 'bd-callout bd-callout-pink'; break;
            case '4': return 'bd-callout bd-callout-red'; break;
            case '5': return 'bd-callout bd-callout-orange'; break;
            case '6': return 'bd-callout bd-callout-yellow'; break;
            case '7': return 'bd-callout bd-callout-green'; break;
            case '8': return 'bd-callout bd-callout-teal'; break;
            case '9': return 'bd-callout bd-callout-cyan'; break;
            case '10': return 'bd-callout bd-callout-success'; break;
            case '11': return 'bd-callout bd-callout-info'; break;
            case '12': return 'bd-callout bd-callout-green'; break;
            case '13': return 'bd-callout bd-callout-teal'; break;
            case '14': return 'bd-callout bd-callout-cyan'; break;
            case '15': return 'bd-callout bd-callout-success'; break;
            case '16': return 'bd-callout bd-callout-info'; break;
            default: return 'dark'; break;
        }
    }
    //getattribute
    public function getClassTextColorAttribute()
    {
        switch ($this->id) {
            case '1': return 'bd-callout-text-indigo'; break;
            case '2': return 'bd-callout-text-purple'; break;
            case '3': return 'bd-callout-text-pink'; break;
            case '4': return 'bd-callout-text-red'; break;
            case '5': return 'bd-callout-text-orange'; break;
            case '6': return 'bd-callout-text-yellow'; break;
            case '7': return 'bd-callout-text-green'; break;
            case '8': return 'bd-callout-text-teal'; break;
            case '9': return 'bd-callout-text-cyan'; break;
            case '10': return 'bd-callout-text-success'; break;
            case '11': return 'bd-callout-text-info'; break;
            case '12': return 'bd-callout-text-green'; break;
            case '13': return 'bd-callout-text-teal'; break;
            case '14': return 'bd-callout-text-cyan'; break;
            case '15': return 'bd-callout-text-success'; break;
            case '16': return 'bd-callout-text-info'; break;
            default: return 'bd-callout-text-dark'; break;
        }
    }

    public function getEstudiantPosicionPromedioLapso($lapso_id)
    {
        $notas = collect();
        $estudiants = $this->getEstudiantsLapsos($lapso_id);
        foreach ( $estudiants as $estudiant ) {
            $nota = collect();
            $nota_final = $estudiant->getNotaFinalLapso($lapso_id,2);
            $nota->put('estudiant_id',$estudiant->id);
            $nota->put('nota',$nota_final);
            $notas->push($nota);
        }
        $notas = $notas->sortByDesc('nota'); //dd($notas);
        return $notas;
    }

    public function getPollAnswers($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id); //dd($poll_main);
        $arr_id = $poll_main->attendees->pluck('id')->toArray(); //dd($arr_id);
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
            ->where('poll_mains.id',$poll_main->id)
            ->where('grados.id',$this->id)
            ->whereIn('users.id',$arr_id)
            ->groupBy('representants.id')
            ->get();

        return $poll_answers;
    }

    public function getPollTokens($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $arr_id = $poll_main->attendees->pluck('id')->toArray();
        $poll_tokens = Grado::select('poll_tokens.*')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('users', 'users.id', '=', 'representants.user_id')
            ->join('poll_tokens', 'users.id', '=', 'poll_tokens.user_id')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->where('poll_mains.id',$poll_main->id)
            ->where('grados.id',$this->id)
            ->whereIn('users.id',$arr_id)
            ->whereNull('estudiants.deleted_at')
            ->whereNull('representants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->groupBy('poll_tokens.token')
            ->get();

        return $poll_tokens;
    }

}
