<?php

namespace App\Exports;

use App\Models\app\Estudiante\Boletin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BoletinExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return ['boletin_id','evaluacion_id','estudiant_id','ci_estudiant','nota'];
    }

    public function collection()
    {
        $request            = (!empty($this->request)) ? $this->request : null;
        $lapso_id           = (!empty($request->lapso_id)) ? $request->lapso_id : null ;
        $grado_id           = (!empty($request->grado_id)) ? $request->grado_id : null ;

        $boletins = Boletin::select(
            'boletins.id','boletins.evaluacion_id','boletins.estudiant_id','estudiants.ci_estudiant','boletins.nota')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')

            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')

            ->wherenull('evaluacions.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->where('estudiants.status_active','true');

            //->get(); //dd($evaluacions);

        $boletins = ($lapso_id) ? $boletins->where('lapsos.id',$lapso_id) : $boletins ;
        $boletins = ($grado_id) ? $boletins->where('grados.id',$grado_id) : $boletins ;

        $boletins = $boletins->get(); //dd($datas);

        return $boletins;
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
