<?php

namespace App\Http\Controllers\General\Catchment;

use App\Http\Controllers\Controller;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
use Jenssegers\Date\Date;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        return view('general.catchments.index');
    }

    public function interview(Request $request)
    {
        $institucion = Institucion::select('institucions.name', 'institucions.code', 'institucions.address')->first();
        return view('general.catchments.interview',compact('institucion'));
    }

    public function paperId($id)
    {
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('COORDINACIÓN DE BIENESTAR ESTDIANTÍL');

        //COORDINACIÓN DE BIENESTAR ESTDIANTÍL
        //COORDINADORA DE REGISTRO Y CONTROL DE ESTUDIO
        
        $catchment = New Catchment;

        $catchment_interview = CatchmentInterview::find($id);
        $catchment_interview = ($catchment_interview) ? $catchment_interview : New CatchmentInterview ;

        $toDay = Date::now();

        $list_comment = CatchmentInterview::COLUMN_COMMENTS;
        return view('general.catchments.paper',compact('institucion','autoridad1','autoridad2','catchment','catchment_interview','list_comment','toDay'));
    }

    public function paper($identification_number)
    {
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        // $autoridad2 = Autoridad::getAuthority('COORDINADORA DE REGISTRO Y CONTROL DE ESTUDIO');
        $autoridad2 = Autoridad::getAuthority('COORDINACIÓN DE BIENESTAR ESTDIANTÍL');
        $catchment = New Catchment;

        $catchment_interview = CatchmentInterview::where('identification_number',$identification_number)->orderBy('created_at','desc')->first();
        $catchment_interview = ($catchment_interview) ? $catchment_interview : New CatchmentInterview ;

        $toDay = Date::now();

        $list_comment = CatchmentInterview::COLUMN_COMMENTS;
        return view('general.catchments.paper',compact('institucion','autoridad1','autoridad2','catchment','catchment_interview','list_comment','toDay'));
    }

    public function paperBlank()
    {
        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('COORDINACIÓN DE BIENESTAR ESTDIANTÍL');

        $catchment = new Catchment();
        $catchment_interview = new CatchmentInterview();

        $toDay = Date::now();
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;

        return view('general.catchments.paper', compact(
            'institucion',
            'autoridad1',
            'autoridad2',
            'catchment',
            'catchment_interview',
            'list_comment',
            'toDay'
        ));
    }

    public function accepted($token)
    {
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('COORDINADORA DE REGISTRO Y CONTROL DE ESTUDIO');
        $catchment = New Catchment;

        $catchment_interview = CatchmentInterview::where('token',$token)->orderBy('created_at','desc')->first();
        if (empty($catchment_interview)) abort(403, 'Acción no autorizada');

        $link = route('catchments.accepted',$catchment_interview->token);

        $toDay = Date::now();
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;

        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'letter'; // legal, letter
        $view =  View::make('general.catchments.accepted',compact('institucion','autoridad1','autoridad2','catchment','catchment_interview','list_comment','toDay','link'));
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        // return $pdf->stream('CARTA DIGITAL DE ACEPTACIÓN');

        return view('general.catchments.accepted',compact('institucion','autoridad1','autoridad2','catchment','catchment_interview','list_comment','toDay'));
    }

    public function standby($id)
    {
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $director = Autoridad::getAuthority('DIRECTORA');
        $autoridad = Autoridad::getAuthority('COORDINACIÓN DE BIENESTAR ESTDIANTÍL');

        $catchment_interview = CatchmentInterview::findOrFail($id);
        $catchment = ($catchment_interview->catchment) ? $catchment_interview->catchment  : New Catchment;
        if (empty($catchment_interview)) abort(403, 'Acción no autorizada');

        $link = route('catchments.standby',$catchment_interview->id);

        $toDay = Date::now();
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;

        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'letter'; // legal, letter
        $view =  View::make('general.catchments.standby',compact('institucion','autoridad','director','catchment_interview','catchment','list_comment','toDay','link'));
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        return view('general.catchments.standby',compact('institucion','director','autoridad','catchment','catchment_interview','list_comment','toDay'));
    }
}
