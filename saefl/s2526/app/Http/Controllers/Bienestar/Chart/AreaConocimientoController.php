<?php

namespace App\Http\Controllers\Bienestar\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Administracion\Chart\AreaConocimientoController as AdministracionAreaConocimientoController;

class AreaConocimientoController extends Controller
{
    public function __construct()
    {
        $this->administracion_area_conocimiento = new AdministracionAreaConocimientoController;
    }

    public function promedio_x_area_extend(Request $request)
    {
        return $this->administracion_area_conocimiento->promedio_x_area($request);
    }

}
