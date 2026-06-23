<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Profesor;
use Illuminate\Http\Request;

class SocialActionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_control']);
    }

    public function index(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null;
        $ci_estudiant = (!empty($request->ci_estudiant)) ? $request->ci_estudiant:null;
        $grado = Grado::find($grado_id);
        $estudiants = ($grado) ? $grado->estudiants : collect() ;
        $profesors = Profesor::getProfesorGuia();

        $list_grado = Grado::list_pestudio_grado();

        return view('administracion.social_actions.index', compact('estudiants','profesors','list_grado','grado_id','ci_estudiant'));
    }

    public function listado(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null;
        $ci_estudiant = (!empty($request->ci_estudiant)) ? $request->ci_estudiant:null;
        $grado = Grado::find($grado_id);
        $estudiants = ($grado) ? $grado->estudiants : collect() ;
        $profesors = Profesor::getProfesorGuia();

        $list_grado = Grado::list_pestudio_grado();

        return view('administracion.social_actions.listado', compact('estudiants','profesors','list_grado','grado_id','ci_estudiant'));
    }
}
