<?php

namespace App\Http\Controllers\Representant\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Payment;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Pescolar\Seccion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistroPagoController extends Controller
{
    protected $representant,$estudiants,$list_comment,$expire_bill_pendientes;
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
            $this->estudiants = ($this->representant) ? $this->representant->estudiants_formaly : null;
            $this->expire_bill_pendientes = ($this->representant) ? $this->representant->exchange_expire_bill_pendientes : null;
            return $next($request);
        }]);
    }
    public function crud(Request $request)
    {
        $representant = $this->representant;
        $estudiants = $this->estudiants; //dd($estudiants);
        $expire_bill_pendientes = $this->expire_bill_pendientes;
        $registropagos = RegistroPago::select('registro_pagos.*','cuentaxpagars.name as cuentaxpagar_name')
        ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
        ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_pagos_ammount')
        ->selectRaw('sum(credito_a_favors.credito_ammount) as total_credito_ammount')
        ->Join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
        ->Join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
        ->leftJoin('credito_a_favors', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
        ->where('registro_pagos.representant_id',$representant->id)
        ->groupBy('registro_pagos.cuentaxpagar_id')
        ->orderBy('cuentaxpagars.date_expiration','desc')
        ->get();

        // $payments = $representant->payments; dd($payments);
        $payments = Payment::where('ci_representant',$representant->ci_representant)->get(); //dd($payments);

        // dd($registropagos);

        /*******************list****************************/
        $list_comment = $this->list_comment; //dd($list_comment);

        return view('representants.registropagos.crud', compact('representant','estudiants','registropagos','payments','list_comment','expire_bill_pendientes'));
    }

    public function payments()
    {
        $representant = $this->representant;
        $estudiants = $this->estudiants; //dd($estudiants);
        $expire_bill_pendientes = $this->expire_bill_pendientes;

        $payments = Payment::where('ci_representant',$representant->ci_representant)->get();; //dd($payments);

        /*******************list****************************/
        $list_comment_form = Payment::COLUMN_COMMENTS;
        $method_pay_list = MetodoPago::public()->pluck('name','id')->toArray();
        $banco_list         = Banco::banco_list();
        $list_grado_seccion = Seccion::list_grado_seccion();

        $list_comment = $this->list_comment;
        return view('representants.registropagos.payments', compact('representant','list_comment','list_comment_form','payments','banco_list','method_pay_list','expire_bill_pendientes','list_grado_seccion'));
    }
}
