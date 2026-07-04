<?php

namespace App\Exports;

use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Representant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use DB;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CreditoAFavorExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function headings(): array
    {
        return [
            'ID',
            'RId',
            'Identificador',
            'Representante',
            'Monto',
            'MCambiario',
        ];
    }
    public function collection()
    {

        // foreach ($credito_a_favors as $credito_a_favor) {
        //     $representant = $credito_a_favor->representant;
        //     if($representant->active){
        //         $datas->push($credito_a_favor);
        //     }
        // }

        $datas = CreditoAFavor::withTrashed()
            ->select('credito_a_favors.id','representants.id as representant_id','representants.ci_representant','representants.name as representants_name','credito_a_favors.credito_ammount','credito_a_favors.exchange_ammount')
            ->join('representants', 'representants.id', '=', 'credito_a_favors.representant_id')

            ->join('estudiants', 'estudiants.id', '=', 'credito_a_favors.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')

            ->where('credito_a_favors.exchange_ammount','>',0.009)

            ->where('estudiants.status_active','true')
            ->where('representants.status_active','true')
            ->whereNull('credito_a_favors.deleted_at')
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
