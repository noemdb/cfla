<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\app\Profesor\Pevaluacion;

class EvaluacionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return ['id','fecha','description','pevaluacion_id ','pevaluacion_description','grado_name','seccion_name','lapso_name','ci_profesor','lastname','name','asignatura_code_sm'];
    }
    public function collection()
    {
        $evaluacions = Evaluacion::select(
            'evaluacions.id','evaluacions.fecha','evaluacions.description','pevaluacions.id as pevaluacion_id','pevaluacions.description as pevaluacion_description','grados.name as grado_name',
            'seccions.name as seccion_name','lapsos.name as lapso_name','profesors.ci_profesor','profesors.lastname','profesors.name','asignaturas.code as asignatura_code'
            )
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')

            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('profesors.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            ->wherenull('lapsos.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('asignaturas.deleted_at')

            ->where('profesors.status_active','true')

            ->get(); //dd($evaluacions);
        return $evaluacions;
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
