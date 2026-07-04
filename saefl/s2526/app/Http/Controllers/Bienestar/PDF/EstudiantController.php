<?php

namespace App\Http\Controllers\Bienestar\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Jenssegers\Date\Date;

class EstudiantController extends Controller
{

	public function fichaEstudiant($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $representant = $estudiant->representant;
        $student_record = StudentRecord::where('estudiant_id',$estudiant_id)->first();
        $incidents = $estudiant->incidents; //dd($incidents);
        $toDate = Date::now()->format('d F Y');

        if (empty($student_record)) {
            $student_record = New StudentRecord;
            $student_record->estudiant_id = $estudiant_id; //dd($student_record);
            $student_record->save();
        }

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority(2); //dd($autoridad1);
        $autoridad2 = Autoridad::getTipoAuthority(1);

        $compact = ['student_record','estudiant','incidents','institucion','autoridad1','autoridad2','toDate','estudiant','representant'];
        $view =  View::make('livewire.bienestar.estudiant.pdf.expediente',compact($compact))->render();
        // /home/nuser/code/s2223/resources/views/administracion/bienestars/pdf/ficha.blade.php

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72; //legal, lettet
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'expediente_estudiante';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

    }

    public function ficha($student_record_id){

        $student_record = StudentRecord::findOrfail($student_record_id);
        $estudiant = Estudiant::findOrfail($student_record->estudiant_id);
        $representant = $estudiant->representant;
        $toDate = Date::now()->format('d F Y');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority(2); //dd($autoridad1);
        $autoridad2 = Autoridad::getTipoAuthority(1);

        $compact = ['student_record','estudiant','institucion','autoridad1','autoridad2','toDate','representant'];
        $view =  View::make('livewire.bienestar.estudiant.pdf.expediente',compact($compact))->render();
        //views/livewire/bienestar/estudiant/pdf/expediente.blade.php

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72; //legal, lettet
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'ficha_estudiante';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

    }
}
