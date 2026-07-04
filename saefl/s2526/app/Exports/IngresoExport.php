<?php

namespace App\Exports;

use App\Models\app\Estudiante\Ingreso;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use DB;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class IngresoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function headings(): array
    {
        return [
            'ID',
            'Identificador',
            'Representante',
            'Banco',
            'Referencia',
            'Observación',
            'Monto',
            'M. Cambiario',
            'Fecha Banco',
            'Fecha Registro',
            'Fecha de Pago',
        ];
    }
    public function collection()
    {
        $datas =
        Ingreso::select('ingresos.id','representants.ci_representant','representants.name as representants_name',
        'bancos.name as bancos_name','ingresos.number_i_pay','ingresos.ingreso_observations','ingresos.ingreso_ammount','ingresos.exchange_ammount',
        'ingresos.date_transaction','ingresos.created_at','ingresos.date_payment')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->join('representants', 'representants.id', '=', 'ingresos.representant_id')
            ->wherenull('ingresos.deleted_at')
            ->wherenull('representants.deleted_at')
            ->get();
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
