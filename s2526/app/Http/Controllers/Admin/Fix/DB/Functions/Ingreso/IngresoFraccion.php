<?php

namespace App\Http\Controllers\Admin\Fix\DB\Functions\Ingreso;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;

use Illuminate\Support\Facades\DB;

trait IngresoFraccion {

    public function ingreso_fraccions()
    {
        $combinados= RegistroPagoCombinado::all(); dd($combinados->toArray());
    }

}
