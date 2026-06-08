<?php

namespace App\Http\Controllers\Admin\Fix\DB\Functions\RegistroPagoCombinado;

use App\Models\app\Estudiant;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\Recurso;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait FixRegistroPago {

    public function fix_pago_combinado_anulacion_imcompleta()
    {
        $combinados =
        DB::table('registro_pago_combinados')
        ->select('registro_pago_combinados.*','registro_pagos.id as registro_pago_id')
        ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
        ->whereNull('registro_pagos.deleted_at')
        ->whereNotNull('registro_pago_combinados.deleted_at')
        ->get();

        foreach ($combinados as $combinado) {
            $registro_pago_combinados = RegistroPagoCombinado::withTrashed()->where('id',$combinado->id)->first();
            // $registro_pago_combinados->fill(['deleted_at'=>null]);
            $registro_pago_combinados->restore();
            // $registro_pago_combinados->save();
        }

        $combinados2 =
        DB::table('registro_pago_combinados')
        ->select('registro_pago_combinados.*','registro_pagos.id as registro_pago_id')
        ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
        ->whereNull('registro_pagos.deleted_at')
        ->whereNotNull('registro_pago_combinados.deleted_at')
        ->get();

        dd($combinados,$combinados2);
    }

    public function fix_concepto_cancelado_status_paid()
    {
        $datas = collect([]);
        $datas2 = collect([]);

        // $cuentaxpagars = Cuentaxpagar::where('type','GENERAL')->get();
        $cuentaxpagars = Cuentaxpagar::where('type','<>','GENERAL')->get()->slice(150,50);

        foreach ($cuentaxpagars as $cuentaxpagar) {

            $fecha = Carbon::createFromDate($cuentaxpagar->date_expiration)->endOfMonth()->format('Y-m-d');

            $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion($fecha)->get();

            // $datas->push($estudiants);

            foreach ($estudiants as $estudiant) {

                $concepto_pagados = $cuentaxpagar->ConceptosPagados($estudiant->id);

                foreach ($concepto_pagados as $concepto_pagado) {

                    $data = collect([]);

                    $descuento = 0 ;
                    if ($concepto_pagado->status_discount=="true") {
                        $descuento = $estudiant->descuento_ammount($cuentaxpagar->id) / 100;
                    }

                    $concepto_ammount = $concepto_pagado->concepto_ammount * ( 1 - $descuento);

                    $total_concepto_cancelado_ammount = $concepto_pagado->sum_pay_concepto_ammount * ( 1 - $concepto_pagado->descuento_ammount);

                    $data->put('estudiant_id', $estudiant->id);
                    $data->put('ci_estudiant', $estudiant->ci_estudiant);
                    $data->put('ci_representant', $estudiant->representant->ci_representant);
                    $data->put('cuentaxpagar_id', $cuentaxpagar->id);
                    $data->put('concepto_pago_id', $concepto_pagado->concepto_pago_id);
                    $data->put('concepto_pago_id', $concepto_pagado->concepto_pago_id);
                    $data->put('concepto_ammount', $concepto_ammount);
                    $data->put('total_concepto_pagado', $total_concepto_cancelado_ammount);

                    if($concepto_ammount == $total_concepto_cancelado_ammount) {

                        $concepto_cancelado = ConceptoCancelado::select('concepto_cancelados.*')
                            ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
                            ->join('registro_pagos', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')

                            ->Where('registro_pagos.estudiant_id',$estudiant->id)
                            ->Where('registro_pagos.cuentaxpagar_id',$cuentaxpagar->id)
                            ->where('concepto_cancelados.status_paid','true')
                            ->first();

                        if ( empty($concepto_cancelado) ) {

                            $concepto_cancelado_status_paid = ConceptoCancelado::select('concepto_cancelados.*')
                                ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
                                ->join('registro_pagos', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')

                                ->Where('registro_pagos.estudiant_id',$estudiant->id)
                                ->Where('registro_pagos.cuentaxpagar_id',$cuentaxpagar->id)
                                ->first();

                            $concepto_cancelado_status_paid->update(['status_paid' => 'true']);

                            // $datas2->push($data->toArray());
                            // $datas2->push($concepto_cancelado_status_paid->toArray());

                        }
                    }

                }

            }
        }

        // dd($datas,$datas2);

    }

    public function fix_fill_recursos()
    {
        $datas = collect([]);

        // DB::statement("SET foreign_key_checks=0");
        // Recurso::truncate();
        // DB::statement("SET foreign_key_checks=1");

        $registro_pago_combinados = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos','registro_pago_combinados.id','=','registro_pagos.registro_pago_combinado_id')
            ->leftjoin('recursos','registro_pago_combinados.id','=','recursos.registro_pago_combinado_id')
            ->WhereNull('recursos.registro_pago_combinado_id')
            ->WhereNull('registro_pagos.deleted_at')
            ->GroupBy('registro_pago_combinados.id')
            ->OrderBy('registro_pagos.id','asc')
            // ->take(100)
            ->get();

        foreach ($registro_pago_combinados as $registro_pago_combinado) {

            if ( !($registro_pago_combinado->status_irregular) ) {

                $data = collect([]);

                // $data->put('registro_pago_combinado', $registro_pago_combinado);

                $ingresos = $registro_pago_combinado->ingresos;
                foreach ($ingresos as $ingreso) {
                    $arr = [
                        'registro_pago_combinado_id' => $registro_pago_combinado->id,
                        'ingreso_id' => $ingreso->id,
                        'status_ingreso' => 'true',
                    ];
                    $recurso = Recurso::create($arr);
                    $name = 'ingreso_id_'.$ingreso->id;
                    $data->put($name, $arr);
                    $datas->push($data);
                }

                $abonos= $registro_pago_combinado->abonos_aplicados;
                foreach ($abonos as $abono) {
                    $arr = [
                        'registro_pago_combinado_id' => $registro_pago_combinado->id,
                        'ingreso_id' => $abono->ingreso_id,
                        'status_abono' => 'true',
                    ];
                    $recurso = Recurso::create($arr);
                    $name = 'ingreso_id_'.$ingreso->id;
                    $data->put($name, $arr);
                    $datas->push($data);
                }

                $creditos_aplicados = $registro_pago_combinado->creditos_aplicados;
                foreach ($creditos_aplicados as $credito) {
                    $arr = [
                        'registro_pago_combinado_id' => $registro_pago_combinado->id,
                        'credito_a_favor_id' => $credito->id,
                        'status_credito' => 'true',
                    ];
                    $recurso = Recurso::create($arr);
                    $name = 'credito_id_'.$credito->id;
                    $data->put($name, $arr);
                    $datas->push($data);
                }
            }

        }

        dd($datas->toarray());

    }

    public function fix_registro_pagos($start=1,$size=100)
    {
        $datas = collect([]);

        $count = RegistroPagoCombinado::all()->count();

        $registro_pago_combinados = RegistroPagoCombinado::irregular_pay($start,$size); // dd($start,$size,$registro_pago_combinados);

        foreach ($registro_pago_combinados as $registro_pago_combinado) {

            $registro_pagos = RegistroPago::withTrashed()->where('registro_pago_combinado_id',$registro_pago_combinado['id'])->get();

            foreach ($registro_pagos as $registro_pago) {

                $data = $registro_pago->fix_registro_pago_zero($registro_pago_combinado);

                if ($data->isNotEmpty()) {

                    $datas->push($data);

                }

            }

        }

        dd('datas',$datas->toarray());

    }

    public function fix_pago_combinado_null()
    {
        // $datas = collect([]);

        $registropagos = RegistroPago::select('registro_pagos.*')
            ->join('registro_pago_combinados','registro_pago_combinados.id','=','registro_pagos.registro_pago_combinado_id')
            ->WhereNotNull('registro_pago_combinados.deleted_at')
            ->get();

        foreach ($registropagos as $registropago) {

            $registropago->delete();

            $registro_pago_combinado = RegistroPagoCombinado::onlyTrashed()->where('id',$registropago->registro_pago_combinado_id)->first();
            if (!empty($registro_pago_combinado)) {
                $registro_pago_combinado->restore();
            }

            // $datas->push($registropago);
        }

        // dd($datas->toarray());

    }

    public function fix_paid_zero()
    {
        $datas = collect([]);

        $registropagos =
            RegistroPago::select('registro_pagos.*')
            ->join('pagos','registro_pagos.id','=','pagos.registro_pago_id')
            ->Where('pagos.pagos_ammount','0.00')
            ->get();

        foreach ($registropagos as $registropago) {

            foreach ($registropago->pagos as $pago)  $pago->delete();
            foreach ($registropago->abono_aplicados as $abono_aplicado)  $abono_aplicado->delete();
            foreach ($registropago->creditoaplicados as $creditoaplicado)  $creditoaplicado->delete();
            foreach ($registropago->descuentoaplicados as $descuentoaplicado)  $descuentoaplicado->delete();

            $registropago->delete();

            $registro_pago_combinado = (!empty($registropago->registro_pago_combinado)) ? $registropago->registro_pago_combinado:null;
            if (!empty($registro_pago_combinado)) {
                $registro_pagos_count = (!empty($registro_pago_combinado->registropagos)) ? $registro_pago_combinado->registropagos->count() : null ;
                if ($registro_pagos_count == 1) {
                    $registro_pago_combinado->delete();
                }
            }

            $datas->push($registropago);
        }

        // dd($datas->toarray());

    }

    public function fix_registro_pago_combinados(Request $request)
    {
        $datas = collect([]);
        // $registropagos =RegistroPago::withTrashed()
        //     ->where('representant_id','0')
        //     ->get();
        // $datas = collect([]);

        // foreach ($registropagos as $registropago) {
        //     $representant_id = $registropago->estudiant->representant->id;
        //     $update = RegistroPago::withTrashed()
        //         ->where('id',$registropago->id)
        //         ->update(['representant_id'=>$representant_id]);
        //     DB::commit($update);
        // }

        $registropagos =
            DB::table('registro_pagos')
            ->select('registro_pagos.id as registro_pago_id',
                'registro_pagos.estudiant_id','registro_pagos.created_at','registro_pagos.updated_at','representants.id as representant_id',
                DB::raw("DATE(registro_pagos.created_at) as date")
            )
            ->leftjoin('registro_pago_combinados','registro_pago_combinados.id','=','registro_pagos.registro_pago_combinado_id')
            ->join('representants','representants.id','=','registro_pagos.representant_id')
            ->where('registro_pagos.registro_pago_combinado_id',0)
            ->groupby('date','representants.id')
            ->orderby('registro_pagos.created_at')
            ->get();

        foreach ($registropagos as $registropago) {

            $combinado = RegistroPagoCombinado::create([
                'representant_id' => $registropago->representant_id,
                'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: '.$registropago->date,
                'created_at' => $registropago->created_at,
                'updated_at' => $registropago->updated_at,
            ]);

            $datas->push($combinado);

            $update = RegistroPago::withTrashed()
                ->where('representant_id',$registropago->representant_id)
                ->whereDate('created_at',$registropago->date)
                ->update(['registro_pago_combinado_id'=>$combinado->id]);

            DB::commit($update);
        }

        dd($datas->toarray());

    }

    public function fix_registro_pago_combinados_2(Request $request)
    {
        $datas = collect([]);
        $registropagos =
            DB::table('registro_pagos')
            ->select('registro_pagos.id as registro_pago_id',
                'registro_pagos.estudiant_id','registro_pagos.created_at','registro_pagos.updated_at','representants.id as representant_id',
                DB::raw("DATE(registro_pagos.created_at) as date")
            )
            ->leftjoin('registro_pago_combinados','registro_pago_combinados.id','=','registro_pagos.registro_pago_combinado_id')
            ->join('representants','representants.id','=','registro_pagos.representant_id')
            ->where('registro_pagos.registro_pago_combinado_id',0)
            // ->groupby('date','representants.id')
            ->orderby('registro_pagos.created_at')
            ->get();

        // dd($registropagos);

        foreach ($registropagos as $registropago) {

            $combinado = RegistroPagoCombinado::select('registro_pago_combinados.*')
                ->where('representant_id',$registropago->representant_id)
                ->where(DB::raw("DATE(registro_pago_combinados.created_at)"),$registropago->date)
                ->first();

            $datas->push($combinado);

            $update = RegistroPago::withTrashed()
                ->where('id',$registropago->registro_pago_id)
                // ->whereDate('created_at',$registropago->date)
                ->update(['registro_pago_combinado_id'=>$combinado->id]);

            DB::commit($update);
        }
        dd($datas);
    }

}
