<?php

namespace App\Http\Controllers\Administracion\Tab\Collection;

use App\Http\Controllers\Controller;
use App\Models\app\Cobranzas\CollMessege;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Cobranzas\CollNivel;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class CollNivelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function index()
    {
        return view('administracion.collections.coll_nivels.index');
    }

    public function crud()
    {
        $coll_nivels = CollNivel::all();
        $list_comment = CollNivel::COLUMN_COMMENTS;
        return view('administracion.collections.coll_nivels.crud',compact('coll_nivels','list_comment'));
    }

    public function create()
    {
        $coll_nivels = CollNivel::all()->sortByDesc('created_at');
        $list_coll_politicals = CollPolitical::list_coll_politicals();
        $list_comment = CollNivel::COLUMN_COMMENTS;
        return view('administracion.collections.coll_nivels.create',compact('coll_nivels','list_coll_politicals','list_comment'));
    }

    public function store(Request $request)
    {
        $coll_nivel = CollNivel::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        return redirect()->route('administracion.collections.coll_nivels.create');
    }

    public function edit($id)
    {
        $coll_nivel = CollNivel::findOrFail($id);
        $list_comment = CollNivel::COLUMN_COMMENTS;
        $list_coll_politicals = CollPolitical::list_coll_politicals();
        return view('administracion.collections.coll_nivels.edit',compact('coll_nivel','list_coll_politicals','list_comment'));
    }

    public function update(Request $request, $id)
    {
        $coll_nivel = CollNivel::findOrFail($id);
        $coll_nivel->fill($request->all());
        $coll_nivel->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_nivels.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $coll_nivel = CollNivel::findOrFail($id);
        $coll_nivel->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_nivels.crud');
    }

    public function previewMsnId($id)
    {
        $coll_message = CollMessege::findOrFail($id);
        // $coll_nivel = $coll_message->coll_nivel;
        // $coll_political = $coll_nivel->coll_political;
        // $representant = $coll_political->representants->shuffle()->first();
        // $representant = Representant::inRandomOrder()->first(); //dd($representants);
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
