<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Prosecucion;
use App\Models\app\Pescolar\Seccion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CatchmentFixDB {    

    public static function importCatchment()
    {  
        $datas = collect();      
        $catchments = Catchment::select('catchments.*')
        ->join('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->where('catchment_interviews.accepted',true)
        ->get();

        foreach ($catchments as $item) {
            $data = collect();  
            $representant = Representant::where('ci_representant',$item->representant_ci)->first();
            if (! $representant) {
                $arr = [
                    'ci_representant'=>$item->representant_ci,
                    'name'=>$item->representant_lastname.' '.$item->representant_name,
                    'phone'=>$item->representant_phone,
                    'cellphone'=>$item->representant_phone,
                    'email'=>$item->email,
                ];
                $representant = Representant::create($arr);
                $data->put('representant',$representant);
            }
            $user_id = $representant->setCreateUserGetId();

            $estudiant = Estudiant::where('lastname',$item->lastname)->where('name',$item->firstname)->where('representant_id',$representant->id)->first();
            if (!$estudiant) {
                $ci_estudiant = Carbon::now()->timestamp;
                $estudiant = Estudiant::create([
                    'planpago_id'=>1,//ninguno
                    'ci_estudiant'=> $ci_estudiant,
                    'lastname'=>$item->lastname,
                    'name'=>$item->firstname,
                    'gender'=>$item->gender,
                    'date_birth'=>$item->date_birth,
                    'country_birth'=>$item->country_foreign,
                    'dir_address'=>$item->direction,
                    'representant_id'=>$representant->id,
                    'status_active'=>'true',
                    ]
                );
                $data->put('estudiant',$estudiant);
            }
            $user_id = $estudiant->setCreateUserGetId();

            $grado = Grado::find($item->grade);
            if ($grado) {
                $seccion = Seccion::where('grado_id',$grado->id)->where('name','U')->first();
                if ($seccion) {
                    $inscripcion = Inscripcion::where('estudiant_id',$estudiant->id)->first();
                    if (! $inscripcion) {
                        $inscripcion = Inscripcion::create([
                            'tipo_id'=>1,
                            'seccion_id'=>$seccion->id,
                            'estudiant_id'=>$estudiant->id,
                            'escolaridad_id'=>1,
                            'programacion_id'=>1,
                            'grupo_estable_id'=>null,
                            'observations'=>'Nuevo ingreso, asignado a la sección U',
                        ]);
                        $data->put('inscripcion',$inscripcion);
                    }

                    if ($inscripcion) {
                        $prosecucion = Prosecucion::where('estudiant_id',$inscripcion->estudiant_id)->first();
                        if (!$prosecucion) {
                            $prosecucion = Prosecucion::create([
                                'seccion_id'=>$seccion->id,
                                'estudiant_id'=>$estudiant->id,
                                'observations'=>'Nuevo ingreso, asignado a la sección U',
                            ]);
                        }
                    }
                }
            }
            $datas->push($data); //dd($datas);
        }
        //dd($datas);
    }

    public static function generate_token_accepted()
    {
        $catchment_interviews = CatchmentInterview::getAccepteds();
        $datas = collect();
        foreach ($catchment_interviews as $item) {
            $catchment_interview = CatchmentInterview::find($item->id);
            if (empty($catchment_interview->token)) {
                $byte = random_bytes(45);
                if (isset($byte) && strlen($byte) == 45) {
                    $token = substr(str_replace(['+', '/', '=', '&','%'], '', bcrypt($byte)), 0, 64);
                    $catchment_interview->token = $token ;
                    $catchment_interview->save();
                    $datas->push($catchment_interview);
                }
                sleep(5);
            }
        }

        $tokens = CatchmentInterview::whereNotNull('token')->get();

        dd(CatchmentInterview::getAccepteds(),$tokens,$datas);
    }

    public static function importCatchmentTwo()
    {  
        $datas = collect();      
        $catchments = DB::connection('s2324')
        // $catchments = DB::connection('mysql')
        ->table('catchments')
        ->select('catchments.*')
        ->join('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->where('catchment_interviews.accepted',true)
        ->get(); //dd($catchments);

        foreach ($catchments as $item) {
            $data = collect();  
            $representant = Representant::where('ci_representant',$item->representant_ci)->first();
            if (! $representant) {
                $arr = [
                    'ci_representant'=>$item->representant_ci,
                    'name'=>$item->representant_lastname.' '.$item->representant_name,
                    'phone'=>$item->representant_phone,
                    'cellphone'=>$item->representant_phone,
                    'email'=>$item->email,
                ];
                $representant = Representant::create($arr);
                $data->put('representant',$representant);
            } //dd($representant);
            $user_id = $representant->setCreateUserGetId(); //dd($representant_id);

            $estudiant = Estudiant::where('lastname',$item->lastname)->where('name',$item->firstname)->where('representant_id',$representant->id)->first();
            if (!$estudiant) {

                // $ci_estudiant = Str::replace(['-', ':', ' '], '', $item->created_at);
                $ci_estudiant = Carbon::now()->timestamp;
                $estudiant = Estudiant::create([
                    'planpago_id'=>1,//ninguno
                    'ci_estudiant'=> $ci_estudiant,
                    'lastname'=>$item->lastname,
                    'name'=>$item->firstname,
                    'gender'=>$item->gender,
                    'date_birth'=>$item->date_birth,
                    'country_birth'=>$item->country_foreign,
                    'dir_address'=>$item->direction,
                    'representant_id'=>$representant->id,
                    'status_active'=>'true',
                    ]
                ); //dd($estudiant);
                $data->put('estudiant',$estudiant);
            }
            $user_id = $estudiant->setCreateUserGetId();

            $grado = Grado::find($item->grade); //dd($grado);
            if ($grado) {
                $seccion = Seccion::where('grado_id',$grado->id)->where('name','U')->first(); //dd($grado->id,$seccion);
                if ($seccion) {
                    $inscripcion = Inscripcion::where('estudiant_id',$estudiant->id)->first();
                    if (! $inscripcion) {
                        $inscripcion = Inscripcion::create([
                            'tipo_id'=>1,
                            'seccion_id'=>$seccion->id,
                            'estudiant_id'=>$estudiant->id,
                            'escolaridad_id'=>1,
                            'programacion_id'=>1,
                            'grupo_estable_id'=>null,
                            'observations'=>'Nuevo ingreso, asignado a la sección U',
                        ]);
                        $data->put('inscripcion',$inscripcion);
                    }

                    if ($inscripcion) {
                        $prosecucion = Prosecucion::where('estudiant_id',$inscripcion->estudiant_id)->first();
                        if (!$prosecucion) {
                            $prosecucion = Prosecucion::create([
                                'seccion_id'=>$seccion->id,
                                'estudiant_id'=>$estudiant->id,
                                'observations'=>'Nuevo ingreso, asignado a la sección U',
                            ]);
                        }
                    }
                }
            }
            $datas->push($data); //dd($datas);
        }
        //dd($datas);
    }

}
