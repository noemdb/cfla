<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\User;
use App\Models\app\Planpago\Payment;
use App\Models\app\Institucion\Banco;
use App\Models\app\Institucion\MetodoPago;

class PaymentController extends Controller
{

    public function inscriptions(Request $request)
    {
        $total_ingreso = null;

        $payments = Payment::where('type_pay','=','Inscripción Período Escolar 2021 - 2022')->orderBy('payments.created_at','asc');

        $finicial           = (!empty($request->finicial)) ? $request->finicial : Carbon::now()->format('Y-m-d') ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null  ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;
        $type_pay           = (!empty($request->type_pay)) ? $request->type_pay : 'Inscripción Período Escolar 2021 - 2022'  ; //dd($type_pay);

        $payments = (isset($finicial)) ? $payments->wheredate('payments.created_at','>=',$finicial) : $payments;
        $payments = (isset($ffinal)) ? $payments->wheredate('payments.created_at','<=',$ffinal) : $payments;
        $payments = (isset($ci_representant)) ? $payments->where('payments.ci_representant',$ci_representant) : $payments;
        //$payments = (isset($type_pay)) ? $payments->where('payments.type_pay','like', "%".$type_pay."%") : $payments;

        if ($banco_id) {
            $payments = $payments->where( function($query) use ($banco_id) {
                $query->where('payments.banco_id_1',$banco_id)
                    ->orWhere('payments.banco_id_2',$banco_id)
                    ->orWhere('payments.banco_id_3',$banco_id)
                    ->orWhere('payments.banco_id_4',$banco_id);
            });
        }
        if ($number_i_pay) {
            $payments = $payments->where( function($query) use ($number_i_pay) {
                $query->where('payments.number_i_pay_1','like', "%".$number_i_pay."%")
                    ->orWhere('payments.number_i_pay_2','like', "%".$number_i_pay."%")
                    ->orWhere('payments.number_i_pay_3','like', "%".$number_i_pay."%")
                    ->orWhere('payments.number_i_pay_4','like', "%".$number_i_pay."%");
            });
        }

        $payments = $payments->get();

        $list_banco = Banco::list_public_bancos();
        $list_comment = Payment::COLUMN_COMMENTS;
        $list_type_pay = Payment::LIST_TYPE_PAY;

        return view('administracion.payments.inscriptions',compact('payments','finicial','ffinal','ci_representant','number_i_pay','banco_id','list_comment','list_banco','type_pay','list_type_pay'));
    }


    public function crud(Request $request)
    {
        $total_ingreso = null;

        $payments = Payment::where('type_pay','<>','Inscripción Período Escolar 2021 - 2022')->orderBy('payments.created_at','asc');

        $finicial           = (!empty($request->finicial)) ? $request->finicial : Carbon::now()->format('Y-m-d') ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null  ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;
        $type_pay           = (!empty($request->type_pay)) ? $request->type_pay : null  ; //dd($type_pay);

        $payments = (isset($finicial)) ? $payments->wheredate('payments.created_at','>=',$finicial) : $payments;
        $payments = (isset($ffinal)) ? $payments->wheredate('payments.created_at','<=',$ffinal) : $payments;
        $payments = (isset($ci_representant)) ? $payments->where('payments.ci_representant',$ci_representant) : $payments;
        $payments = (isset($type_pay)) ? $payments->where('payments.type_pay','like', "%".$type_pay."%") : $payments;

        if ($banco_id) {
            $payments = $payments->where( function($query) use ($banco_id) {
                $query->where('payments.banco_id_1',$banco_id)
                    ->orWhere('payments.banco_id_2',$banco_id)
                    ->orWhere('payments.banco_id_3',$banco_id)
                    ->orWhere('payments.banco_id_4',$banco_id);
            });
        }
        if ($number_i_pay) {
            $payments = $payments->where( function($query) use ($number_i_pay) {
                $query->where('payments.number_i_pay_1','like', "%".$number_i_pay."%")
                    ->orWhere('payments.number_i_pay_2','like', "%".$number_i_pay."%")
                    ->orWhere('payments.number_i_pay_3','like', "%".$number_i_pay."%")
                    ->orWhere('payments.number_i_pay_4','like', "%".$number_i_pay."%");
            });
        }

        $payments = $payments->get();

        $list_banco = Banco::list_public_bancos();
        $list_comment = Payment::COLUMN_COMMENTS;
        $list_type_pay = Payment::LIST_TYPE_PAY;

        return view('administracion.payments.crud',compact('payments','finicial','ffinal','ci_representant','number_i_pay','banco_id','list_comment','list_banco','type_pay','list_type_pay'));
    }

    public function charts(Request $request)
    {
        return view('administracion.payments.charts');
    }
}
