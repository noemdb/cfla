<?php

namespace App\Imports;

use App\boletins;
use Maatwebsite\Excel\Concerns\ToModel;

class BoletinsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new boletins([
            //
        ]);
    }
}
