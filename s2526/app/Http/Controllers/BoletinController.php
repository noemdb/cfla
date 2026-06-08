<?php

namespace App\Http\Controllers;

use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Lapso;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;


class BoletinController extends Controller
{
    public function showBoletinByToken($token)
    {
        // $token = substr($token, -40);
        $token = str_replace('%7B%7B1%7D%7D', '', $token);

        $estudiant = Estudiant::where('token', $token)->first();

        if (!$estudiant) {
            return response()->view('errors.estudiant-not-found', [], 404);
        }

        $inscripcion = $estudiant->inscripcion;
        $administrativa = $estudiant->administrativa; //dd($inscripcion,$administrativa);
        if (!$inscripcion || !$administrativa) {
            return response()->view('errors.inscripcion-not-found', [], 404);
        }

        $representant = $estudiant->representant;
        if (!$representant->status_solvent_exchange) {
            return response()->view('errors.estudiant-no-solvent', [], 403);
        }

        // $lapso = Lapso::current();

        // Verificar si existe un lapso_anterior
        $now = Carbon::now()->format('Y-m-d');
        $lapso = Lapso::where('ffinal','<=',$now)->orderBy('ffinal','desc')->first(); //dd($lapso);

        if (!$lapso) {
            return response()->view('errors.estudiant-lapso-not-found', [], 404);
        }

        $pdf = $this->generateBoletinPDF($estudiant->id, $lapso->id);

        $fileName = 'InformeNotas' . $estudiant->id . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function generateBoletinPDF($estudiant_id, $lapso_id)
    {
        $orientacion = 'portrait';
        $paper  = 'letter';
        $baremo = new Baremo();

        $estudiant = Estudiant::findOrFail($estudiant_id);
        $lapso = Lapso::findOrFail($lapso_id);
        $seccion = $estudiant->inscripcion->seccion;
        $grado = $seccion->grado;
        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);
        $profesor_guia = $grado->profesor_guias->where('seccion_id', $seccion->id)->first();
        $pensums = $grado->pensums->sortBy(fn($v) => $v->asignatura->order ?? null);
        $pensums_subareas = $grado->pensums_subareas;

        $lapsos = Lapso::all();
        $institucion = Institucion::latest()->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view = View::make('administracion.boletins.pdf.' . $pestudio->code,
            compact('estudiant','seccion','grado','pestudio','baremos','baremo','profesor_guia','pensums','lapsos','lapso','lapso_id','institucion','autoridad1','autoridad2')
        )->render();

        $pdf = App::make('dompdf.wrapper')->setOptions([
            'dpi' => 72,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        return $pdf;
    }

    public function generatePdfPathBoletinPDF($estudiant_id, $lapso_id)
    {
        $orientacion = 'portrait';
        $paper  = 'letter';
        $baremo = new Baremo();

        $estudiant = Estudiant::findOrFail($estudiant_id);
        $lapso = Lapso::findOrFail($lapso_id);
        $seccion = $estudiant->inscripcion->seccion;
        $grado = $seccion->grado;
        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);
        $profesor_guia = $grado->profesor_guias->where('seccion_id', $seccion->id)->first();
        $pensums = $grado->pensums->sortBy(fn($v) => $v->asignatura->order ?? null);
        $pensums_subareas = $grado->pensums_subareas;

        $lapsos = Lapso::all();
        $institucion = Institucion::latest()->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view = View::make('administracion.boletins.pdf.' . $pestudio->code,
            compact('estudiant','seccion','grado','pestudio','baremos','baremo','profesor_guia','pensums','lapsos','lapso','lapso_id','institucion','autoridad1','autoridad2')
        )->render();

        $pdf = App::make('dompdf.wrapper')->setOptions([
            'dpi' => 72,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        $fileName = 'Informe_Evaluacion_' . str_shuffle($estudiant->id . Str::random(10)) . '.pdf';
        $filePath = "tmp/PDF/{$fileName}";

        Storage::disk('public')->put($filePath, $pdf->output());

        return Storage::disk('public')->url($filePath);
        
    }   


}
