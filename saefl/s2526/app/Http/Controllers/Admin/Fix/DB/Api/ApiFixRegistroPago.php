<?php

namespace App\Http\Controllers\Admin\Fix\DB\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\Recurso;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Carbon\Carbon;

class ApiFixRegistroPago extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function fix_status_pay(Request $request)
    {

        $start = (!empty($request->start)) ? $request->start : null;
        $size = (!empty($request->size)) ? $request->size : null ;

        $datas_fix = collect([]);
        $datas_no_fix = collect([]);

        $cuentaxpagars = Cuentaxpagar::all()->slice($start,$size);

        foreach ($cuentaxpagars as $cuentaxpagar) {

            $fecha = Carbon::createFromDate($cuentaxpagar->date_expiration)->endOfMonth()->format('Y-m-d');

            $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion($fecha)->get();

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

                            $datas_fix->push($datas_fix);
                        }
                        else {
                            $datas_no_fix->push($data);
                        }
                    }

                }

            }

        }

        return json_encode($datas_fix);

    }

    public function fix_fill_recursos(Request $request)
    {

        $start = (!empty($request->start)) ? $request->start : null;
        $size = (!empty($request->size)) ? $request->size : null ;

        if ($start && $size) {

            $datas = collect([]);

            $registro_pago_combinados = RegistroPagoCombinado::select('registro_pago_combinados.*')
                ->join('registro_pagos','registro_pago_combinados.id','=','registro_pagos.registro_pago_combinado_id')
                ->leftjoin('recursos','registro_pago_combinados.id','=','recursos.registro_pago_combinado_id')
                ->WhereNull('recursos.registro_pago_combinado_id')
                ->WhereNull('registro_pagos.deleted_at')
                ->GroupBy('registro_pago_combinados.id')
                ->OrderBy('registro_pagos.id','asc')
                ->get()
                ->slice($start,$size);

            foreach ($registro_pago_combinados as $registro_pago_combinado) {

                if ( !($registro_pago_combinado->status_irregular) ) {

                    $data = collect([]);

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
                        $name = 'ingreso_id_'.$abono->id;
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

            return json_encode($datas);
        }

    }

    public function fix_registro_pagos(Request $request)
    {

        $start = (!empty($request->start)) ? $request->start : null;
        $size = (!empty($request->size)) ? $request->size : null ;

        $datas = collect([]);

        if ($start && $size) {

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
        }

        return json_encode($datas);
    }

}
