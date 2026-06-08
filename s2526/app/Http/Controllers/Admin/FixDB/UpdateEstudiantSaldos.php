<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Estudiant;
use App\Models\app\Planpago\Saldo;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait UpdateEstudiantSaldos {

    public static function update_estudiant_saldos()
    {
        $datas = collect();

        $file = "EstudiantSaldos";
        $folder = "estudiants";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $saldo_csv) {
            $modeCreate = null; $modeUpdate = null;
            $estudiant = Estudiant::where('ci_estudiant',$saldo_csv['ci_estudiant'])->first();
            if ($estudiant) {
                $ammount = $saldo_csv['ammount'];
                if ($ammount) {
                    $arr = ['ammount'=>$ammount]; //dd($arr);
                    $saldo = Saldo::where('estudiant_id',$estudiant->id)->first();
                    if ($saldo) {
                        $saldo->fill($arr);
                        $saldo->save();
                        $modeUpdate = true;
                    } else {
                        $arr = ['estudiant_id'=>$estudiant->id,'ammount'=>$ammount]; //dd($arr);
                        $saldoCreate = Saldo::create($arr);
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
