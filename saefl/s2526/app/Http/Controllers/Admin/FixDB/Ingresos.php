<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiant\Representant;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait Ingresos {

    public static function fix_ingresos_pea()
    {
        $datas = collect();

        $file = "pea";
        $folder = "2122/ingresos";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv'; //dd($csvFile);
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $row) {
            //id;number_i_pay
            $id = $row['id'];
            $number_i_pay = $row['number_i_pay'];
            $ingreso = Ingreso::where('number_i_pay',$number_i_pay)->first();
            if ($ingreso) {
                $ingreso->update(['deleted_at'=>'2021-09-01']);
                // $ingreso->save();
                $datas->push($ingreso);
            }
        }

        dd($arr_data,$datas);

    }

    public static function fix_ingresos_pm()
    {
        $datas = collect();
        // $ingresos = Ingreso::where('banco_id',6)->get(); dd($ingresos);
        $ingresos = Ingreso::select('ingresos.*')->withTrashed()->where('banco_id',9)->get(); //dd($ingresos);
        foreach ($ingresos as $ingreso) {
            $ingreso->banco_id = 2;
            $ingreso->method_pay_id = 7;
            $ingreso->save();
            $datas->push($ingreso);
        }
        dd($datas);
    }

}
