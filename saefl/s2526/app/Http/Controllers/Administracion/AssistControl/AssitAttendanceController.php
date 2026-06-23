<?php

namespace App\Http\Controllers\Administracion\AssistControl;

use App\Http\Controllers\Controller;
use App\Imports\AssitAttendanceImport;
use App\Models\app\Assistcontrol\AssitAttendance;
use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\sys\Cargo;
use App\Models\sys\Rol;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssitAttendanceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function setOrderWorker(Request $request)
    {
        return view('administracion.asisst_controls.assit_attendances.setOrderWorker');
    }

    public function helpCollectCSV(Request $request)
    {
        return view('administracion.asisst_controls.assit_attendances.helpCollectCSV');
    }

    public function helpLoadCSV(Request $request)
    {
        return view('administracion.asisst_controls.assit_attendances.helpLoadCSV');
    }
    public function helpGeneratePDF(Request $request)
    {
        return view('administracion.asisst_controls.assit_attendances.helpGeneratePDF');
    }

    public function personal(Request $request)
    {
        $finicial = (!empty($request->finicial)) ? $request->finicial:null;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal:null;
        $user_id = (!empty($request->user_id)) ? $request->user_id:null;

        $dates = ($finicial && $ffinal) ? date_range($finicial,$ffinal) : collect(); //dd($dates);
        $user = User::find($user_id);

        $list_users_worker = User::list_users_worker(); //dd($list_users_worker);

        $list_comment = AssitAttendance::COLUMN_COMMENTS;

        $compact = ['user','dates','finicial','ffinal','user_id','list_comment','list_users_worker'];

        return view('administracion.asisst_controls.assit_attendances.personal',compact($compact));
    }

    public function format(Request $request)
    {
        $finicial = (!empty($request->finicial)) ? $request->finicial:null;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal:null;
        $area = (!empty($request->area)) ? $request->area:null;
        $assit_schedule_id = (!empty($request->assit_schedule_id)) ? $request->assit_schedule_id:null;
        $cargo_id = (!empty($request->cargo_id)) ? $request->cargo_id:null;

        $dates = ($finicial && $ffinal) ? date_range($finicial,$ffinal) : collect(); //dd($dates);
        $user = new User;

        $list_comment = AssitAttendance::COLUMN_COMMENTS;
        $list_area = Rol::list_area();
        $list_cargos = Cargo::list_cargos();
        $list_assit_schedule = AssitSchedule::list_assit_schedule();

        $compact = ['user','dates','finicial','ffinal','cargo_id','assit_schedule_id','list_assit_schedule','area','list_area','list_cargos','list_comment'];

        return view('administracion.asisst_controls.assit_attendances.format',compact($compact));
    }

    public function index(Request $request)
    {
        $file_name = (!empty($request->file_name)) ? $request->file_name:null;
        $file_path = (!empty($request->file_path)) ? $request->file_path:null;
        $file_time = ($file_path) ? Storage::lastModified($file_path) : null; //dd($file_time);
        $file_date = Carbon::createFromTimestamp($file_time);//->format('Y-m-d');

        $assitAttendanceImport = new AssitAttendanceImport;

        $assitAttendanceCSV = ($file_path) ? $assitAttendanceImport->toCollectionGet($file_path) : collect() ;

        $list_comment = AssitAttendance::COLUMN_COMMENTS;

        $compact = ['assitAttendanceCSV','file_path','file_name','file_date','list_comment'];

        return view('administracion.asisst_controls.assit_attendances.index',compact($compact));
    }

    public function csvPost(Request $request)
    {
        $file = $request->file('file_csv');
        $file_name = $file->getClientOriginalName();
        $file_path = $file->storeAs('tmp', Str::random(40) . '.' . $file->getClientOriginalExtension());

        $delimiter = (!empty($request->delimiter)) ? $request->delimiter:";";
        $input_encoding = (!empty($request->input_encoding)) ? $request->input_encoding:"UTF-8";

        $inputs = [
            'file_name'=>$file_name,
            'file_path'=>$file_path,
            'delimiter'=>$delimiter,
            'input_encoding'=>$input_encoding
        ];

        return redirect()->route('administracion.asisst_controls.assit_attendances.index',$inputs);

    }


    public function storeCSV(Request $request)
    {
        $file_name = (!empty($request->file_name)) ? $request->file_name:null;
        $file_path = (!empty($request->file_path)) ? $request->file_path:null;

		$assitAttendanceImport = new AssitAttendanceImport;

		$assitAttendanceCSV = ($file_path) ? $assitAttendanceImport->toCollectionStore($file_path) : collect() ;

        $messenge = (count($assitAttendanceCSV) > 0 ) ? 'Buen trabajo! La carga Fue realizada éxitosamente: Nuevos registros: '.count($assitAttendanceCSV) : 'Sin datos registrados';
        Session::flash('operp_ok',$messenge);

        $inputs = [ 'file_name'=>$file_name,'file_path'=>$file_path,'assitAttendanceCSV'=>$assitAttendanceCSV ];

        return redirect()->route('administracion.asisst_controls.assit_attendances.index',$inputs);
    }
}
