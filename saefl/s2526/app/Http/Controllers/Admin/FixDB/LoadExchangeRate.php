<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Estudiant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Representant;

trait LoadExchangeRate {

    public static function fixCAFIngresoCreateING()
    {
        $datas = collect();
        $data = collect();
        $cafsFix = collect();
        $pagoFix = collect();
        $ingresos = collect();

        $cafs = DB::table('credito_a_favors')
            ->select('credito_a_favors.*','ingresos.id as ingresos_id','pagos.abono_ids','pagos.caf_ids')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->join('credito_aplicados', 'registro_pagos.id', '=', 'credito_aplicados.registro_pago_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->whereNull('registro_pagos.deleted_at')
            ->get();

        //INGRESOS
        foreach ($cafs as $caf) {
            $estudiant = Estudiant::withTrashed()->where('id',$caf->estudiant_id)->first();
            $representant = Representant::withTrashed()->where('id',$caf->representant_id)->first();
            if ($estudiant && $representant ) {
                $ingreso = Ingreso::withTrashed()->where('id',$caf->ingresos_id)->first();
                if ($ingreso) {
                    $ingresos->push($ingreso);
                    $number_i_pay = $ingreso->number_i_pay.'-CAF-'.$caf->id;
                    $data = Ingreso::create([
                        'estudiant_id' => $caf->estudiant_id,
                        'representant_id' => $caf->representant_id,
                        'method_pay_id' => 1,
                        'banco_id' => 1,
                        'caf_id' => $caf->id,
                        'date_transaction' =>$ingreso->date_transaction,
                        'number_i_pay' =>$number_i_pay,
                        'date_payment' =>$ingreso->date_payment,
                        'ingreso_ammount' =>$caf->credito_ammount,
                        'exchange_ammount' =>$caf->exchange_ammount,
                        'ingreso_observations' => $ingreso->ingreso_observations,
                        'person_bill_ci' => $representant->ci_representant,
                        'person_bill_name' =>$representant->name,
                    ]);

                    $pago = Pago::select('pagos.*')
                        ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                        ->join('credito_aplicados', 'registro_pagos.id', '=', 'credito_aplicados.registro_pago_id')
                        ->where('credito_aplicados.credito_a_favor_id',$caf->id)
                        ->whereNull('pagos.ingreso_id')
                        ->groupBy('pagos.id')
                        ->first();

                    if ($pago) $pago->update(['ingreso_id'=>$data->id]);
                }
            }
        }
        dd($cafs,$cafsFix,$pagoFix,$ingresos,$datas);
    }

    public static function fixCAFIngresoCreateABN()
    {
        $datas = collect();
        $data = collect();
        $cafsFix = collect();
        $pagoFix = collect();
        $ingresos = collect();

        //Abonos
        $cafs = DB::table('credito_a_favors')
            ->select('credito_a_favors.*','ingresos.id as ingresos_id','pagos.abono_ids','pagos.caf_ids')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->leftjoin('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->whereNull('registro_pagos.deleted_at')
            ->whereNull('pagos.ingreso_id')
            ->whereNull('pagos.caf_ids')
            ->whereNotNull('pagos.abono_ids')
            ->get();
        foreach ($cafs as $caf) {
            $estudiant = Estudiant::withTrashed()->where('id',$caf->estudiant_id)->first();
            $representant = Representant::withTrashed()->where('id',$caf->representant_id)->first();

            if ($estudiant && $representant ) {

                $abono_ids =  explode(";", $caf->abono_ids) ;
                $ingreso = DB::table('ingresos')
                    ->select('ingresos.*')
                    ->join('abonos', 'ingresos.id', '=', 'abonos.ingreso_id')
                    ->whereIn('abonos.id', $abono_ids)
                    ->orderBy('ingresos.date_payment','desc')
                    ->first();

                if ($ingreso) {
                    $number_i_pay = $ingreso->number_i_pay.'-CAF-'.$caf->id;
                    $data = Ingreso::create([
                        'estudiant_id' => $caf->estudiant_id,
                        'representant_id' => $caf->representant_id,
                        'method_pay_id' => 1,
                        'banco_id' => 1,
                        'caf_id' => $caf->id,
                        'date_transaction' =>$ingreso->date_transaction,
                        'number_i_pay' =>$number_i_pay,
                        'date_payment' =>$ingreso->date_payment,
                        'ingreso_ammount' =>$caf->credito_ammount,
                        'exchange_ammount' =>$caf->exchange_ammount,
                        'ingreso_observations' => $ingreso->ingreso_observations,
                        'person_bill_ci' => $representant->ci_representant,
                        'person_bill_name' =>$representant->name,
                    ]);

                    $pago = Pago::select('pagos.*')
                        ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                        ->join('credito_aplicados', 'registro_pagos.id', '=', 'credito_aplicados.registro_pago_id')
                        ->where('credito_aplicados.credito_a_favor_id',$caf->id)
                        ->whereNull('pagos.ingreso_id')
                        ->groupBy('pagos.id')
                        ->first();

                    if ($pago) $pago->update(['ingreso_id'=>$data->id]);

                }

            }
        }
        dd($cafs,$cafsFix,$pagoFix,$ingresos,$datas);

    }

    public static function fixCAFIngresoCreateCAF()
    {
        $datas = collect();
        $data = collect();
        $cafsFix = collect();
        $pagoFix = collect();
        $ingresos = collect();

        //CAF'S
        $cafs = DB::table('credito_a_favors')
            ->select('credito_a_favors.*','ingresos.id as ingresos_id','pagos.abono_ids','pagos.caf_ids')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->leftjoin('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->whereNull('pagos.ingreso_id')
            ->whereNull('pagos.abono_ids')
            ->whereNotNull('pagos.caf_ids')
            ->get();

        foreach ($cafs as $caf) {
            $estudiant = Estudiant::withTrashed()->where('id',$caf->estudiant_id)->first();
            $representant = Representant::withTrashed()->where('id',$caf->representant_id)->first();

            if ($estudiant && $representant ) {

                $caf_ids =  explode(";", $caf->caf_ids) ;
                $ingreso = DB::table('ingresos')
                    ->select('ingresos.*')
                    ->whereIn('ingresos.caf_id', $caf_ids)
                    ->orderBy('ingresos.date_payment','desc')
                    ->first();

                if ($ingreso) {
                    $number_i_pay = $ingreso->number_i_pay.'-CAF-'.$caf->id;
                    $data = Ingreso::create([
                        'estudiant_id' => $caf->estudiant_id,
                        'representant_id' => $caf->representant_id,
                        'method_pay_id' => 1,
                        'banco_id' => 1,
                        'caf_id' => $caf->id,
                        'date_transaction' =>$ingreso->date_transaction,
                        'number_i_pay' =>$number_i_pay,
                        'date_payment' =>$ingreso->date_payment,
                        'ingreso_ammount' =>$caf->credito_ammount,
                        'exchange_ammount' =>$caf->exchange_ammount,
                        'ingreso_observations' => $ingreso->ingreso_observations,
                        'person_bill_ci' => $representant->ci_representant,
                        'person_bill_name' =>$representant->name,
                    ]);

                    $pago = Pago::select('pagos.*')
                        ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                        ->join('credito_aplicados', 'registro_pagos.id', '=', 'credito_aplicados.registro_pago_id')
                        ->join('credito_a_favors', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
                        ->where('credito_aplicados.credito_a_favor_id',$caf->id)
                        ->groupBy('pagos.id')
                        ->first();

                    if ($pago) $pago->update(['ingreso_id'=>$data->id]);
                }
            }
        }
        dd($cafs,$cafsFix,$pagoFix,$ingresos,$datas);
    }

    public static function load_tdc_bcv_csv()
    {
        $datas = collect();

        $file = "load_tdc_bcv_csv";
        $folder = "exchange_rate";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);
        $format_date = 'd-m-Y';

        DB::statement('SET FOREIGN_KEY_CHECKS=0'); $truncate = ExchangeRate::truncate(); DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($arr_data as $k => $exchange) {

            $date = (Carbon::createFromFormat($format_date, $exchange['date']) !== false) ? Carbon::parse($exchange['date'])->format('Y-m-d') : null ; //dd($date);
            // $date = (Carbon::createFromFormat('Y-m-d', $exchange['date']) !== false) ? $exchange['date'] : null ; //dd($date);

            $ammount = (is_numeric($exchange['ammount'])) ? $exchange['ammount'] : null ; //dd($ammount);

            if ($date && $ammount) {
                $exist = ExchangeRate::whereDate('date',$date)->first();
                if (empty($exist)) {
                    $arr = [
                        'currency_id'=>1,
                        'currency_referential_id'=>1,
                        'date'=>$date,
                        'ammount'=>$ammount,
                        'source'=>'BCV',
                        'status_official'=>true,
                        'user_id'=>1,
                    ];
                    $exchange_rate = ExchangeRate::create($arr);
                    $datas->push($exchange_rate);
                }
            }
        }

        dd($arr_data,$datas);

        // print_r($datas);
    }

    public static function fix_date_payment_ingresos()
    {
        $datas = collect();
        $errors = collect();

        $file = "fix_fecha";
        $folder = "date_payment";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $row) {

            $error = collect();

            // try {
                $date_payment = Carbon::parse($row['date_payment']);
                // if ($date_payment) {
                    $ingreso_ammount = $row['ingreso_ammount'];
                    $number_i_pay = $row['number_i_pay'];

                    // $ingreso = Ingreso::withTrashed()->where('number_i_pay',$number_i_pay)->whereNull('date_payment')->first();
                    $ingreso = Ingreso::withTrashed()->where('number_i_pay',$number_i_pay)->first();

                    if ($ingreso) {
                        $ingreso->update(['date_payment'=>$date_payment]);
                        $datas->push($row);
                    }
                    else {
                        // $error->put('number_i_pay',$row);
                        $errors->push($row);
                    }

                // }
                // else {
                    // $error->put('error',$row);
                // }
            // } catch (\Exception $e) {
                // if (count($error) > 1 ) {
                    // $errors->push($error);
                // }
            // }
        }
        $i=1;
        echo '<table width="100%">';
        foreach ($errors as $error) {
            echo '<tr>';
                echo '<td>'.$i++.'</td>';
                echo '<td>'.$error['number_i_pay'].'</td>';
                echo '<td>'.$error['date_payment'].'</td>';
                echo '<td>'.$error['ingreso_ammount'].'</td>';
                echo '<td>'.$error['date_transaction'].'</td>';
            echo '<tr>';
        }
        echo '</table>';

        dd($arr_data,$datas,$errors);

    }
    /* ojo no ejecutar antes de verificar */
    public static function fill_date_payment_ingresos()
    {
        $ingresos = Ingreso::whereNull('date_payment')->get();
        foreach ($ingresos as $ingreso) {
            $ingreso->update(['date_payment'=>$ingreso->date_transaction]);
        }
    }

    public static function fill_ingresos_exchange_ammount()
    {
        $datas = collect();
        $ingresos = Ingreso::withTrashed()->whereDate('date_payment','>=','2020-05-01')->get(); //dd($ingresos);
        foreach ($ingresos as $ingreso) {
            $data = $ingreso->update_exchange_rate;
            $datas->push($data);
        }
        dd($datas);
        // print_r($datas);
    }

    public static function fill_cafs_exchange_ammount()
    {
        $pagos_exchange_null = DB::table('credito_a_favors')->update(['exchange_rate_id'=>null,'exchange_ammount'=>null]);

        $creditos = CreditoAFavor::whereNull('registro_pago_id')->get();
        foreach ($creditos as $credito) {
            $exchange_rate = ExchangeRate::whereDate('date','2020-09-01')->first();
            if ($exchange_rate) {
                $exchange_ammount = $credito->credito_ammount / $exchange_rate->ammount ; //dd($exchange_ammount);
                $affected = DB::table('credito_a_favors')->where('id', $credito->id)->update(['exchange_rate_id'=>$exchange_rate->id,'exchange_ammount'=>$exchange_ammount]);
            }
        }

        $datas = collect();
        $errors = collect();
        $creditos = CreditoAFavor::withTrashed()
            ->select('credito_a_favors.*')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->whereNull('registro_pagos.deleted_at')
            ->whereNotNull('credito_a_favors.registro_pago_id')
            ->whereDate('credito_a_favors.created_at','>=','2020-05-01')->get(); //dd($creditos);
        foreach ($creditos as $credito) {
            $exchange_ammount = $credito->update_exchange_rate;
            if ($exchange_ammount) {
                $datas->push($credito);
            } else {
                $errors->push($credito);
            }

        }

        $i=0;
        echo '<table width="100%">';
        foreach ($errors as $error) {
            echo '<tr>';
                echo '<td>'.$i++.'</td>';
                echo '<td>'.$error->id.'</td>';
                echo '<td>'.$error->representant->name.'</td>';
                echo '<td>'.$error->representant->ci_representant.'</td>';
                echo '<td>'.$error->credito_ammount.'</td>';
                echo '<td>'.$error->exchange_ammount.'</td>';
            echo '<tr>';
        }
        echo '</table>';

        dd($datas,$errors);
        // print_r($datas);
    }

    public static function fill_pagos_combinado_exchange_ammount()
    {
        $pagos_exchange_null = DB::table('pagos')->update(['exchange_ammount'=>null]);

        $combinados = RegistroPagoCombinado::select('registro_pago_combinados.*')
        ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
        ->whereDate('registro_pago_combinados.created_at','>=','2020-05-01')
        ->get()
        // ->take(150)
        ; //dd($combinados);

        //dd($combinados);

        $data = collect();
        $datas = collect();

        foreach ($combinados as $combinado) {
            $data = $combinado->update_exchange_rate; //dd($data);
            $datas->push($data);
        }

        dd($datas);

    }

    public static function fix_creditos_generados_exchange_ammount()
    {
        $pagos_exchange_null = DB::table('pagos')->update(['exchange_ammount'=>null]);

        $combinados = RegistroPagoCombinado::select('registro_pago_combinados.*')
        ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
        ->whereDate('registro_pago_combinados.created_at','>=','2020-05-01')
        ->get()
        // ->take(150)
        ; //dd($combinados);

        $data = collect();
        $datas = collect();

        foreach ($combinados as $combinado) {
            $data = $combinado->fix_credito_generado_exchange; //dd($data);
            $datas->push($data);
        }

        dd($datas);

    }

    public static function fill_pagos_exchange_ammount()
    {
        $pagos_exchange_null = DB::table('pagos')->update(['exchange_ammount'=>null]);

        $datas = collect();
        $pagos = Pago::whereDate('created_at','>=','2020-05-01')
        ->get()
        ->take(100)
        ;
        //dd($pagos);

        foreach ($pagos as $pago) {
            $data = $pago->update_exchange_rate; //dd($data);
            $datas->push($data);
        }

        dd('datas',$datas);
    }

    public static function fill_concepto_cancelados_exchange_ammount()
    {
        $datas = collect();
        $concepto_cancelados = ConceptoCancelado::whereDate('created_at','>=','2020-05-01')->get();

        foreach ($concepto_cancelados as $concepto_cancelado) {
            $data = $concepto_cancelado->update_exchange_rate;
            $datas->push($data);
        }

        dd('datas',$datas);
    }

}
