<?php

namespace App\Http\Controllers\Director\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Administracion\Chart\RegistroPagosController as AdministracionRegistroPagosController;

class RegistroPagosController extends Controller
{
    public function __construct()
    {
        $this->administracion_estudiant = new AdministracionRegistroPagosController;
    }

    public function actividades_extend(Request $request)
    {
        return $this->administracion_estudiant->actividades($request);
    }
}
