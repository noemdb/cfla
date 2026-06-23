<?php

namespace App\Http\Controllers\Planning\PDF;

use App\Http\Controllers\Controller;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pensum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class CompetitionController extends Controller
{

    public function batch($grado_id = null)
    {

        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'letter'; // legal, letter

        $grado = Grado::findOrFail($grado_id);

        $questions = DebateQuestion::getByGradoId($grado_id);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');
        $fecha = Carbon::now()->format('d-m-Y');

        $view =  View::make('plannings.competitions.pdf.batch',
        compact(
            'questions','grado','grado',
            'institucion','autoridad1','autoridad2','fecha'))
        ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $view;
    }

    public function cards($pensum_id,$category)
    {

        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'letter'; // legal, letter

        $pensum = Pensum::findOrFail($pensum_id);

        $list_category = DebateQuestion::CATEGORY;

        if (array_key_exists($category, $list_category)) {

            $grado = $pensum->grado;
            // echo $category; exit;
            $questions = DebateQuestion::where('pensum_id',$pensum_id)->orderBy('weighting')->where('category',$category)->get(); //dd($pensum_id,$category,$questions);

            $institucion = Institucion::OrderBy('created_at','DESC')->first();
            $autoridad1 = Autoridad::getAuthority('DIRECTORA');
            $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');
            $fecha = Carbon::now()->format('d-m-Y');

            // views/plannings/competitions/pdf/cards.blade.php
            $view =  View::make('plannings.competitions.pdf.cards',
            compact(
                'questions','grado','grado','category',
                'institucion','autoridad1','autoridad2','fecha'))
            ->render();
            $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
            $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
            return $view;
        }
    }

    public function list($pensum_id,$category)
    {
        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'letter'; // legal, letter

        $pensum = Pensum::findOrFail($pensum_id);

        $list_category = DebateQuestion::CATEGORY;

        if (array_key_exists($category, $list_category)) {

            $grado = $pensum->grado;
            // echo $category; exit;
            $questions = DebateQuestion::where('pensum_id',$pensum_id)->orderBy('weighting')->where('category',$category)->get(); //dd($pensum_id,$category,$questions);

            $institucion = Institucion::OrderBy('created_at','DESC')->first();
            $autoridad1 = Autoridad::getAuthority('DIRECTORA');
            $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');
            $fecha = Carbon::now()->format('d-m-Y');

            // views/plannings/competitions/pdf/cards.blade.php
            $view =  View::make('plannings.competitions.pdf.list',
            compact(
                'questions','grado','grado','category',
                'institucion','autoridad1','autoridad2','fecha'))
            ->render();
            $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
            $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
            return $view;
        }
    }
}
