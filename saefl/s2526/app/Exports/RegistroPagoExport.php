<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Ingreso;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use DB;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RegistroPagoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'ID',
            // 'Identificador',
            'CI Representante',
            'Representante',
            'Fecha Registro',

            'Total Pagado',
            'Total Pagado Cambiario',
            'Total Ingeso',
            'Total Abonos',
            'Tot.Cred. Aplicados',
            'Tot.Cred. Generados',

            'Concepto(s) de Cobro',
            'Montos Pagado',

            'ING Banco',
            'ING Referencia',
            'ING Monto',
            'ING Fecha Operación',

            'ABN Banco',
            'ABN Referencia',
            'ABN Monto',
            'ABN Fecha Operación',

            'CAF Banco',
            'CAF Referencia',
            'CAF Monto',
            'CAF Fecha Operación',


        ];
    }

    public function collection()
    {
        $request            = (!empty($this->request)) ? $this->request : null;
        $cuentaxpagar_id    = (!empty($request->cuentaxpagar_id)) ? $request->cuentaxpagar_id : null  ;
        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;
        $ci                 = (!empty($request->ci)) ? $request->ci : null  ;

        $registro_pago_combinados =
            RegistroPagoCombinado::select(
                'registro_pago_combinados.*','registro_pagos.id as registro_pago_id',
                // 'estudiants.ci_estudiant', DB::raw("CONCAT(estudiants.lastname,' ',estudiants.name) as estudiants_fullname"),
                // 'cuentaxpagars.name as cuentaxpagar_name','pagos.pagos_ammount',
                'representants.ci_representant','representants.name as representants_name'
                // 'bancos.name as bancos_name','ingresos.number_i_pay','ingresos.ingreso_ammount','ingresos.date_transaction',
                // 'registro_pagos.created_at as created_at'
            )
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->join('estudiants', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->leftjoin('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->leftjoin('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->where('pagos.pagos_ammount','>',0)
            // ->wherenull('ingresos.deleted_at')
            ->wherenull('representants.deleted_at')
            ->wherenull('cuentaxpagars.deleted_at')
            // ->wherenull('pagos.deleted_at')
            ->wherenull('estudiants.deleted_at')
            // ->wherenull('registro_pagos.deleted_at')
            ->groupby('registro_pago_combinados.id')
            ;

        if ($cuentaxpagar_id) {
            $registro_pago_combinados = $registro_pago_combinados->where('cuentaxpagars.id',$cuentaxpagar_id);
        }
        if ($finicial) {
            $registro_pago_combinados = $registro_pago_combinados->wheredate('registro_pagos.created_at','>=',$finicial);
        }
        if ($ffinal) {
            $registro_pago_combinados = $registro_pago_combinados->wheredate('registro_pagos.created_at','<=',$ffinal);
        }
        if ($number_i_pay) {
            $registro_pago_combinados = $registro_pago_combinados->where('ingresos.number_i_pay', 'like', "%".$number_i_pay."%");
        }
        if ($ci) {
            $registro_pago_combinados = $registro_pago_combinados->where('estudiants.ci_estudiant', 'like', "%".$ci."%");
            $registro_pago_combinados = $registro_pago_combinados->Orwhere('representants.ci_representant', 'like', "%".$ci."%");
        }

        $registro_pago_combinados = $registro_pago_combinados->get();

        // dd($registro_pago_combinados);

        $datas = collect([]);

        foreach ($registro_pago_combinados as $registro_pago_combinado) {

            $pagos = $registro_pago_combinado->pagos;
            $ingresos = $registro_pago_combinado->ingresos;
            $abonos = $registro_pago_combinado->abonos_aplicados;
            $creditos_aplicados = $registro_pago_combinado->creditos_aplicados;
            $creditos_generados = $registro_pago_combinado->creditos_generados;

            $data = collect([]);

            $id                     = (!empty($registro_pago_combinado->id)) ? $registro_pago_combinado->id:null;
            $ci_representant        = (!empty($registro_pago_combinado->ci_representant)) ? $registro_pago_combinado->ci_representant:null;
            $representants_name     = (!empty($registro_pago_combinado->representants_name)) ? $registro_pago_combinado->representants_name:null;
            $total_pagado           = $pagos->sum('pagos_ammount');
            $total_pagado_exchange  = $pagos->sum('exchange_ammount');
            $total_ingreso          = $ingresos->sum('ingreso_ammount');
            $total_abonos           = $abonos->sum('ingreso_ammount');
            $total_credito_aplicado = $creditos_aplicados->sum('credito_ammount');
            $total_credito_generado = $creditos_generados->sum('credito_ammount');
            $created_at             = (!empty($registro_pago_combinado->created_at)) ? $registro_pago_combinado->created_at->format('d-m-Y'):null;

            $cuentaxpagar_name = null;$pagos_ammount = null;
            $registropagos = $registro_pago_combinado->registropagos;
            // $nsb = ($registropagos->count()>1) ? '|':null;
            $nsb = '|';
            foreach ($registropagos as $registropago) {
                $cuentaxpagar_name      .= (!empty($registropago->cuentaxpagar)) ? $registropago->cuentaxpagar->name.$nsb:null;
                $pagos_ammount          .= (!empty($registropago->pago)) ? $registropago->pago->pagos_ammount.$nsb:null;
            }
            $cuentaxpagar_name = substr($cuentaxpagar_name,0,-1);
            $pagos_ammount = substr($pagos_ammount,0,-1);

            $ing_bancos_name = null;$ing_number_i_pay = null;$ing_ingreso_ammount = null;$ing_date_transaction = null;
            $nsb = '|';
            foreach ($ingresos as $ingreso) {
                $ing_bancos_name .= $ingreso->banco_name.$nsb;
                $ing_number_i_pay .= $ingreso->number_i_pay.$nsb;
                $ing_ingreso_ammount .= $ingreso->ingreso_ammount.$nsb;
                $ing_date_transaction .= f_date($ingreso->date_transaction).$nsb;
            }

            $abn_bancos_name = null;$abn_number_i_pay = null;$abn_abono_ammount = null;$abn_date_transaction = null;
            $nsb = ($abonos->count()>1) ? '|':null;
            foreach ($abonos as $abono) {
                $abn_bancos_name .= $abono->banco_name.$nsb;
                $abn_number_i_pay .= $abono->number_i_pay.$nsb;
                $abn_abono_ammount .= $abono->ingreso_ammount.$nsb;
                $abn_date_transaction .= f_date($abono->date_transaction).$nsb;
            }

            $caf_bancos_name = null;$caf_number_i_pay = null;$caf_ammount = null;$caf_date_transaction = null;
            $nsb = ($creditos_aplicados->count()>1) ? '|':null;
            foreach ($creditos_aplicados as $credito) {
                if (!empty($credito->number_i_pay)) {
                    $caf_bancos_name .= $credito->banco_name.$nsb;
                    $caf_number_i_pay .= $credito->number_i_pay.$nsb;
                    $caf_ammount .= $credito->ingreso_ammount.$nsb;
                    $caf_date_transaction .= f_date($credito->date_transaction).$nsb;
                }
                else{
                    $credito_a_favor = CreditoAFavor::withTrashed()->where('id',$credito->id)->first();
                    $caf_ingresos = $credito_a_favor->ingresos_recursive($credito->registro_pago_id);
                    $nsb = (count($caf_ingresos)>1) ? '|':null;
                    $temp = null;
                    foreach ($caf_ingresos->unique() as $credito) {
                        $caf_bancos_name .= (!empty($credito['banco_name'])) ? $credito['banco_name'].$nsb : 'FALLO:'.$credito['ingreso_id'].'-'.$credito['number_i_pay'].'-'.$credito['ingreso_ammount'].'-'.$credito['date_transaction'].$nsb;
                        $caf_number_i_pay .= (!empty($credito['number_i_pay'])) ? $credito['number_i_pay'].$nsb : 'FALLO';
                        $caf_ammount .= (!empty($credito['ingreso_ammount'])) ? $credito['ingreso_ammount'].$nsb : 'FALLO';
                        $caf_date_transaction .= (!empty($credito['date_transaction'])) ? $credito['date_transaction'].$nsb : 'FALLO';
                        if ($credito['number_i_pay']=="1268152450") {
                            // dd($registro_pago_combinado);
                        }
                    }
                }
            }

            // if ($registro_pago_combinado->id=='2324') {
            //     dd($credito_a_favor->id,$caf_ingresos,$caf_bancos_name,$caf_number_i_pay,$caf_ammount,$caf_date_transaction,$caf_ingresos);
            // }

            $data->put('id', $id);
            // $data->put('ci_estudiant', $ci_estudiant);
            // $data->put('estudiants_fullname', $estudiants_fullname);
            $data->put('ci_representant', $ci_representant);
            $data->put('representants_name', $representants_name);
            $data->put('created_at', $created_at);

            $data->put('total_pagado', $total_pagado);
            $data->put('total_pagado_exchange', $total_pagado_exchange);
            $data->put('total_ingreso', $total_ingreso);
            $data->put('total_abonos', $total_abonos);
            $data->put('total_credito_aplicado', $total_credito_aplicado);
            $data->put('total_credito_generado', $total_credito_generado);

            $data->put('cuentaxpagar_name', $cuentaxpagar_name);
            $data->put('pagos_ammount', $pagos_ammount);

            $data->put('ing_bancos_name', $ing_bancos_name);
            $data->put('ing_number_i_pay', $ing_number_i_pay);
            $data->put('ing_ingreso_ammount', $ing_ingreso_ammount);
            $data->put('ing_date_transaction', $ing_date_transaction);

            $data->put('abono_banco', $abn_bancos_name);
            $data->put('abono_referencia', $abn_number_i_pay);
            $data->put('abono_ammount', $abn_abono_ammount);
            $data->put('abono_date', $abn_date_transaction);

            $data->put('caf_bancos_name', $caf_bancos_name);
            $data->put('caf_number_i_pay', $caf_number_i_pay);
            $data->put('caf_ammount', $caf_ammount);
            $data->put('caf_date_transaction', $caf_date_transaction);

            // if ($total_credito_aplicado) {
            //     dd($data);
            // }

            $datas->push($data);
        }

        return $datas;

    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:Z1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);
            },
        ];
    }
}
