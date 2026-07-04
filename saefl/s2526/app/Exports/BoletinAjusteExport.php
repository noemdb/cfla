<?php

namespace App\Exports;

use App\BoletinAjuste;
use Maatwebsite\Excel\Concerns\FromCollection;

class BoletinAjusteExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return BoletinAjuste::all();
    }
}
