<?php

namespace App\Http\Controllers\Administracion\Tab\Collection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administracion\Collection\CreateCollMessegeRequest;
use App\Models\app\Cobranzas\CollMessege;
use App\Models\app\Cobranzas\CollNivel;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Common\Status;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class CollMessegeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function index()
    {
        return view('administracion.collections.coll_messeges.index');
    }

    public function sendIndividual(Request $request)
    {
        $coll_political_id = (!empty($request->coll_political_id)) ? $request->coll_political_id : null ;
        $allRepresentants = collect();
        $representants = collect();
        $coll_political = null;
        $coll_messeges = collect();

        if ($coll_political_id) {
            $coll_political = CollPolitical::findOrFail($coll_political_id); //dd($coll_political);
            $representants = $coll_political->representants; //dd($coll_political,$representants);
            $coll_nivels = $coll_political->coll_nivels; //dd($coll_nivels);
            $coll_messeges = $coll_political->coll_messeges; //dd($coll_messeges);
        }

        $list_coll_politicals = CollPolitical::list_coll_politicals();
        return view('administracion.collections.coll_messeges.sendIndividual',compact('coll_political','coll_messeges','representants','coll_political_id','list_coll_politicals'));
    }

    public function crud()
    {
        $coll_messeges = CollMessege::all();
        $list_comment = CollMessege::COLUMN_COMMENTS;
        return view('administracion.collections.coll_messeges.crud',compact('coll_messeges','list_comment'));
    }

    public function create()
    {
        $coll_messeges = CollMessege::all()->sortByDesc('created_at')->take(4);
        $list_political_nivels = CollNivel::list_political_nivels();
        $list_users = User::list_users();
        $list_comment = CollMessege::COLUMN_COMMENTS;
        $compact = ['coll_messeges','list_political_nivels','list_users','list_comment'];
        return view('administracion.collections.coll_messeges.create',compact($compact));
    }

    public function store(CreateCollMessegeRequest $request)
    {
        $coll_messege = CollMessege::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        return redirect()->route('administracion.collections.coll_messeges.create');
    }

    public function edit($id)
    {
        $coll_messege = CollMessege::findOrFail($id);
        $coll_messeges = CollMessege::all()->sortByDesc('created_at')->take(4);
        $list_political_nivels = CollNivel::list_political_nivels();
        $list_users = User::list_users();
        $list_comment = CollMessege::COLUMN_COMMENTS;
        $compact = ['coll_messege','coll_messeges','list_political_nivels','list_users','list_comment'];
        return view('administracion.collections.coll_messeges.edit',compact($compact));
    }
    public function update(Request $request, $id)
    {
        $coll_messege = CollMessege::findOrFail($id);
        $coll_messege->fill($request->all());
        $coll_messege->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_messeges.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $coll_messege = CollMessege::findOrFail($id);
        $coll_messege->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_messeges.crud');
    }

    public function previewMsnId($id)
    {
        $coll_message = CollMessege::findOrFail($id);
        $representant = Representant::representantDebRandom(false); //dd($representant);
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        $compact = ['coll_message','representant','institucion','autoridad1','autoridad2','toDate','lastDate'];
        return view('email.collections.messege',compact($compact));

    }
}
