<?php

namespace App\Http\Controllers\Administracion\Tab\Collection;

use App\Http\Controllers\Controller;
use App\Models\app\Cobranzas\CollActivity;
use App\Models\app\Cobranzas\CollNivel;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Common\Status;
use App\Models\app\Estudiante\Representant;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CollActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function index()
    {
        return view('administracion.collections.coll_activities.index');
    }
    public function generate()
    {
        // $allRepresentants = Representant::inRandomOrder()->get(); //dd($representants);
        $allRepresentants = Representant::representantFormaly()->take(50); //dd($representants);

        $representants = collect();
        foreach ($allRepresentants as $representant) {
            $exchange_ammount = $representant->exchange_ammount_expire_bill;
            if ($exchange_ammount > 0.009) {
                $data = collect();
                $data->put('representant',$representant);
                $data->put('exchange_ammount',$exchange_ammount);
                $representants->push($data);
            }
        }
        return view('administracion.collections.coll_activities.generate',compact('representants'));
    }

    public function crud()
    {
        $coll_activities = CollActivity::all();
        $list_comment = CollActivity::COLUMN_COMMENTS;
        return view('administracion.collections.coll_activities.crud',compact('coll_activities','list_comment'));
    }

    public function create()
    {
        $coll_activities = CollActivity::all()->sortByDesc('created_at')->take(10);
        $list_political_nivels = CollNivel::list_political_nivels();
        // $list_users = User::list_users();
        $list_users = User::list_users_employ();
        $list_representant = Representant::list_representant();
        $list_status = Status::list_status();
        $list_comment = CollActivity::COLUMN_COMMENTS;
        $compact = ['coll_activities','list_political_nivels','list_users','list_representant','list_status','list_comment'];
        return view('administracion.collections.coll_activities.create',compact($compact));
    }

    public function store(Request $request)
    {
        $coll_activity = CollActivity::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        return redirect()->route('administracion.collections.coll_activities.create');
    }

    public function edit($id)
    {
        $coll_activity = CollActivity::findOrFail($id);
        $list_political_nivels = CollNivel::list_political_nivels();
        // $list_users = User::list_users_employ();
        $list_users = User::list_users_employ();
        $list_representant = Representant::list_representant();
        $list_status = Status::list_status();
        $list_comment = CollActivity::COLUMN_COMMENTS;
        $compact = ['coll_activity','list_political_nivels','list_users','list_representant','list_status','list_comment'];
        return view('administracion.collections.coll_activities.edit',compact($compact));
    }
    public function update(Request $request, $id)
    {
        $coll_activity = CollActivity::findOrFail($id);
        $coll_activity->fill($request->all());
        $coll_activity->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_activities.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $coll_activity = CollActivity::findOrFail($id);
        $coll_activity->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_activities.crud');
    }
}
