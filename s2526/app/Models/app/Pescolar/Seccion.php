<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;
// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\app\Pescolar\Functions\Seccion\Lists;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollMain;

class Seccion extends Model
{
    use Lists;

    protected $fillable = [
        'grado_id',
        'name',
        'description',
        'amount_student',
        'observation',
        'status_active',
        'comment_final',
        'status_inscription_affects'
    ];
    const COLUMN_COMMENTS = [
        'grado_id' => 'Grado del Plan de Estudio',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'amount_student' => 'Cantidad de Estudiantes',
        'observation' => 'Observaciones',
        'status_active' => 'Estado',
        'comment_final' => 'Observaciones Resumen Final',
        'status_inscription_affects' => 'Contabiliza Inscripción'
    ];

    /*INI relaciones entre modelos*/
    public function grado()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Grado');
    }
    public function inscripcions()
    {
        return $this->hasMany('App\Models\app\Estudiante\Inscripcion');
    }
    public function prosecucions()
    {
        return $this->hasMany('App\Models\app\Estudiante\Prosecucion');
    }
    public function pevaluacions()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion');
    }
    public function profesor_guias()
    {
        return $this->hasMany('App\Models\app\Pescolar\ProfesorGuia');
    }
    /*FIN relaciones entre modelos*/

    //scope
    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('seccions.status_active', $flag);
    }

    public function getStatusDeleteAttribute()
    {
        return (empty($this->inscripcions->count()) && empty($this->profesor_guias->count())) ? true : false;
    }

    public function getCountNotas($lapso_id)
    {
        $lapso = Lapso::findOrFail($lapso_id);
        $boletin = Boletin::select('seccions.id', DB::raw('count(boletins.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('pevaluacions.seccion_id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->where('inscripcions.seccion_id', $this->id)
            ->where('estudiants.status_active', 'true')
            ->whereDate('inscripcions.created_at', '>=', $lapso->finicial)
            ->whereDate('inscripcions.created_at', '<=', $lapso->ffinal)
            ->wherenotnull('boletins.nota')
            ->wherenull('grados.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('seccions.id')
            ->first();

        return (!empty($boletin)) ? $boletin->value : 0;
    }

    public function getCountEvaluacions($lapso_id)
    {
        $evaluacion = Evaluacion::select('seccions.id', DB::raw('count(evaluacions.id) as value'))
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->where('pevaluacions.seccion_id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso_id)
            ->wherenull('pensums.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('seccions.id')
            ->first();
        // if ($lapso_id==3) {
        //     dd($lapso_id,$evaluacion);
        // }
        return (!empty($evaluacion)) ? $evaluacion->value * $this->getEstudiants($lapso_id)->count() : null;
    }

    public function getCountEvaluacionsCorte($lapso_id)
    {
        $lapso = Lapso::findOrFail($lapso_id);
        $fcorte = $lapso->date_cutnote;
        $evaluacion = Evaluacion::select('seccions.id', DB::raw('count(evaluacions.id) as value'))
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->where('pevaluacions.seccion_id', $this->id)
            ->where('pevaluacions.lapso_id', $lapso->id)
            ->where('evaluacions.fecha', '<=', $fcorte)
            ->wherenull('pensums.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('seccions.id')
            ->first();
        return (!empty($evaluacion)) ? $evaluacion->value * $this->getEstudiants($lapso_id)->count() : null;
    }

    public function getEstudiants($lapso_id = null)
    {
        // $lapso = Lapso::findOrFail($lapso_id);
        $inscripcions = Inscripcion::select('inscripcions.*')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->wherenull('inscripcions.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            ->Where('inscripcions.seccion_id', '=', $this->id)
            ->Where('estudiants.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->orderby('estudiants.ci_estudiant');
        // ->get();

        if (!empty($lapso_id)) {
            $lapso = Lapso::findOrFail($lapso_id);
            // $inscripcions = $inscripcions->whereDate('inscripcions.created_at', '>=', $lapso->finicial)->whereDate('inscripcions.created_at', '<=', $lapso->ffinal);
            $inscripcions = $inscripcions->whereDate('inscripcions.created_at', '<=', $lapso->ffinal);
        }

        $inscripcions = $inscripcions->get();

        return ($inscripcions) ? $inscripcions : 0;
    }

    public function getEstudiantsInAttribute()
    {
        $lapso = Lapso::current();
        $estudiants = Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->wherenull('inscripcions.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            ->Where('seccions.id', '=', $this->id)
            ->Where('estudiants.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->orderby('estudiants.ci_estudiant')
            ->whereDate('inscripcions.created_at', '<=', $lapso->ffinal)
            ->get();
        return ($estudiants) ? $estudiants : 0;
    }

    public function getFullNameAttribute()
    {
        return $this->grado->name . ' ' . $this->name;
    }
    public function getFullNameSmAttribute()
    {
        return  $this->grado->code . ' ' . ' Secc. ' . $this->name;
    }

    public function inscritos()
    {
        $inscripcions = Inscripcion::select('seccions.name', DB::raw('count(inscripcions.id) as value'))
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->Where('seccions.id', '=', $this->id)
            ->groupby('seccions.id')
            ->first()
            ->value;

        // dd($inscripcions);

        return ($inscripcions) ? $inscripcions : 0;
    }
    public static function getName($id)
    {
        $seccion = Seccion::Where('id', $id)->first();
        if ($seccion) {
            return $seccion->name;
        } else {
            return '...';
        }
    }

    public static function list_grado_seccion()
    {
        $seccions = Seccion::all();
        $list = collect();
        foreach ($seccions as $seccion) {
            $grado_name = ($seccion->grado) ? $seccion->grado->name : null;
            $seccion_name = $grado_name . ' ' . $seccion->name;
            $list->put($seccion_name, $seccion_name);
        }
        return $list;
    }

    public function getCountEstudiants($lapso_id = null)
    {
        $estudiants = $this->getEstudiants($lapso_id);
        return $estudiants->count();
    }

    public function getEstudiantPosicionPromedioLapso($lapso_id)
    {
        $notas = collect();
        $estudiants = $this->estudiants_in;
        foreach ($estudiants as $estudiant) {
            $nota = collect();
            // $nota_final = $estudiant->getNotaFinalLapso($lapso_id,5);
            $nota_final = $estudiant->getNotaFinalLapso($lapso_id, 8, true, false);
            $nota->put('estudiant_id', $estudiant->id);
            $nota->put('fullname', $estudiant->fullname);
            $nota->put('ci_estudiant', $estudiant->ci_estudiant);
            $nota->put('nota', $nota_final);
            $notas->push($nota);
        }
        $notas = $notas->sortByDesc('nota'); //dd($notas);
        return $notas;
    }

    public function getEstudiantsPosicionPromedioAttribute()
    {
        $notas = collect();
        $estudiants = $this->estudiants_in;
        foreach ($estudiants as $estudiant) {
            $nota = collect();
            // $nota_final = $estudiant->promedio;
            // $nota_final = $estudiant->getPromedioFinalLapso(1);
            $nota_final = $estudiant->getPromedioFinalAlternative(2);
            $nota->put('estudiant_id', $estudiant->id);
            $nota->put('fullname', $estudiant->fullname);
            $nota->put('ci_estudiant', $estudiant->ci_estudiant);
            $nota->put('nota', $nota_final);
            $notas->push($nota);
        }
        $notas = $notas->sortByDesc('nota'); //dd($notas);
        return $notas;
    }

    public function getPromedioLapso($lapso_id, $decimal = 2)
    {
        $estudiants = $this->getEstudiantPosicionPromedioLapso($lapso_id);
        $sum = null;
        foreach ($estudiants as $index => $estudiant) { //dd($estudiants,$index);
            $sum += $estudiant['nota'];
        }
        $promedio = ($sum) ? round(($sum / $estudiants->count()), 2) : null;

        return $promedio;
    }

    public function getPromedio($lapso_id = null)
    {
        $boletin =
            Boletin::select(
                DB::raw('count(boletins.id) as value'),
                DB::raw('sum(boletins.nota) as sum_nota')
            )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')

            ->where('pevaluacions.seccion_id', $this->id)
            ->where('asignaturas.enable_academic_index', 'true')
            // ->where('pevaluacions.lapso_id',$lapso_id)
            // ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pevaluacions.seccion_id');
        // ->first();

        $boletin = ($lapso_id) ? $boletin->where('pevaluacions.lapso_id', $lapso_id) : $boletin;

        $boletin = $boletin->first();

        return ($boletin) ? round(($boletin->sum_nota / $boletin->value), 2) : null;
    }

    public function getPestudioAttribute()
    {
        $pestudio = Pestudio::select('pestudios.*')
            ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->where('seccions.id', $this->id)
            ->first();
        return $pestudio;
    }

    public function getPollAnswers($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id); //dd($poll_main);
        $arr_id = $poll_main->attendees->pluck('id')->toArray();
        $poll_answers = collect();
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
            ->where('poll_mains.id', $poll_main->id)
            ->where('seccions.id', $this->id)
            ->whereIn('users.id', $arr_id)
            ->groupBy('representants.id')
            ->get();

        return $poll_answers;
    }

    public function getPollTokens($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $arr_id = $poll_main->attendees->pluck('id')->toArray();
        $poll_tokens  = collect();
        $poll_tokens = Grado::select('poll_tokens.*')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('users', 'users.id', '=', 'representants.user_id')
            ->join('poll_tokens', 'users.id', '=', 'poll_tokens.user_id')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->where('poll_mains.id', $poll_main->id)
            ->where('seccions.id', $this->id)
            ->whereIn('users.id', $arr_id)
            ->whereNull('estudiants.deleted_at')
            ->whereNull('representants.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->groupBy('poll_tokens.token')
            ->get();

        return $poll_tokens;
    }

    /////////////////////////////////////////////////////////////////////////////////

    public function getRepresentantsAttribute()
    {
        $representants =  Representant::select('representants.*')
            ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('seccions.id', '=', $this->id)
            ->Where('seccions.status_active', '=', 'true')
            ->Where('grados.status_active', '=', 'true')
            ->Where('estudiants.status_active', '=', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->where('planpagos.status_inscription_affects', 'true');

        $representants = $representants->get();

        return $representants;
    }

    public function getLateIndexAttribute()
    {
        $late_index = 0;

        $representants = $this->representants; //if ($this->id=="7") dd($representants);
        $goal = $representants->count();

        $representant_debtors = $this->representant_debtors; //if ($this->id=="7") dd($representant_debtors);
        $real = $representant_debtors->count();

        $late_index = ($goal) ? (100 * $real / $goal) : 0;

        return $late_index;
    }

    public function getRepresentantDebtorsAttribute()
    {
        $results = collect();
        $representants = $this->representants;
        foreach ($representants as $representant) {
            $ammount = round($representant->exchange_ammount_expire_bill, 2);
            if ($ammount > 0) {
                $results->push($representant);
            }
        }
        return $results;
    }
}
