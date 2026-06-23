<?php

namespace App\Http\Controllers\Administracion\PDF;

use App\Http\Controllers\Controller;
use App\Models\app\Cobranzas\CollPromise;
use Illuminate\Http\Request;

class CollectionPoliticalController extends Controller
{
    public function coll_promise($coll_promise_id){
        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet

        $coll_promise = CollPromise::findOrFail($coll_promise_id);
        $representant = $coll_promise->representant;

        $view =  \View::make('administracion.collections.coll_promises.pdf.acta',
        compact('coll_promise','representant'))
        ->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('Boletin');
        return $view;
    }
}
