<?php

namespace App\Models\app\Estudiante\Functions\Inscripcions;

use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

trait InscripcionMeta
{

    public function generatePdfPathBoletinPDFEmail($estudiant_id,$lapso_id)
    {
        // Obtén los datos necesarios

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $baremo = new Baremo();

        $estudiant = Estudiant::findOrFail($estudiant_id);
        $lapso = Lapso::findOrFail($lapso_id);

        $seccion = $estudiant->inscripcion->seccion;
        $grado = $seccion->grado;
        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);
        $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first();
        $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;});
        $pensums_subareas = $grado->pensums_subareas;

        $lapsos = Lapso::all();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        // Renderiza la vista
        $view =  View::make('administracion.boletins.pdf.'.$pestudio->code,
        compact('estudiant','seccion','grado','pestudio','baremos','baremo','profesor_guia','pensums','lapsos','lapso','lapso_id','institucion','autoridad1','autoridad2'))
        ->render();

        // Configura y genera el PDF
        $pdf = App::make('dompdf.wrapper')->setOptions([
            'dpi' => 72,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $pdf->loadHTML($view)->setPaper('letter', 'portrait');

        // Define el nombre del archivo PDF
        $fileName = 'Informe_Evaluacion_' . str_shuffle($estudiant->id . Str::random(10) ) . '.pdf';
        $filePath = "tmp/PDF/{$fileName}";

        // Guarda el archivo en el disco público
        Storage::disk('public')->put($filePath, $pdf->output());

        // Ruta absoluta del archivo para adjuntar en el mail
        $fullPath = Storage::disk('public')->path($filePath);

        // Retorna array con info útil
        return [
            'public_url' => Storage::disk('public')->url($filePath),
            'path' => $fullPath,
            'filename' => $fileName
        ];
    }


    public function generatePdfPathBoletinPDF($estudiant_id,$lapso_id)
    {
        // Obtén los datos necesarios

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $baremo = new Baremo();

        $estudiant = Estudiant::findOrFail($estudiant_id);
        $lapso = Lapso::findOrFail($lapso_id);

        $seccion = $estudiant->inscripcion->seccion;
        $grado = $seccion->grado;
        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);
        $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first();
        $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;});
        $pensums_subareas = $grado->pensums_subareas;

        $lapsos = Lapso::all();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        // Renderiza la vista
        $view =  View::make('administracion.boletins.pdf.'.$pestudio->code,
        compact('estudiant','seccion','grado','pestudio','baremos','baremo','profesor_guia','pensums','lapsos','lapso','lapso_id','institucion','autoridad1','autoridad2'))
        ->render();

        // Configura y genera el PDF
        $pdf = App::make('dompdf.wrapper')->setOptions([
            'dpi' => 72,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $pdf->loadHTML($view)->setPaper('letter', 'portrait');

        // Define el nombre del archivo PDF
        $fileName = 'Informe_Evaluacion_' . str_shuffle($estudiant->id . Str::random(10) ) . '.pdf';
        $filePath = "tmp/PDF/{$fileName}";

        // Guarda el archivo en el disco público
        /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
        Storage::disk('public')->put($filePath, $pdf->output());

        // Retorna la ruta accesible públicamente
        return Storage::disk('public')->url($filePath);
    }


    public function generatePdfPathEstudioPDF($id)
    {
        // Obtén los datos necesarios

        $estudiant = Estudiant::findOrFail($id); //dd($estudiant);
        $inscripcion = $estudiant->inscripcion;
        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1'); // REPRESENTANTE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3'); // JEFE DE CONTROL DE ESTUDIO

        // Renderiza la vista
        $view =  View::make('administracion.inscripciones.constancia.pdf.estudio', compact('inscripcion','estudiant','institucion','autoridad1','autoridad2'))->render();

        // Configura y genera el PDF
        $pdf = App::make('dompdf.wrapper')->setOptions([
            'dpi' => 72,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $pdf->loadHTML($view)->setPaper('letter', 'portrait');

        // Define el nombre del archivo PDF
        $fileName = 'Constancia_Estudio_Ident_' . str_shuffle($estudiant->id . Str::random(10) ). '.pdf';
        $filePath = "tmp/PDF/{$fileName}";

        // Guarda el archivo en el disco público
        /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
        Storage::disk('public')->put($filePath, $pdf->output());

        // Retorna la ruta accesible públicamente
        return Storage::disk('public')->url($filePath);
    }

    public function generatePdfPathConstanciaPDF($id)
    {
        // Obtén los datos necesarios

        $estudiant = Estudiant::findOrFail($id); //dd($estudiant);
        $inscripcion = $estudiant->inscripcion;
        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1'); // REPRESENTANTE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3'); // JEFE DE CONTROL DE ESTUDIO

        // Renderiza la vista
        $view = View::make('administracion.inscripciones.constancia.pdf.inscripcion', compact('inscripcion', 'estudiant', 'institucion', 'autoridad1', 'autoridad2'))->render();

        // Configura y genera el PDF
        $pdf = App::make('dompdf.wrapper')->setOptions([
            'dpi' => 72,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $pdf->loadHTML($view)->setPaper('letter', 'portrait');

        // Define el nombre del archivo PDF
        $fileName = 'Constancia_Inscripcion_Ident_' . str_shuffle($estudiant->id . Str::random(10) ) . '.pdf';
        $filePath = "tmp/PDF/{$fileName}";

        // Guarda el archivo en el disco público
        /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
        Storage::disk('public')->put($filePath, $pdf->output());

        // Retorna la ruta accesible públicamente
        return Storage::disk('public')->url($filePath);
    }


}
