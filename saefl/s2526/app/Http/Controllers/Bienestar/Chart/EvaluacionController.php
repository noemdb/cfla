<?php

namespace App\Http\Controllers\Bienestar\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Administracion\Chart\EvaluacionController as AdministracionEvaluacionController;

class EvaluacionController extends Controller
{
    public function __construct()
    {
        $this->administracion_estudiant = new AdministracionEvaluacionController;
    }

    public function actividades_extend(Request $request)
    {
        return $this->administracion_estudiant->actividades($request);
    }
}
