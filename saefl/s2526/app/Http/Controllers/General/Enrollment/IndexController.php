<?php

namespace App\Http\Controllers\General\Enrollment;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Estudiante\Representant;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\Pescolar\Grado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    // public function index($token)
    // {        
    //     return view('general.enrollments.index',compact('token'));
    // }

    public function index($token)
    {   
        $status_representant = false; 
        $enrollment = New Enrollment; 
        $estudiant_index = null;  
        $representant = Representant::where('ci_representant',$token)->first();
        if ($representant) {
            $status_representant = true;
            $estudiants_formaly = Estudiant::select('estudiants.*')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'prosecucions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')

            ->where('representants.ci_representant',$token)
            ->get()
            ;

            $estudiants_enrollments = $representant->estudiants_enrollments; 
            $estudiants_enrollments_in = $representant->estudiants_enrollments_in;
            if ($estudiants_enrollments->isNotEmpty()) {
                $estudiant = $estudiants_enrollments->first();
                $estudiant_index = $estudiants_enrollments_in->count() + 1;
                
                $enrollment = New Enrollment;
                if ($estudiant) {
                    $enrollment->ci_estudiant = $estudiant->ci_estudiant;
                    $enrollment->name = $estudiant->name;
                    $enrollment->lastname = $estudiant->lastname;
                    $enrollment->gender = $estudiant->gender;
                    $enrollment->date_birth = $estudiant->date_birth;
                    $enrollment->city_birth = $estudiant->city_birth;
                    $enrollment->town_hall_birth = $estudiant->town_hall_birth;
                    $enrollment->state_birth = $estudiant->state_birth;
                    $enrollment->country_birth = $estudiant->country_birth;
                    $enrollment->dir_address = $estudiant->dir_address;
                    $enrollment->phone = $estudiant->phone;

                    $grado = $estudiant->grado;
                    $grado_next = ($grado) ? $estudiant->getGradoNext($grado->id) : null;
                    $enrollment->grado_id = ($grado_next) ? $grado_next->id : null;

                    if ($representant) {
                        $enrollment->ci_representant = $representant->ci_representant;
                        $enrollment->name_representant = $representant->name;
                        $enrollment->phone_representant = $representant->cellphone;
                        $enrollment->email_representant = $representant->email;
                    }
                }

            } else {
                $estudiant = null;
            }

        } else {
            $representant = null;
            $status_representant = false;
            $estudiant = null;
            $estudiants_formaly = collect(New Estudiant());
            $estudiants_enrollments = collect(New Estudiant);
        }

        $list_comment = Enrollment::COLUMN_COMMENTS;
        $list_potencial = Enrollment::LIST_POTENCIAL;
        $list_grado = Grado::list_pestudio_grado();
		$list_country_birth = Estudiant::list_country_birth();
		$list_relationship = ['Madre'=>'Madre','Padre'=>'Padre','Hermano(a)'=>'Hermano(a)','Abuelo(a)'=>'Abuelo(a)','Otro'=>'Otro'];
		$list_oinstitucions = Oinstitucion::list_oinstitucions()->take(1);
        $list_blood_type = Enrollment::list_blood_type();

        $compact = [
            'token',
            'list_comment',
            'list_potencial',
            'list_grado',
            'list_country_birth',
            'list_relationship',
            'list_oinstitucions',
            'status_representant',
            'representant',
            'estudiant',
            'estudiants_formaly',
            'estudiant_index',
            'enrollment',
            'list_blood_type',
        ];
        return view('general.enrollments.index',compact($compact));
    }

    public function matriculations(Request $request)
    {
        return view('general.enrollments.matriculations');
    }
    public function send(Request $request)
    {
        $token = (!empty($request->token)) ? $request->token : null ;         
        return view('general.enrollments.index',compact('token'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $enrollment = Enrollment::create($request->all());

        $messenge = trans('db_oper_result.oper_ok');
        Session::flash('operp_ok',$messenge);

        $token = (!empty($request->token)) ? $request->token : null ;

        return redirect()->route('general.enrollments.index',compact('token'));
    }

}
