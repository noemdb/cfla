<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinRevision;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BoletinRevisionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_control']);
    }

    public function index(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $estudiants = collect();
        $baremo = new Baremo();
        if ($grado_id || $seccion_id) {
            $estudiants = Estudiant::select('estudiants.*')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
                ->join('grados', 'seccions.grado_id', '=', 'grados.id')
                ->Where('estudiants.status_active', '=', 'true');

            $estudiants = ($grado_id) ? $estudiants->Where('grados.id', '=', $grado_id) : $estudiants;
            $estudiants = ($seccion_id) ? $estudiants->Where('seccions.id', '=', $seccion_id) : $estudiants;

            $estudiants = $estudiants->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : collect();

        $grado = ($grado_id) ? Grado::findOrFail($grado_id) : collect();
        $pensums = ($grado_id) ? $grado->pensums : collect();

        return view(
            'administracion.boletin_revisions.index',
            compact('estudiants', 'pensums', 'list_grado', 'grado_id', 'list_seccion', 'seccion_id', 'baremo')
        );
    }




    public function resumen_final(Request $request)
    {
        /*******************request****************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;

        /*******************inicializaciones****************************/
        $seccions = Seccion::OrderBy('created_at', 'desc');

        /*******************query****************************/
        $seccions = ($grado_id) ? $seccions->where('grado_id', $grado_id) : $seccions;
        $seccions = ($seccion_id) ? $seccions->where('id', $seccion_id) : $seccions;

        $seccions = $seccions->get();
        $seccions = $seccions->sortByDesc(function ($value, $key) {
            return (!empty($value->grado->pestudio->code)) ? $value->grado->pestudio->code : null;
        });

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : collect();

        return view(
            'administracion.boletin_revisions.resumen_final',
            compact('seccions', 'grado_id', 'list_grado', 'seccion_id', 'list_seccion')
        );
    }
}
