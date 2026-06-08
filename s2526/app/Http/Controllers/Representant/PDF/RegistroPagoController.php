<?php

namespace App\Http\Controllers\Representant\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPago;
use Illuminate\Support\Facades\Auth;

class RegistroPagoController extends Controller
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

    public function recibo_pago_representant($cuentaxpagar_id){

        $representant = $this->representant;
        $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);

        $registropagos = RegistroPago::select('registro_pagos.*','cuentaxpagars.name as cuentaxpagar_name')
        // ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
        // ->selectRaw('sum(credito_a_favors.credito_ammount) as total_credito_ammount')
        ->Join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
        ->Join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
        ->leftJoin('credito_a_favors', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
        ->where('cuentaxpagars.id',$cuentaxpagar->id)
        ->where('registro_pagos.representant_id',$representant->id)
        ->orderBy('cuentaxpagars.date_expiration','desc')
        ->get();

        $paper  = 'lettet';
        $orientacion = 'portrait';
        $view =  \View::make('representants.registropagos.recibos.pagos.representant', compact('registropagos','cuentaxpagar','representant'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        return $pdf->stream('Recibo de Pago Representante');
    }
}
