<?php

namespace App\Http\Controllers\Permission\PDF;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Permission\Pase;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class PaseController extends Controller
{
    public function certificate($id){

        $pase = Pase::findOrfail($id);
        $user = $pase->user;
        $estudiant = $pase->estudiant;
        $representant = $estudiant->representant;
        $profesor = $pase->profesor;
        $pensum = $pase->pensum; //dd();

        $lapso = Lapso::current();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority(2); //dd($autoridad1);
        $autoridad2 = Autoridad::getTipoAuthority(1);

        $compact = ['pase','user','estudiant','representant','profesor','pensum','institucion','autoridad1','autoridad2','lapso'];
        $view =  View::make('permissions.pases.pdf.certificate',compact($compact))->render();
        // /home/nuser/code/s2223/resources/views/administracion/bienestars/pdf/ficha.blade.php

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72; //legal, lettet
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'ficha_estudiante';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

    }
}
