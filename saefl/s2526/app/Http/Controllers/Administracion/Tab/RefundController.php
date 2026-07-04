<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Planpago\Refund;
use Illuminate\Http\Request;

use App\Models\app\Institucion\Banco;

class RefundController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function index(Request $request)
    {

        $banco_id                   = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $number_i_pay               = (!empty($request->number_i_pay)) ? $request->number_i_pay : null ;
        $finicial                   = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal                     = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $representant_ci            = (!empty($request->representant_ci)) ? $request->representant_ci : null  ;
        $representant_id            = (!empty($request->representant_id)) ? $request->representant_id : null  ;
        $credito_a_favor_id         = (!empty($request->credito_a_favor_id)) ? $request->credito_a_favor_id : null  ;
        $registro_pago_combinado_id = (!empty($request->registro_pago_combinado_id)) ? $request->registro_pago_combinado_id : null  ;

        $modeIndex  = true  ;
        $modeCreate = false  ;
        $modeShow   = false  ;

        $refunds         = collect();

        if ($request->all()) {
            $refunds = Refund::select('refunds.*')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'refunds.registro_pago_combinado_id')
            ->join('representants', 'representants.id', '=', 'refunds.representant_id');

            $refunds = ($finicial) ? $refunds->where('registro_pago_combinados.created_at','<=',$finicial) : $refunds ;
            $refunds = ($ffinal) ? $refunds->where('registro_pago_combinados.created_at','>=',$ffinal) : $refunds ;
            $refunds = ($representant_ci) ? $refunds->where('representants.ci_representant',$representant_ci) : $refunds ;

            $refunds = $refunds->get();
        }

        $list_banco = Banco::banco_list();
        $list_comment = Refund::COLUMN_COMMENTS;

        $compact = [
            'refunds','finicial','ffinal','representant_ci','representant_id','credito_a_favor_id','registro_pago_combinado_id',
            'banco_id','number_i_pay','list_banco','modeIndex','modeCreate','modeShow',
            'list_comment',
        ];

        return view('administracion.refunds.index',compact($compact));
    }

    public function create(Request $request)
    {
        dd($request->all());
        $registro_pago_combinado_id = (!empty($request->registro_pago_combinado_id)) ? $request->registro_pago_combinado_id : null  ;
        $representant_id = (!empty($request->representant_id)) ? $request->representant_id : null  ;

        $modeIndex       = false  ;
        $modeCreate      = true  ;
        $modeShow        = false  ;

        $list_banco = Banco::banco_list();
        $list_comment = Refund::COLUMN_COMMENTS;
        $refunds = Refund::all();

        $compact = [
            'refunds',
            'representant_id','registro_pago_combinado_id',
            'list_banco','list_comment',
            'modeIndex','modeCreate','modeShow',
        ];
        return view('administracion.refunds.index',compact($compact));
    }
    public function store(Request $request)
    {
        // $seccion = Seccion::create($request->all());
        // $messenge = trans('db_oper_result.create_ok');
        // Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.seccions.index');
    }
}
