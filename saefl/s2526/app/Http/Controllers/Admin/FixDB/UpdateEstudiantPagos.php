<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiant\Representant;
use App\Models\app\Planpago\Saldo;
use App\Models\app\Planpago\Pago;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait UpdateEstudiantPagos {

    public static function update_estudiant_pagos()
    {
        $datas = collect();

        $file = "representantPagos";
        $folder = "representants";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $pago_csv) {
            $modeCreate = null; $modeUpdate = null;
            $representant = Representant::where('ci_representant',$pago_csv['ci_representant'])->first();
            if ($representant) {
                $ammount = $pago_csv['ammount'];
                $cuota = $pago_csv['cuota'];
                if ($ammount) {
                    $arr = ['ammount'=>$ammount,'cuota'=>$cuota]; //dd($arr);
                    $pago = Pago::where('representant_id',$representant->id)->first();
                    if ($pago) {
                        $pago->fill($arr);
                        $pago->save();
                        $modeUpdate = true;
                    } else {
                        // $arr = ['estudiant_id'=>$estudiant->id,'ammount'=>$ammount]; //dd($arr);
                        $arr = ['representant_id'=>$representant->id,'ammount'=>$ammount,'cuota'=>$cuota]; //dd($arr);
                        $pagoCreate = Pago::create($arr);
                        $modeCreate = true;
                    }
                    $arr['modeCreate'] = $modeCreate;
                    $arr['modeUpdate'] = $modeUpdate;
                    $datas->push($arr);

                }
            }
        }

        dd($arr_data,$datas);

        // print_r($users);
    }

}
