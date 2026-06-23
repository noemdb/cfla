<?php

namespace App\Exports;

use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiant;
use DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

// class AdministrativaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
class AdministrativaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
            'Nivel',
            'Plan de Pago',
            'PPId'
        ];
    }
    public function collection()
    {
        $estudiants = Estudiant::select(
            'administrativas.id as administrativa_id','estudiants.id','estudiants.ci_estudiant','estudiants.lastname','estudiants.name',
            DB::raw('concat(grados.name, " ",seccions.name ) as nivel'),'planpagos.name as planpago_name','planpagos.id as planpago_id'

            // DB::raw('concat(representants.phone, " ",representants.cellphone ) as contac_representant'),
            // DB::raw('concat(estudiants.phone, " ",estudiants.cellphone ) as contac_estudiant')
            )
            ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
            ->orderByRaw('estudiants.lastname asc, nivel asc')
            // ->orderby('nivel','asc')
            ->where('estudiants.status_active','true')
            ->wherenull('estudiants.deleted_at')
            ->get();

        // $estudiants = DB::table('administrativas')
        //         ->select('estudiants.ci_estudiant','estudiants.lastname','estudiants.name','administrativas.user_id','administrativas.observations','administrativas.created_at')
        //         ->join('estudiants', 'estudiants.id', '=', 'administrativas.estudiant_id')
        //         ->get();
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
