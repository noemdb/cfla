<?php

namespace App\Http\Controllers\Bienestar\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_bienestar']);
    }

    public function index()
    {
        return view('bienestars.student_records.index');
    }

    public function summaries()
    {
        $indicators = StudentRecord::indicators(); //dd($indicators);
        $illness_others = StudentRecord::illness_others(); //dd($illness_others);
        $conditions_others = StudentRecord::conditions_others(); //dd($conditions_others);
        $treated_specialists = StudentRecord::treated_specialists(); //dd($conditions_others);
        return view('bienestars.student_records.summaries',compact('indicators','illness_others','conditions_others','treated_specialists'));
    }

    public function batch(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id:null;

        $estudiants = collect(New Estudiant());

        if (count($request->all())>0) {

            $estudiants =
                Estudiant::select('estudiants.*')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->whereNull('inscripcions.deleted_at')
                    ->whereNull('seccions.deleted_at')
                    ->whereNull('grados.deleted_at')
                    ->groupby('estudiants.id');

            $estudiants = (isset($grado_id)) ? $estudiants->where('grados.id',$grado_id) : $estudiants;
            $estudiants = (isset($seccion_id) && isset($seccion_id)) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants;

            $estudiants = $estudiants->get();

            foreach ($estudiants as $estudiant) {
                $student_record = StudentRecord::where('estudiant_id',$estudiant->id)->first();
                if (empty($student_record)) {
                    $student_record = New StudentRecord;
                    $student_record->estudiant_id = $estudiant->id;
                    $student_record->save();
                }
            }
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();

        $compact = [
            'estudiants',
            'grado_id','seccion_id',
            'list_grado','list_seccion'
        ];

        return view('bienestars.student_records.batch',compact($compact));
    }
}
