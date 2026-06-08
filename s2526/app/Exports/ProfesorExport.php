<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Models\app\Pescolar\Profesor;

class ProfesorExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'ID',
            'user_id',
            'username',
            'Identificador',
            'Apellido',
            'Nombre',
            'gsemail',
            'gspassword',
            'status_active',
        ];
    }
    public function collection()
    {
        $profesors = Profesor::select(
            'profesors.id','profesors.user_id','users.username','profesors.ci_profesor','profesors.lastname','profesors.name','profesors.gsemail','profesors.gspassword','profesors.status_active')
            ->leftjoin('users', 'users.id', '=', 'profesors.user_id')
            ->where('profesors.status_active','true')
            ->get();
        return $profesors;

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
