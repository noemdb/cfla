<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\Representant;

class RepresentantExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'ID',
            'Identificador',
            'Representante',
            'Teléfonos Representante 1',
            'Teléfonos Representante 2',
            'Teléfonos',
            'Créditos',
            'Abonos',
            'Deuda actual',
            'Deuda Total',
            // 'Saldo a favor',
        ];
    }
    public function collection()
    {
        $representants = Representant::select('representants.id','representants.ci_representant','representants.name as representants_name',
            'representants.phone as representants_phone','representants.cellphone as representants_cellphone'
            // DB::raw('concat(representants.phone, " ",representants.cellphone ) as contac_representant')
            )
            // ->join('estudiants', 'estudiants.id', '=', 'representants.estudiant_id')
            // ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            // ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->active('true')
            ->groupby('representants.ci_representant')
            ->orderBy('representants_name')
            ->get();

        $datas = collect([]);
        foreach ($representants as $representant) {
            $deuda = $representant->ammount_expire_bill;
            // $deuda = 100;
            $abono = $representant->total_abono;
            $credito = $representant->total_credito;
            $total_deuda = $deuda - ( $abono + $credito );
            $total_saldo = ($total_deuda<0) ? (-1) * ($total_deuda) : null ;
            $total_deuda = ($total_deuda>=0) ? $total_deuda : null ;

            if ($total_deuda>0) {
                $data = collect($representant->toArray());
                $data->pop();
                $data->put('phone_estudiant', $representant->fullphone);
                $data->put('credito', $credito);
                $data->put('abono', $abono);
                $data->put('ammount_expire_bill', $deuda);
                $data->put('total_deuda', $total_deuda);
                // $data->put('total_saldo', $total_saldo);
                $datas->push($data);
            }
            unset($deuda,$abono,$credito);
        }
        // dd($datas);
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
