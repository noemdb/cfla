<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiant\Representant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Prosecucion;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait UpdateEstudiants {

    public static function fill_prosecucions()
    {
        //ejecutar una sola vez [genera las datos para la prosecucions del siguiente año escolar]
        $datas = collect();
        // DB::statement("SET foreign_key_checks=0");
        // Prosecucion::truncate();
        // DB::statement("SET foreign_key_checks=1");
        $inscripcions = Inscripcion::select('inscripcions.*')
        ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->where('estudiants.status_active','true')
        ->where('seccions.status_active','true')
        ->get()
        ; //dd($inscripcions);
        foreach ($inscripcions as $item) {
            $seccion = $item->seccion;
            if ($seccion) {
                $grado_id = $seccion->grado_id + 1;
                if ($grado_id < 21) {
                    $grado = Grado::find($grado_id);
                    if ($grado) {
                        $seccionNext = Seccion::where('grado_id',$grado_id)->first();
                        if ($seccionNext) {
                            $prosecucion = Prosecucion::where('estudiant_id',$item->estudiant_id)->first();
                            if ($prosecucion) {
                                $prosecucion->seccion_id = $seccionNext->id;
                                $prosecucion->save();
                            } else {
                                $prosecucion = Prosecucion::create([
                                    'seccion_id'=>$seccionNext->id,
                                    'estudiant_id'=>$item->estudiant_id,
                                ]);
                            }

                            $datas->push($prosecucion);
                        }
                    }
                }
            }
        }
        dd($inscripcions,$datas);
    }

    public static function movement_estudiants()
    {
        $datas = collect();

        $inscripcions = DB::table('inscripcions')->select('inscripcions.*')
        ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->join('grados', 'grados.id', '=', 'seccions.grado_id')
        ->where('grados.id',7)
        ->get();
        foreach ($inscripcions as $item) {
            $inscripcion = Inscripcion::find($item->id);
            if ($inscripcion) {
                $seccion_id = $item->seccion_id;
                $seccion_id = ($seccion_id == 13) ? 47 : $seccion_id ;
                $seccion_id = ($seccion_id == 14) ? 48 : $seccion_id ;
                $inscripcion->seccion_id = $seccion_id;
                $inscripcion->save();
                $datas->push($inscripcion);
            }
        }
        dd($inscripcions,$datas);

    }

    public static function update_gemail_estudiants()
    {
        $datas = collect();
        $file = "updateGSEmail";
        $folder = "estudiants"; // public/csv/2324/estudiants/updateGSEmail.csv
        $csvFile = public_path().'/csv/2526/'.$folder.'/'.$file.'.csv'; //dd($csvFile);
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $estudiant_csv) {
            $ci_estudiant = $estudiant_csv['ci_estudiant'];
            $estudiant = Estudiant::where('ci_estudiant',$ci_estudiant)->first(); //dd($estudiant_csv,$estudiant);
            if ($estudiant) {
                $gsemail = $estudiant_csv['gsemail']; //dd($gsemail);
                if ($gsemail) {
                    if (validate_email($gsemail)) { //dd($gsemail);
                        $unique = Estudiant::where('gsemail',$gsemail)->first(); //dd($unique);
                        if (empty($unique)) {
                            $arr = ['gsemail'=>$gsemail]; //dd($arr);
                            $estudiant->fill($arr);
                            $estudiant->save();
                            $datas->push($estudiant);
                        }
                    }
                }
            }
        }

        dd($arr_data,$datas);
    }

}
