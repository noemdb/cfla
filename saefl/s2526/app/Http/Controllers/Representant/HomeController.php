<?php

namespace App\Http\Controllers\Representant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\RegistroPago;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $representant,$estudiants,$list_comment,$expire_bill_pendientes;



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

    public function users()
    {
        $representant = $this->representant; //dd($representant);
        $expire_bill_pendientes = $this->expire_bill_pendientes; //dd($representant);
        $registropagos = RegistroPago::select('registro_pagos.*')
        ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
        ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_ammount')
        ->join('pagos','registro_pagos.id','=','pagos.registro_pago_id')
        ->join('cuentaxpagars', 'cuentaxpagars.id','=','registro_pagos.cuentaxpagar_id')
        ->where('registro_pagos.representant_id',$representant->id)
        ->orderBy('cuentaxpagars.date_expiration','desc')
        ->groupBy('cuentaxpagars.id')
        ->get(); //dd($registropagos);

        $estudiants = $this->estudiants; //dd($estudiants);
        $profesors = $representant->profesors; //dd($profesors);
        $pestudios = $representant->pestudios; //dd($pestudios);
        $plan_beneficos = $representant->plan_beneficos; //dd($plan_beneficos);

        $list_comment = $this->list_comment; //dd($list_comment);

        return view('representants.users.index',compact('representant','estudiants','profesors','pestudios','expire_bill_pendientes','registropagos','plan_beneficos','list_comment'));
    }

    public function home()
    {
        $representant = $this->representant; //dd($representant);
        $expire_bill_pendientes = $this->expire_bill_pendientes; //dd($representant);
        $registropagos = RegistroPago::select('registro_pagos.*')
        ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
        ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_ammount')
        ->join('pagos','registro_pagos.id','=','pagos.registro_pago_id')
        ->join('cuentaxpagars', 'cuentaxpagars.id','=','registro_pagos.cuentaxpagar_id')
        ->where('registro_pagos.representant_id',$representant->id)
        ->orderBy('cuentaxpagars.date_expiration','desc')
        ->groupBy('cuentaxpagars.id')
        ->get(); //dd($registropagos);
        $estudiants = $this->estudiants; //dd($estudiants);
        $profesors = $representant->profesors; //dd($profesors);
        $pestudios = $representant->pestudios; //dd($pestudios);
        $plan_beneficos = $representant->plan_beneficos; //dd($plan_beneficos);
        // $expire_bill_pendientes = $representant->expire_bill_pendientes; //dd($expire_bill_pendientes);
        // $expire_bill_pendientes = $representant->exchange_expire_bill_pendientes; //dd($expire_bill_pendientes);

        $list_comment = $this->list_comment; //dd($list_comment);

        return view('representants.home',compact('representant','estudiants','profesors','pestudios','expire_bill_pendientes','registropagos','plan_beneficos','list_comment'));
    }
}
