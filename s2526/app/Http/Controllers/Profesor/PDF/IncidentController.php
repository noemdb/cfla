<?php

namespace App\Http\Controllers\Profesor\PDF;

use App\Http\Controllers\Controller;
use App\Models\app\Incident\Incident;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

use Carbon\Carbon;

use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Jenssegers\Date\Date;

class IncidentController extends Controller
{
    public function fichaEstudiant($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $representant = $estudiant->representant;
        $student_record = StudentRecord::where('estudiant_id',$estudiant_id)->first();
        $enrollment = Enrollment::where('ci_estudiant',$estudiant->ci_estudiant)->first();
        $incidents = $estudiant->incidents;
        $toDate = Date::now()->format('d F Y');

        if (empty($student_record)) {
            $student_record = New StudentRecord;
            $student_record->estudiant_id = $estudiant_id;
            $student_record->save();
        }

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority(2);
        $autoridad2 = Autoridad::getTipoAuthority(1);

        $compact = ['enrollment','student_record','estudiant','incidents','institucion','autoridad1','autoridad2','toDate','estudiant','representant'];
        $view =  View::make('livewire.bienestar.estudiant.pdf.expediente',compact($compact))->render(); //dd($view);

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72; //legal, lettet
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'expediente_estudiante';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

    }

    public function ficha($incident_id){

        $incident = Incident::findOrfail($incident_id);
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);
        $incidents = $estudiant->incidents;
        $representant = $estudiant->representant;
        $toDate = Date::now()->format('d F Y');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority(2);
        $autoridad2 = Autoridad::getTipoAuthority(1);

        $compact = ['incident','estudiant','incidents','institucion','autoridad1','autoridad2','toDate','representant'];
        $view =  View::make('bienestars.incidents.pdf.ficha',compact($compact))->render();

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72;
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        $name_file = 'ficha_incidencia';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

    }
}
