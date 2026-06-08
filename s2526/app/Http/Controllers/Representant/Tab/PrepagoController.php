<?php

namespace App\Http\Controllers\Representant\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\Prepago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PrepagoController extends Controller
{
    protected $representant,$estudiants,$list_comment;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','is_representant', function ($request, $next) {
            $this->representant = Representant::where('user_id',Auth::user()->id)->first();
            $this->list_comment = Representant::COLUMN_COMMENTS;
            $this->estudiants = ($this->representant) ? $this->representant->estudiants : null;
            return $next($request);
        }]);
    }
    public function crud(Request $request)
    {
        $representant = $this->representant;
        $prepagos = Prepago::select('prepagos.*')
        ->where('prepagos.representant_id',$representant->id)
        ->get();

        /*******************list****************************/
        $list_comment = $this->list_comment; //dd($list_comment);

        return view('representants.prepagos.crud', compact('prepagos','list_comment'));
    }

    public function create()
    {
        $representant = $this->representant;
        $prepagos = $representant->prepagos->sortByDesc('created_at');

        /*******************list****************************/
        $banco_list         = Banco::banco_list();
        $method_pay_list = MetodoPago::select('name','id')->where('is_public','true')->orderby('name','asc')->pluck('name', 'id');
        $list_comment = $this->list_comment;
        return view('representants.prepagos.create', compact('representant','list_comment','prepagos','banco_list','method_pay_list'));
    }

    public function store(Request $request)
    {
        $prepago = Prepago::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('representants.prepagos.create');
    }

    public function edit($id)
    {
        $seccion = Prepago::findOrFail($id);
        $list_comment = Prepago::COLUMN_COMMENTS;
        // $pestudios = Pestudio::active('true')->with('grados')->orderBy('id', 'asc')->get();
        // foreach ($pestudios as $pestudio) {
        //     $list_grado[$pestudio->name] = $pestudio->grados->pluck('name', 'id');
        // }
        $list_grado = Grado::list_pestudio_grado();
        return view('administracion.configuraciones.seccions.edit',compact('seccion','list_comment','list_grado'));
    }

    public function update(Request $request, $id)
    {
        $seccion = Prepago::findOrFail($id);
        $seccion->fill($request->all());
        $seccion->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.seccions.index',$id);
    }

    public function destroy($id, Request $request)
    {
        $prepago = Prepago::findOrFail($id);

        if ($prepago->status_approved != 'true' && $prepago->status_apply != 'true') {
            $prepago->delete();
            $messenge = trans('db_oper_result.delete_ok');
            $operation= 'delete';
            if($request->ajax()){
                return response()->json([
                    "messenge"=>$messenge,
                    "operation"=>$operation,
                ]);
            }

            Session::flash('operp_ok',$messenge);
            return redirect()->route('administracion.prepagos.crud');

        }

    }
}
