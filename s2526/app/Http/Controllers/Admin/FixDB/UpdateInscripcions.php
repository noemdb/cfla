<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiant\Representant;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait UpdateInscripcions {

    public static function update_gemail_inscripcions()
    {
        $datas = collect();

        $file = "updateGSEmail";
        $folder = "inscripcions";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $inscripcion_csv) {
            $estudiant = Estudiant::where('ci_estudiant',$inscripcion_csv['ci_estudiant'])->first();
            if ($estudiant) {
                $gsemail = $inscripcion_csv['gsemail'];
                if ($gsemail) {
                    $inscripcion = $estudiant->inscripcion;
                    if ($inscripcion) {
                        $arr = ['email_cv'=>$gsemail]; //dd($arr);
                        $inscripcion->fill($arr);
                        $inscripcion->save();
                        $datas->push($inscripcion);
                    }

                }
            }
        }

        dd($arr_data,$datas);

        // print_r($users);
    }

}
