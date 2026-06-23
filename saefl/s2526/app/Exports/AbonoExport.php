<?php

namespace App\Exports;

use App\Models\app\Estudiante\Abono;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AbonoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'ID',
            'RId',
            'Identificador',
            'Representante',
            'Banco',
            'Referencia',
            'Monto',
            'MCambiario',
            'Fecha',
        ];
    }
    public function collection()
    {
        $request            = (!empty($this->request)) ? $this->request : null;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null  ;
        $state             = (!empty($request->state)) ? $request->state : null  ;

        $datas = Abono::select('abonos.id','representants.id as representant_id','representants.ci_representant','representants.name as representants_name',
                'bancos.name as bancos_name','ingresos.number_i_pay','ingresos.ingreso_ammount','ingresos.exchange_ammount as ammount','ingresos.date_transaction')
            ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->join('representants', 'representants.id', '=', 'abonos.representant_id')
            ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            // ->wherenull('abonos.deleted_at')
            // ->wherenull('ingresos.deleted_at')
            ->groupBy('abonos.id')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')
            ->wherenull('representants.deleted_at');

            if ($finicial) {
                $datas = $datas->whereDate('ingresos.date_transaction','>=',$finicial);
            }
            if ($ffinal) {
                $datas = $datas->whereDate('ingresos.date_transaction','<=',$ffinal);
            }
            if ($banco_id) {
                $datas = $datas->where('ingresos.banco_id', 'like', "%".$banco_id."%");
            }
            if ($number_i_pay) {
                $datas = $datas->where('ingresos.number_i_pay', 'like', "%".$number_i_pay."%");
            }
            if ($ci_representant) {
                $datas = $datas->where('representants.ci_representant', 'like', "%".$ci_representant."%");
            }
            switch ($state) {
                case 'APLICADO': $datas = $datas->whereNotNull('abonos.deleted_at'); break;
                case 'NO APLICADO': $datas = $datas->whereNull('abonos.deleted_at')->wherenull('ingresos.deleted_at'); break;
                default: $datas = $datas->wherenull('abonos.deleted_at')->wherenull('ingresos.deleted_at'); break;
            }

            $datas = $datas->get(); //dd($datas);

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
