<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Retiro;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;
use App\User;

class RetiroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $retiros = Retiro::all();
        return view('administracion.retiros.index',compact('retiros'));
    }

    public function crud(Request $request)
    {
        $estudiants = Estudiant::active('true');

        $per_page = (!empty($request->per_page)) ? $request->per_page : 10  ;

        $search = (!empty($request->search)) ? $request->search : null  ;
        $arr_get = ['search'=>$search];

        $estudiants = $estudiants->name($arr_get)->paginate($per_page);

        return view('administracion.retiros.crud',compact('estudiants','search','per_page'));
    }

    public function store($id, Request $request)
    {
        $estudiant =  Estudiant::findOrFail($id);
        $user = User::find(Auth::id());
        $tipo = ( $user->isControl() ) ? 'control' : null ;
        $tipo = ( $user->isAdmon() ) ? 'admon' : null ;
        $retiro = Retiro::where('estudiant_id',$estudiant->id)->first();

        if (empty($retiro->id)) {
            $retiro = Retiro::create([
                'estudiant_id' => $id,
                'user_id' => $user->id,
                'tipo' => $tipo
            ]);
        }

        $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill; //dd($exchange_ammount_expire_bill);

        if ( $exchange_ammount_expire_bill > 0  && $tipo=='admon') {
            $expire_bill = Cuentaxpagar::create([
                'planpago_id' => $estudiant->administrativa->planpago_id,
                'name' => 'DEUDA INDIVIDUAL PENDIENTE',
                'type' => 'INDIVIDUAL',
                'estudiant_id' => $estudiant->id,
                'date_expiration' => Carbon::now(),
                'description' => 'DEUDA INDIVIDUAL PENDIENTE GENERADA POR: '.Auth::user()->username.', DEL RETIRO DEL ESTUDIANTE. ',
                'status_active' => 'true',
                'status_inscription' => 'true'
                ]);
            $ammount_expire_bill = $estudiant->ammount_expire_bill;
            $expire_concepto = ConceptoPago::create([
                'cuentaxpagar_id' => $expire_bill->id,
                'nom_concepto_pago_id' => 3,
                'concepto_description' => 'DEUDA INDIVIDUAL PENDIENTE',
                'concepto_ammount' => $ammount_expire_bill,
                'exchange_ammount' => $exchange_ammount_expire_bill
            ]);

        }

        if (!empty($estudiant->inscripcion->id) && $tipo=='control') {
            $inscripcion = Inscripcion::findOrFail($estudiant->inscripcion->id);
            $inscripcion->delete();
        }

        if (!empty($estudiant->administrativa->id) && $tipo=='admon') {
            $administrativa = Administrativa::findOrFail($estudiant->administrativa->id);
            $administrativa->delete();
            $estudiant->update(['status_active' => 'false']);
        }

        DB::commit();

        $title='Retirado el: '.$retiro->created_at;

        if($request->ajax()){
            return response()->json([
                "messenge"=>'Estudiante retirado exitosamente.',
                "operation"=>'operp_ok',
                "div"=>'
                <a title="'.$title.'" class="btn btn-secondary btn-sm disabled" href="#">
                    <i class="fa fa-check" aria-hidden="true"></i>
                </a>',
            ]);
        }
    }
}
