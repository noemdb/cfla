<?php

namespace App\Http\Controllers\Bienestar\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Administracion\Chart\EstudiantController as AdministracionEstudiantController;

class EstudiantController extends Controller
{
    public function __construct()
    {
        $this->administracion_estudiant = new AdministracionEstudiantController;
    }

    public function estudiants_municipios_extend(Request $request)
    {
        return $this->administracion_estudiant->estudiants_municipios($request);
    }
    public function estudiants_municipios_pestudio_extend(Request $request)
    {
        return $this->administracion_estudiant->estudiants_municipios_pestudio($request);
    }
}
