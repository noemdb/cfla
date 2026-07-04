<?php

namespace App\Http\Controllers\Bienestar\PDF;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;

use Carbon\Carbon;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;


class StudentRecordController extends Controller
{
    public function ficha($student_record_id){

        $student_record = StudentRecord::findOrfail($student_record_id);
        $estudiant = Estudiant::findOrfail($student_record->estudiant_id); //dd($estudiant);

        $enrollment = Enrollment::where('ci_estudiant',$estudiant->ci_estudiant)->first();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority(2); //dd($autoridad1);
        $autoridad2 = Autoridad::getTipoAuthority(1);

        $compact = ['enrollment','student_record','estudiant','institucion','autoridad1','autoridad2'];
        $view =  View::make('bienestars.student_records.pdf.ficha',compact($compact))->render();

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72; //legal, lettet
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'ficha_estudiante';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

    }

    public function fichaEstudiant($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $student_record = StudentRecord::where('estudiant_id',$estudiant_id)->first();

        if (empty($student_record)) {
            $student_record = New StudentRecord;
            $student_record->estudiant_id = $estudiant_id; //dd($student_record);
            $student_record->save();
        }

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority(2); //dd($autoridad1);
        $autoridad2 = Autoridad::getTipoAuthority(1);

        $compact = ['student_record','estudiant','institucion','autoridad1','autoridad2'];
        $view =  View::make('bienestars.student_records.pdf.ficha',compact($compact))->render();
        // /home/nuser/code/s2223/resources/views/administracion/bienestars/pdf/ficha.blade.php

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72; //legal, lettet
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'ficha_estudiante';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

    }

    public function batch(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id:null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id:null;
        $status_active     = (!empty($request->status_active)) ? $request->status_active: null;
        $formally = (!empty($request->formally)) ? $request->formally:null;

        // dd($grado_id,$seccion_id,$planpago_id,$status_active,$formally);

        $estudiants = collect(New Estudiant);

        if (count($request->all())>0) {
            $estudiants =
                Estudiant::select('estudiants.*')
                    ->join('student_records', 'estudiants.id', '=', 'student_records.estudiant_id')
                    ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->whereNull('inscripcions.deleted_at')
                    ->whereNull('seccions.deleted_at')
                    ->whereNull('grados.deleted_at')
                    ->groupby('estudiants.id');

            $estudiants = (isset($grado_id)) ? $estudiants->where('grados.id',$grado_id) : $estudiants;
            $estudiants = (isset($seccion_id) && isset($seccion_id)) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants;
            $estudiants = (isset($planpago_id) && isset($planpago_id)) ? $estudiants->where('administrativas.planpago_id',$planpago_id) : $estudiants;
            $estudiants = ($status_active) ? $estudiants->where('estudiants.status_active',$status_active) : $estudiants ;
            $estudiants = ($formally=='SI') ? $estudiants->whereNotNull('administrativas.id')->whereNotNull('inscripcions.id') : $estudiants ;
            $estudiants = ($formally=='NO') ? $estudiants->where( function($query) { $query->where('inscripcions.id', null)->orWhere('administrativas.id',null);}) : $estudiants ;
            $estudiants = $estudiants->get();
        }

        //dd($grado_id,$seccion_id,$planpago_id,$status_active,$formally,$estudiants);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority(2);
        $autoridad2 = Autoridad::getTipoAuthority(1);

        $compact = ['estudiants','institucion','autoridad1','autoridad2'];
        $view =  View::make('bienestars.student_records.pdf.batch',compact($compact))->render();

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72; //legal, lettet
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'fichas_estudiantiles';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }
}
