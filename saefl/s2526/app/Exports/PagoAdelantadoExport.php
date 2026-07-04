<?php

namespace App\Exports;

use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\RegistroPago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class PagoAdelantadoExport  implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'CI Representante',
            'Representante',
            'Total ING',
            'Total CAF aplicado',
            'Total Recurso',
            'Total Pagado adelantado',
            'Total CAF Generado',
            'Referencias',
            'Fechas',
        ];
    }

    public function collection()
    {
        $request            = (!empty($this->request)) ? $this->request : null;
        $cuentaxpagar_id    = (!empty($request->cuentaxpagar_id)) ? $request->cuentaxpagar_id : null  ;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;


        $registro_pago_combinados =
            RegistroPagoCombinado::select('registro_pago_combinados.*')
                ->join('recursos', 'registro_pago_combinados.id', '=', 'recursos.registro_pago_combinado_id')
                ->leftjoin('ingresos', 'ingresos.id', '=', 'recursos.ingreso_id')
                ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
                ->join('representants', 'representants.id', '=', 'registro_pago_combinados.representant_id')

                ->whereNull('registro_pago_combinados.deleted_at')
                ->whereNull('registro_pagos.deleted_at')
                ->whereNull('ingresos.deleted_at')

                ->GroupBy('registro_pago_combinados.id')
                ;

        if ($cuentaxpagar_id) {

            $registro_pago_combinados = $registro_pago_combinados->where('registro_pagos.cuentaxpagar_id',$cuentaxpagar_id);

            $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);
            $date_expiration = new Carbon($cuentaxpagar->date_expiration);
            $date_expiration_start = $date_expiration->copy()->subMonth()->startOfMonth();
            $date_expiration_end = $date_expiration->copy()->subMonth()->endOfMonth();

            $registro_pago_combinados = $registro_pago_combinados->wheredate('ingresos.date_transaction','>=',$date_expiration_start);
            $registro_pago_combinados = $registro_pago_combinados->wheredate('ingresos.date_transaction','<=',$date_expiration_end);

        }

        if ($banco_id) {
            $registro_pago_combinados = $registro_pago_combinados->where('ingresos.banco_id', 'like', "%".$banco_id."%");
        }

        $registro_pago_combinados = $registro_pago_combinados->get();

        $datas = collect([]);

        foreach ($registro_pago_combinados as $registro_pago_combinado) {

            $ingresos = $registro_pago_combinado->ingresos_cuenta_x_pagar($cuentaxpagar_id,$banco_id);
            $referencias = null;
            $fechas = null;
            foreach ($ingresos as $ingreso){
                $fecha = (validateDate($ingreso->date_transaction,'Y-m-d')) ? f_float($ingreso->date_transaction) : $ingreso->date_transaction ;
                $referencias .= $ingreso->number_i_pay . ';';
                $fechas .= $fecha . ';';
            }
            $total_ingreso_ammount = $ingresos->sum('ingreso_ammount');

            $creditos = $registro_pago_combinado->recursos->where('status_credito','true');
            $total_credito_ammount = 0;
            foreach ($creditos as $credito){
                $credito_ammount = $credito->all_credito_a_favor->credito_ammount;
                $total_credito_ammount += $credito_ammount;
            }

            $pagos = $registro_pago_combinado->pagos_cuenta_x_pagar($cuentaxpagar_id,$banco_id);
            // $total_pago_ammount = $pagos->sum('pagos_ammount');
            $total_pago_ammount = $pagos->sum('pagos_ammount') - $total_credito_ammount;

            $ammount_creditos_generados = $registro_pago_combinado->ammount_creditos_generados;

            $data = collect([]);

            $ci_representant        = $registro_pago_combinado->ci_representant;
            $representants_name     = $registro_pago_combinado->representant_name;

            $data->put('ci_representant', $ci_representant);
            $data->put('representants_name', $representants_name);

            $data->put('total_ingreso_ammount', $total_ingreso_ammount);
            $data->put('total_credito_ammount', $total_credito_ammount);
            $data->put('total_recurso', ($total_ingreso_ammount + $total_credito_ammount));

            $data->put('total_pago_ammount', $total_pago_ammount);

            $data->put('ammount_creditos_generados', $ammount_creditos_generados);

            $data->put('referencias', $referencias);
            $data->put('fechas', $fechas);

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
