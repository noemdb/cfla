<?php

namespace App\Http\Controllers\Administracion\Tab\Collection;

use App\Http\Controllers\Controller;
use App\Models\app\Cobranzas\CollMessege;
use App\Models\app\Cobranzas\CollNivel;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Cobranzas\CollPromise;
use App\Models\app\Common\Status;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class CollPromiseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function asistent(Request $request)
    {
        $coll_political_id = (!empty($request->coll_political_id)) ? $request->coll_political_id : null ;
        $coll_political = null;
        $representant = null;

        if ($coll_political_id) {
            $coll_political = CollPolitical::find($coll_political_id);
            $representant = $coll_political->representants->random();
        }

        // $coll_political = CollPolitical::inRandomOrder()->first();
        $list_coll_politicals = CollPolitical::list_coll_politicals();

        $list_representant = Representant::list_representant();
        $list_comment = CollPromise::COLUMN_COMMENTS;
        $compact = ['coll_political','coll_political_id','representant','list_coll_politicals','list_representant','list_comment'];
        return view('administracion.collections.coll_promises.asistent',compact($compact));
    }

    public function loadRepresentant(Request $request)
    {
        $coll_political = CollPolitical::findOrFail($request->coll_political_id);
        $representant = $coll_political->representants->first(); //dd($representant);
        // $representant = Representant::representantRdDebt();  dd($representant);
        $list_coll_politicals = CollPolitical::list_coll_politicals();
        $list_comment = CollPromise::COLUMN_COMMENTS;
        return view('administracion.collections.coll_promises.form.asistent.steps.representant.main',compact('representant','list_coll_politicals','list_comment'));

    }

    public function preview(Request $request)
    {
        $coll_political_id = (!empty($request->coll_political_id)) ? $request->coll_political_id : null ;
        $representant_id = (!empty($request->representant_id)) ? $request->representant_id : null ;

        $coll_political = CollPolitical::findOrFail($coll_political_id);
        $representant = Representant::findOrFail($representant_id);

        //'coll_political_id','representant_id','date','ammount','exchange_ammount','status','description','observation'
        $arr = [
            'coll_political_id' => $request['coll_political_id'],
            'representant_id' => $request['representant_id'],
            'date' => $request['date'],
            'ammount' => null,
            'exchange_ammount' => $request['exchange_ammount'],
            'status' => 'true',
            'description' => $request['description'],
            'observation' => $request['observation'],
            'created_at' => Carbon::now(),
        ];
        $coll_promise = new CollPromise();
        $coll_promise->fill($arr);
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        $compact = ['coll_political','representant','coll_promise','institucion','autoridad1','autoridad2','toDate','lastDate'];
        return view('administracion.collections.coll_promises.pdf.acta',compact($compact));

    }

    public function index()
    {
        return view('administracion.collections.coll_promises.index');
    }

    public function crud(Request $request)
    {
        $coll_political_id = (!empty($request->coll_political_id)) ? $request->coll_political_id : null ;
        $representant_id = (!empty($request->representant_id)) ? $request->representant_id : null ;
        $help_representante = (!empty($request->help_representante)) ? $request->help_representante : null ;

        $coll_promises = collect();

        if (count($request->all())>0) {
            $coll_promises = CollPromise::select('coll_promises.*');
            $coll_promises = ($coll_political_id) ? $coll_promises->where('coll_political_id',$coll_political_id) : $coll_promises ;
            $coll_promises = ($representant_id) ? $coll_promises->where('representant_id',$representant_id) : $coll_promises ;

            $coll_promises = $coll_promises->get();
        }

        $list_comment = CollPromise::COLUMN_COMMENTS;
        $list_representant = Representant::list_representant();
        $list_coll_politicals = CollPolitical::list_coll_politicals();
        $compact = ['coll_promises','representant_id','coll_political_id','help_representante','list_comment','list_representant','list_coll_politicals'];
        return view('administracion.collections.coll_promises.crud',compact($compact));
    }

    public function create()
    {
        $coll_promises = CollPromise::all()->sortByDesc('created_at')->take(4);
        $list_coll_politicals = CollPolitical::list_coll_politicals();
        $list_representant = Representant::list_representant();
        $list_comment = CollPromise::COLUMN_COMMENTS;
        $compact = ['coll_promises','list_coll_politicals','list_representant','list_comment'];
        return view('administracion.collections.coll_promises.create',compact($compact));
    }

    public function store(Request $request)
    {
        $coll_promise = CollPromise::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        // return redirect()->route('administracion.collections.coll_promises.create');
        return redirect()->back();
    }

    public function asistentStore(Request $request)
    {
        $coll_promise = CollPromise::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        // return redirect()->route('administracion.collections.coll_promises.create');
        return redirect()->back();
    }


    public function edit($id)
    {
        $coll_promise = CollPromise::findOrFail($id);
        $list_coll_politicals = CollPolitical::list_coll_politicals();
        $list_representant = Representant::list_representant();
        $list_comment = CollPromise::COLUMN_COMMENTS;
        $compact = ['coll_promise','list_coll_politicals','list_representant','list_comment'];
        return view('administracion.collections.coll_promises.edit',compact($compact));
    }
    public function update(Request $request, $id)
    {
        $coll_promise = CollPromise::findOrFail($id);
        $coll_promise->fill($request->all());
        $coll_promise->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_promises.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $coll_promise = CollPromise::findOrFail($id);
        $coll_promise->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_promises.crud');
    }

}
