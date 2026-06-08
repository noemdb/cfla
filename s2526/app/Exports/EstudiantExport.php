<?php

namespace App\Exports;

use App\Models\app\Estudiant;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EstudiantExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'ID',
            'Identificador',
            'Apellido',
            'Nombre',
            'Nivel',
            'CI Representante',
            'Representante',
            'Teléfonos Estudiante 1',
            'Teléfonos Estudiante 2',
            'Teléfonos Representante 1',
            'Teléfonos Representante 2',
            'Teléfonos',
            'Deuda',
            'D. Cambiaria',
        ];
    }
    public function collection()
    {
        $estudiants = Estudiant::select(
            'estudiants.id','estudiants.ci_estudiant','estudiants.lastname','estudiants.name',
            DB::raw('concat(grados.name, " ",seccions.name ) as nivel'),
            'representants.ci_representant','representants.name as representants_name',
            'estudiants.phone as estudiants_phone','estudiants.cellphone as estudiants_cellphone',
            'representants.phone as representants_phone','representants.cellphone as representants_cellphone'
            // DB::raw('concat(representants.phone, " ",representants.cellphone ) as contac_representant'),
            // DB::raw('concat(estudiants.phone, " ",estudiants.cellphone ) as contac_estudiant')
            )
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
            ->orderByRaw('nivel asc, estudiants.lastname asc')
            // ->orderby('nivel','asc')
            // ->wherenull('estudiants.deleted_at')
            ->get();

        $datas = collect([]);
        foreach ($estudiants as $estudiant) {
            $deuda_exchange = $estudiant->exchange_ammount_expire_bill;
            $deuda = $estudiant->ammount_expire_bill;
            // $deuda = 100;
            if ($deuda_exchange>0) {
                $data = collect($estudiant->toArray());
                $data->pop();

                $phone = $estudiant->estudiants_phone . ' ' .$estudiant->estudiants_cellphone;
                $phone .= (!empty($estudiant->representant)) ? $estudiant->representant->fullphone : null ;

                $data->put('phone', substr($phone,0,-1));
                $data->put('ammount_expire_bill', $deuda);
                $data->put('exchange_ammount_expire_bill', $deuda_exchange);
                $datas->push($data);
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
