<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\Representant;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RepresentantFulltExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return ['id','username','ci_representant','name','phone','cellphone','email','gsemail','status_active'];
    }
    public function collection()
    {
        $representants =
                Representant::select('representants.id','users.username','representants.ci_representant','representants.name','representants.phone','representants.cellphone','representants.email','representants.gsemail','representants.status_active')
                    ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                    // ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    // ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    // ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                    // ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    // ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                    ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->leftjoin('users', 'users.id', '=', 'representants.user_id')
                    ->where('representants.status_active','true')
                    ->where('estudiants.status_active','true')
                    ->groupby('representants.id')
                    ->get();
                    // dd($representants);
        return $representants;

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
