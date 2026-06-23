<?php

namespace App\Http\Controllers\Profesor\PDF;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\ProfesorGuia;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

use App\Models\app\SocialAction\CommunityAction;
use Jenssegers\Date\Date;

class CommunityActionController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function community_action($id){
        $community_action = CommunityAction::findOrFail($id);
        $user = $community_action->user;
        $grado = $community_action->grado;
        $profesor = $this->profesor;

        $date = Date::now();
        $fecha = 'a los '.$date->format('j').' días del mes de '.$date->format('F').' del año '.$date->format('Y');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3'); //JEFE DE CONTROL DE ESTUDIO

        $view =  View::make('profesors.social_actions.pdf.community_action', compact('community_action','grado','profesor','institucion','autoridad1','autoridad2','date'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true,'isHtml5ParserEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('letter','portrait'); //landscape, // legal, letter
        
        $name_file = 'Certificación de Cumplimiento';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

        // return $pdf->stream($name_file);
        
    }

    public function profesor($id){
        $profesor = Profesor::findOrFail($id);
        $grado = $profesor->grado_guia;

        $date = Date::now();
        $fecha = 'a los '.$date->format('j').' días del mes de '.$date->format('F').' del año '.$date->format('Y');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3'); //JEFE DE CONTROL DE ESTUDIO

        $view =  View::make('profesors.social_actions.pdf.profesor', compact('grado','profesor','institucion','autoridad1','autoridad2','date'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true,'isHtml5ParserEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('letter','portrait'); //landscape, // legal, letter
        
        $name_file = 'Certificación de Cumplimiento';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

        // return $pdf->stream($name_file);
        
    }

    public function estudiant($id){
        $estudiant = Estudiant::findOrFail($id);
        $grado = $estudiant->grado;
        $pestudio = $estudiant->pestudio;
        $profesor = $estudiant->profesor_guia;

        $date = Date::now();
        $fecha = 'a los '.$date->format('j').' días del mes de '.$date->format('F').' del año '.$date->format('Y');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3'); //JEFE DE CONTROL DE ESTUDIO

        $view =  View::make('profesors.social_actions.pdf.estudiant', compact('profesor','grado','pestudio','estudiant','institucion','autoridad1','autoridad2','date'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true,'isHtml5ParserEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('letter','portrait'); //landscape, // legal, letter
        
        $name_file = 'Certificación de Cumplimiento';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);        
    }

}
