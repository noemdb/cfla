<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Census;
use App\Models\app\HistoricoNota\Oinstitucion;
//Modelos
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class LapsoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_control']);
    }

    public function censusUpdate(Request $request, $id)
    {
        $census = Census::findOrFail($id);
        $validated = $request->validate([
            // 'ci_estudiant'=>'required|integer|unique:censuses,ci_estudiant,'.$id,
            'ci_estudiant' => ['required', Rule::unique('censuses', 'ci_estudiant')->ignore($census->id),],
            'lastname'=>'required|string',
            'name'=>'required|string',
            'gender'=>'required|string',
            'date_birth'=>'required|string',
            'town_hall_birth'=>'nullable|string',
            'state_birth'=>'nullable|string',
            'country_birth'=>'nullable|string',
            'dir_address'=>'nullable:string',
            'grado_id'=>'nullable|integer',
            'institution'=>'nullable|string',
            'ci_representant'=>'required|string',
            'name_representant'=>'required|string',
            'relationship'=>'required|string',
            'phone_representant'=>'required|string',
            'email_representant'=>'required|email',
        ]);

         //dd($lapso);
        $census->fill($request->all()); //dd($Lapso);
        $census->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.lapsos.census.edit',$id);
    }

    public function censusEdit(Request $request, $id)
    {
        $census = Census::findOrFail($id); //dd($lapso);
        $list_comment = Census::COLUMN_COMMENTS;
        $list_grado = Grado::list_pestudio_grado();
        $list_country_birth = Estudiant::list_country_birth();
        $list_relationship = ['Madre'=>'Madre','Padre'=>'Padre','Hermano(a)'=>'Hermano(a)','Abuelo(a)'=>'Abuelo(a)','Otro'=>'Otro'];
        $list_oinstitucions = Oinstitucion::list_oinstitucions();
        return view('administracion.configuraciones.lapsos.edit_census',compact('census','list_comment','list_grado','list_country_birth','list_relationship','list_oinstitucions'));
    }

    public function censusIndicators($id)
    {
        $lapso = Census::findOrFail($id);

        return view('administracion.configuraciones.lapsos.indicator_census',compact('id','lapso'));
    }

    public function census($id)
    {
        $lapso = Lapso::findOrFail($id); //dd($lapso);
        $censuses = collect();

        if ($lapso->date_start_census && $lapso->date_end_census) {
            $censuses = Census::select('censuses.*')
                ->whereDate('censuses.created_at','<=',$lapso->date_end_census)
                ->whereDate('censuses.created_at','>=',$lapso->date_start_census)
                ->get()
            ;
        }

        $list_comment = Census::COLUMN_COMMENTS; //dd($list_comment);

        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        $now = Carbon::now()->format('Y-m-d');

        return view('administracion.configuraciones.lapsos.census',compact('id','lapso','list_lapso','censuses','list_comment','now'));
    }

    public function censusDestroy($id, Request $request)
    {
        $census = Census::findOrFail($id);
        $messenge = trans('db_oper_result.destroy_not_ok');
        if ($census->status_delete) {
            $census->delete();
            $messenge = trans('db_oper_result.delete_ok');
            $operation= 'delete';
            if($request->ajax()){
                return response()->json([
                    "messenge"=>$messenge,
                    "operation"=>$operation,
                ]);
            }
        }
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.lapsos.census.index',$id);
    }

    public function index()
    {
        $lapsos = Lapso::all();
        $list_comment = Lapso::COLUMN_COMMENTS;
        return view('administracion.configuraciones.lapsos.index',compact('lapsos','list_comment'));
    }

    public function create()
    {
        $list_comment = Lapso::COLUMN_COMMENTS;
        return view('administracion.configuraciones.lapsos.create',compact('list_grado','list_comment'));
    }

    public function store(Request $request)
    {
        $Lapso = Lapso::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.lapsos.index');
    }

    public function edit($id)
    {
        $lapso = Lapso::findOrFail($id); //dd($lapso);
        $list_comment = Lapso::COLUMN_COMMENTS;
        $list_grado = Grado::list_pestudio_grado();
        return view('administracion.configuraciones.lapsos.edit',compact('lapso','list_comment','list_grado'));
    }

    public function update(Request $request, $id)
    {
        $Lapso = Lapso::findOrFail($id);
        $Lapso->fill($request->all()); //dd($Lapso);
        $Lapso->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.lapsos.index');
    }

    public function destroy($id, Request $request)
    {
        $grado = Lapso::findOrFail($id);
        $messenge = trans('db_oper_result.destroy_not_ok');
        if ($grado->status_delete) {
            $grado->delete();
            $messenge = trans('db_oper_result.delete_ok');
            $operation= 'delete';
            if($request->ajax()){
                return response()->json([
                    "messenge"=>$messenge,
                    "operation"=>$operation,
                ]);
            }
        }
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.lapsos.index');
    }


}
