<?php

namespace App\Http\Controllers\Academico\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Administracion\Chart\InscripcionController as AdministracionInscripcionController;

class InscripcionController extends Controller
{

    public function __construct()
    {
        $this->administracion_inscripcion = new AdministracionInscripcionController;
    }

    public function gender_extend(Request $request)
    {
        return $this->administracion_inscripcion->gender($request);
    }

    public function genderxplan_extend(Request $request)
    {
        return $this->administracion_inscripcion->genderxplan($request);
    }

    public function genderxgrado_extend(Request $request)
    {
        return $this->administracion_inscripcion->genderxplan($request);
    }

}
