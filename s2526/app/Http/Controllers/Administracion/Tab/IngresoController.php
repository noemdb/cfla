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
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\Currency;
use App\Models\app\Planpago\ExchangeRate;

use App\Models\sys\Logdb;


class IngresoController extends Controller
{
    protected $currency_primary,$currency_secondary;

    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
        //$this->middleware('is_admin')->only(['edit','destroy']);
        $this->currency_primary = Currency::where('status_primary',true)->orderBy('created_at','asc')->first();
        $this->currency_secondary = Currency::where('status_secondary',true)->orderBy('created_at','asc')->first();
    }

    public function crud(Request $request)
    {
        $currency_primary = $this->currency_primary;
        $currency_secondary = $this->currency_secondary;
        $ingresos = collect();
        $ingreso_aplicados = collect();
        $total_ingreso = null;

        $finicial             = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal               = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $banco_id             = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $ci                   = (!empty($request->ci)) ? $request->ci : null  ;
        $number_i_pay         = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;
        $is_public            = (!empty($request->is_public)) ? $request->is_public : null  ; //dd($is_public);
        $status_late_payment  = (!empty($request->status_late_payment)) ? $request->status_late_payment : null  ; //dd($status_late_payment);

        if (count($request->all())>0) {

            $ingresos = Ingreso::select('ingresos.*')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->where('ingresos.created_at','>=','2024-07-15')
            ->OrderBy('created_at','desc')
            ;

            $ingresos = (isset($finicial)) ? $ingresos->wheredate('ingresos.date_payment','>=',$finicial) : $ingresos;
            $ingresos = (isset($ffinal)) ? $ingresos->wheredate('ingresos.date_payment','<=',$ffinal) : $ingresos;
            $ingresos = ($is_public=='on') ? $ingresos->where('bancos.is_public','true') : $ingresos;
            $ingresos = ($status_late_payment=='on') ? $ingresos->where('ingresos.status_late_payment','true') : $ingresos;

            $ingresos = (isset($banco_id)) ? $ingresos->where('ingresos.banco_id',$banco_id) : $ingresos;

            if ($ci) {
                $ingresos = $ingresos->join('estudiants', 'estudiants.id', '=', 'ingresos.estudiant_id')->join('representants', 'representants.id', '=', 'ingresos.representant_id');
                $ingresos = $ingresos->where( function($query) use ($ci) {
                    $query->where('estudiants.ci_estudiant', 'like', "%".$ci."%")
                        ->orWhere('representants.ci_representant', 'like', "%".$ci."%");
                });
            }

            $ingresos = (isset($number_i_pay)) ? $ingresos->where('ingresos.number_i_pay', 'like', "%".$number_i_pay."%") : $ingresos;

            $ingresos = $ingresos->get();
        }

        $list_banco = Banco::banco_list();

        $compact = [
            'status_late_payment',
            'ingresos',
            'total_ingreso',
            'currency_primary'
            ,'currency_secondary',
            'is_public',
            'finicial',
            'ffinal',
            'banco_id',
            'ci',
            'number_i_pay',
            'list_banco'
        ];

        return view('administracion.ingresos.crud',compact($compact));
    }

    public function edit($id)
    {
        $ingreso = Ingreso::findOrFail($id);
        $method_pay_list    = MetodoPago::select('name','id')->orderby('name','asc')->pluck('name', 'id');
        $banco_list         = Banco::banco_list();
        return view('administracion.ingresos.edit',compact('ingreso','method_pay_list','banco_list'));
    }

    public function update(Request $request, $id)
    {
        // $estudiant_id = $request->estudiant_id;
        // $estudiant = Estudiant::findOrFail($estudiant_id);
        // $representant = $estudiant->representant;

        // dd($request->all());

        $date_payment = $request->date_payment;

        $exchange_rate_current = ExchangeRate::whereDate('date',$date_payment)->first();
        $exchange_id = ($exchange_rate_current) ? $exchange_rate_current->id : null;
        $exchange_ammount = ($exchange_rate_current) ? $request->ingreso_ammount / $exchange_rate_current->ammount : null;

        $arr = [
            // 'estudiant_id' => $estudiant->id,
            // 'representant_id' => $representant->id,
            'method_pay_id' => $request->method_pay_id,
            'banco_id' => $request->banco_id,
            'number_i_pay' =>$request->number_i_pay,
            'date_transaction' =>$request->date_transaction,
            'date_payment' =>$request->date_payment,
            'ingreso_ammount' =>$request->ingreso_ammount,
            'exchange_rate_id' =>$exchange_rate_current->id,
            'exchange_ammount' =>$exchange_ammount,
            'exchange_ammount_late_payment' =>$request->exchange_ammount_late_payment,
            'ingreso_observations' => $request->ingreso_observations,
            // 'person_bill_ci' => $representant->ci_representant,
            // 'person_bill_name' =>$representant->name,
        ];
        $ingreso = Ingreso::findOrFail($id);
        $ingreso->fill($arr);
        $ingreso->save();
        $messenge = trans('db_oper_result.user_update_ok');
        $messenge = 'OK';
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        return redirect()->route('administracion.ingresos.edit',$id);
    }
    public function destroy($id, Request $request)
    {
        $ingreso = Ingreso::findOrFail($id);

        $number_i_pay = $ingreso->number_i_pay.'[BORRADO]'.$ingreso->id;
        $update = Ingreso::where('id',$ingreso->id)->withTrashed();
        $update->update(['number_i_pay'=>$number_i_pay]);

        $ingreso->delete();
        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }
        Session::flash('operp_ok',$messenge);
        return view('administracion.ingresos.crud');
    }
}
