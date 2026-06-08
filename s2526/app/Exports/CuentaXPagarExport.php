<?php

namespace App\Exports;

use App\Models\app\Estudiant;
use App\Models\app\Planpago\Cuentaxpagar;
use Illuminate\Http\Request;
use App\Models\app\Estudiante\Representant;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CuentaXPagarExport  implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            // 'ID',
            'CI Representante',
            'Nombre Representante',
            'CI Estudiante',
            'Nombre Estudiante',
            // 'Estado',
            'Monto Estudiante',
            'Monto Representante',
            // 'ABN',
            // 'CAF',
            // 'Saldo Estudiante',
            // 'Saldo Representante'

        ];
    }


    public function collection()
    {
        $request            = (!empty($this->request)) ? $this->request : null;
        $cuentaxpagar_id    = (!empty($request->cuentaxpagar_id)) ? $request->cuentaxpagar_id : null  ;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null;

        $datas = collect();

        if (count($request->all())>0) {

            $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);
            $planpago = $cuentaxpagar->planpago;

            $allEstudiants = Estudiant::select('estudiants.*')
                ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->where('estudiants.status_active','true')
                ->where('administrativas.planpago_id',$planpago->id)
                ->orderBy('representants.ci_representant')
                ->groupBy('estudiants.ci_estudiant')
                // ->take(50)
            ;

            $allEstudiants = (isset($ci_representant)) ? $allEstudiants->where('representants.ci_representant', 'like', "%".$ci_representant."%") : $allEstudiants;

            $allEstudiants = $allEstudiants->get();

            $sentinela = Representant::getSentinela();
            $ci_representant_old = null;

            $datas = collect();
            foreach ($allEstudiants as $estudiant) {

                $monto_estudiant = $cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id);

                if ($monto_estudiant > 0) {

                    $data = collect();
                    $representant_ci    = null;
                    $representant_name  = null;
                    $monto_representant = null;

                    $representant = ($estudiant->representant) ? $estudiant->representant : $sentinela ;

                    if ($representant->ci_representant <> $ci_representant_old) {

                        $representant_ci   = $representant->ci_representant ;
                        $representant_name = $representant->name ;

                        foreach ($representant->estudiants as $estudiant_k) {
                            if($estudiant_k->administrativa){
                                $monto_representant = $cuentaxpagar->TotalMontoConceptosXPagar($estudiant_k->id) + $monto_representant;                            
                            }

                        }

                    }

                    $ci_representant_old = $representant->ci_representant;

                    // $status_active = ($estudiant->status_active=='true') ? 'Activo':'Desactivo' ;

                    $data->put('representant_ci',$representant_ci);
                    $data->put('representant_name',$representant_name);
                    $data->put('ci_estudiant',$estudiant->ci_estudiant);
                    $data->put('estudiant_name',$estudiant->fullname);
                    // $data->put('status_active',$status_active);
                    $data->put('monto_estudiant',$monto_estudiant);
                    $data->put('monto_representant',$monto_representant);

                    $datas->push($data);
                }
            }

        }

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
