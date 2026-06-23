<?php

namespace App\Models\app\Estudiante\Functions\Administrativas;

use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

trait AdministrativaMeta
{

    public function generatePdfPathConstanciaAdministrativa($id)
    {
        // Obtén los datos necesarios

        $estudiant = Estudiant::findOrFail($id); //dd($estudiant);
        $inscripcion = $estudiant->inscripcion;
        $administrativa = $estudiant->administrativa;
        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('2');//ADMINISTRADOR

        // Renderiza la vista
        $view =  View::make('administracion.administrativas.constancia.pdf', compact('administrativa','estudiant','institucion','autoridad1','autoridad2'))->render();

        // Configura y genera el PDF
        $pdf = App::make('dompdf.wrapper')->setOptions([
            'dpi' => 72,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $pdf->loadHTML($view)->setPaper('letter', 'portrait');

        // Define el nombre del archivo PDF
        $fileName = 'Constancia-Inscripcion_Administrativa_Ident_' . str_shuffle($estudiant->id . Str::random(10) ). '.pdf';
        $filePath = "tmp/PDF/{$fileName}";

        // Guarda el archivo en el disco público
        /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
        Storage::disk('public')->put($filePath, $pdf->output());

        // Retorna la ruta accesible públicamente
        return Storage::disk('public')->url($filePath);
    }


    public function generatePdfPathSolvenciaAdministrativa($id)
    {
        // Obtén los datos necesarios

        $estudiant = Estudiant::findOrFail($id); //dd($estudiant);
        $inscripcion = $estudiant->inscripcion;
        $administrativa = $estudiant->administrativa;
        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('2');//ADMINISTRADOR

        // Renderiza la vista
        $view =  View::make('administracion.administrativas.solvencia.pdf', compact('administrativa','estudiant','institucion','autoridad1','autoridad2'))->render();

        // Configura y genera el PDF
        $pdf = App::make('dompdf.wrapper')->setOptions([
            'dpi' => 72,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $pdf->loadHTML($view)->setPaper('letter', 'portrait');

        // Define el nombre del archivo PDF
        $fileName = 'Solvencia_Administrativa_Ident_' . str_shuffle($estudiant->id . Str::random(10) ). '.pdf';
        $filePath = "tmp/PDF/{$fileName}";

        // Guarda el archivo en el disco público
        /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
        Storage::disk('public')->put($filePath, $pdf->output());

        // Retorna la ruta accesible públicamente
        return Storage::disk('public')->url($filePath);
    }


}
