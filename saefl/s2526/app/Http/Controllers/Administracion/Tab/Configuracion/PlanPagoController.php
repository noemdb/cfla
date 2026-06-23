<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Planpago\CreatePlanpagoRequest;
use App\Http\Requests\Administracion\Planpago\UpdatePlanpagoRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Estudiante\Descuento;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\NomConceptoPago;
use App\Models\app\Planpago\Currency;
use App\Models\app\Planpago\ReferentialCurrency;

use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;

class PlanPagoController extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $planpagos = Planpago::where('status_active','true')->get();
        $planpagos = Planpago::all();
        $list_comment = Planpago::COLUMN_COMMENTS;

        return view('administracion.configuraciones.planpagos.index',compact('planpagos','list_comment'));
    }
    public function asignar($id)
    {
        $estudiants = Estudiant::where('status_active','true')->get();
        $planpago = Planpago::findOrFail($id);
        $planpago_list = Planpago::select('name', 'id')->where('status_active','true')->orderby('id','asc')->pluck('name', 'id');

        return view('administracion.configuraciones.planpagos.asignar',compact('id','estudiants','planpago_list','planpago'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $list_comment = Planpago::COLUMN_COMMENTS;
        $planpagos = Planpago::all();
        $autoridad4 = Autoridad::getTipoAuthority('4');//DIRECTO ADMINISTRACIÓN
        $list_currency = Currency::all()->pluck('name','id');
        $list_referential_currency = ReferentialCurrency::all()->pluck('name','id');
        return view('administracion.configuraciones.planpagos.create',compact('list_comment','list_currency','list_referential_currency','autoridad4','planpagos'));
    }

    public function store(CreatePlanpagoRequest $request)
    {
        $planpago = Planpago::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.planpagos.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function set_plan(Request $request)
    {
        $planpago_id = $request->all()['planpago_id'];
        $id = $planpago_id;

        $arr = $request->all()['arr_planpago'];
        foreach ($arr as $k => $v) {
            $estudiant = Estudiant::where('id',$k);
            if ($v=="true") {
                $estudiant->update(['planpago_id'=>$planpago_id]);
                $administrativa = Administrativa::where('estudiant_id',$k)->first();
                if (!$administrativa) {
                    $create = Administrativa::create([
                        'estudiant_id' => $k,
                        'user_id' => Auth::user()->id
                    ]);
                }
            }
            else{
                $estudiant->update(['planpago_id'=>'1']);
            }
            unset($estudiant);
        }

        Session::flash('operp_ok','Registro guardado exitosamente, Plan de Pago asignado correctamente');
        $estudiants = Estudiant::where('status_active','true')->get();
        $planpago = Planpago::findOrFail($planpago_id);
        $planpago_list = Planpago::select('name', 'id')->where('status_active','true')->orderby('id','asc')->pluck('name', 'id');
        return redirect()->route('administracion.configuraciones.planpagos.asignar',compact('id','estudiants','planpago_list','planpago'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\app\Planpago\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function show(Pago $pago)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\app\Planpago\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $planpago = Planpago::findOrFail($id);
        $list_comment = Planpago::COLUMN_COMMENTS;
        $institucion_list = Institucion::all()->pluck('name', 'id');
        $list_currency = Currency::pluck('name','id');
        $list_referential_currency = ReferentialCurrency::all()->pluck('name','id');
        return view('administracion.configuraciones.planpagos.edit',compact('planpago','list_currency','institucion_list','list_comment','list_referential_currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\app\Planpago\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function Update(UpdatePlanpagoRequest $request, $id)
    {
        $planpago = Planpago::findOrFail($id);
        // dd($request->all());
        $planpago->fill($request->all());
        $planpago->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.planpagos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\app\Planpago\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $planpago = Planpago::findOrFail($id);
        $planpago->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.planpagos.index');
    }

}

