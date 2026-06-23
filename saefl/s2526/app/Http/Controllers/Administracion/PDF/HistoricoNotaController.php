<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\app\HistoricoNota;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Baremo;
use Illuminate\Support\Facades\Auth;

//Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class HistoricoNotaController extends Controller
{
    public function certificacion($historico_nota_id){
        $orientacion = 'portrait';
        // $orientacion = 'landscape';
        $paper  = 'lettet';

        $fecha = Date::now();
        $pescolar_ffinal = Session::get('pescolar_ffinal');

        $historico_nota = HistoricoNota::findOrFail($historico_nota_id);
        $pestudio = $historico_nota->pestudio;
        $baremos = $pestudio->baremos;
        //$baremos = $pestudio->getBaremos($lapso->id ?? null);

        $fecha_expedicion = $historico_nota->fecha_expedicion;
        $fecha_certificacion = $pestudio->fecha_certificacion;
        $fecha_remision = now()->format('Y-m-d');
        if ($pestudio) $fecha_remision = ($pestudio->fecha_certificacion) ?  $pestudio->fecha_certificacion : $fecha_remision ; //dd($fecha_expedicion);
        $fecha_remision = Date::createFromDate($fecha_remision);

        $estudiant = $historico_nota->estudiant;
        $all_HNotas = $estudiant->GetAllHNotas($pestudio->id,'true');
        $oinstitucions = $estudiant->GetAllInstitucions($pestudio->id,'true'); 

        $n=0;
        $arr_institucion = array();
        foreach ($oinstitucions as $institucion) {
            $arr_institucion[$institucion->id] = ++$n;
        }

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('COORDINADORA DE REGISTRO Y CONTROL DE ESTUDIO'); //dd($autoridad2);
        
        return view('administracion.historico_notas.certificacion.pdf.'.$pestudio->code,
        compact('historico_nota','oinstitucions','arr_institucion','estudiant','fecha','fecha_remision','baremos','pestudio','institucion','autoridad1','autoridad2'));
    }
}
