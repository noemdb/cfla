<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Models\app\Profesor\Pevaluacion;

class PevaluacionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return ['id','profesor_id ','pensum_id','seccion_id','lapso_id','description','ci_profesor','lastname','name'];
    }
    public function collection()
    {
        $pevaluacions = Pevaluacion::select(
            'pevaluacions.id','pevaluacions.profesor_id','pevaluacions.pensum_id','pevaluacions.seccion_id','pevaluacions.lapso_id',
            'pevaluacions.description','profesors.ci_profesor','profesors.lastname','profesors.name'
            )
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->where('profesors.status_active','true')
            ->wherenull('profesors.deleted_at')
            ->get();
        return $pevaluacions;
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
