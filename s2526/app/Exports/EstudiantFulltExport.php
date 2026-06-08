<?php

namespace App\Exports;

use App\Models\app\Estudiant;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EstudiantFulltExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'id','username','type_ci_id','ci_estudiant','lastname','name','gender',
            'date_birth','city_birth','town_hall_birth','state_birth','country_birth','dir_address','phone','cellphone','email','gsemail',
            'status_active','representant_id','ci_representant','representant_name'
        ];
    }
    public function collection()
    {
        $estudiants = Estudiant::select('estudiants.id','users.username','estudiants.type_ci_id','estudiants.ci_estudiant','estudiants.lastname','estudiants.name','estudiants.gender',
            'date_birth','estudiants.city_birth','estudiants.town_hall_birth','estudiants.state_birth','estudiants.country_birth','estudiants.dir_address','estudiants.phone',
            'estudiants.cellphone','estudiants.email','estudiants.gsemail','estudiants.status_active','representants.id as representant_id',
            'representants.ci_representant','representants.name as representant_name')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->leftjoin('users', 'users.id', '=', 'estudiants.user_id')
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
