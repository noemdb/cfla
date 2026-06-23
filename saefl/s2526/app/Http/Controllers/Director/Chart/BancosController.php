<?php

namespace App\Http\Controllers\Director\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Administracion\Chart\BancosController as AdministracionBancosController;


class BancosController extends Controller
{
    public function __construct()
    {
        $this->administracion_banco = new AdministracionBancosController;
    }

    public function deuda_representate_concepto_extend(Request $request)
    {
        return $this->administracion_banco->deuda_representate_concepto($request);
    }

    public function ingreso_month_extend(Request $request)
    {
        return $this->administracion_banco->IngresoXMonth($request);
    }

    public function ingreso_metodo_extend(Request $request)
    {
        return $this->administracion_banco->IngresoXMetodo($request);
    }


}
