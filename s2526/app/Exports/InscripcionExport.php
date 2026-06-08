<?php

namespace App\Exports;

use App\Models\app\Estudiante\Administrativa;
use DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Models\app\Estudiant;

class InscripcionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'ID',
            'EId',
            'Identificador',
            'Apellido',
            'Nombre',
            'Grado',
            'GId',
            'SId'
        ];
    }
    public function collection()
    {
        $estudiants = Estudiant::select(
            'inscripcions.id as inscripcion_id','estudiants.id','estudiants.ci_estudiant','estudiants.lastname','estudiants.name',
            DB::raw('concat(grados.name, " ",seccions.name ) as nivel'),'grados.id as grado_id','seccions.id as seccion_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            // ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->orderByRaw('estudiants.lastname asc, nivel asc')
            ->where('estudiants.status_active','true')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')
            ->get();
        return $estudiants;

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
