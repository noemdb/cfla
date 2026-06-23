<?php

namespace App\Http\Controllers\Administracion\Tab\Receibt;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\Recibo;
use App\Models\app\Planpago\ReciboCash;
use App\Models\app\Planpago\ReciboChange;
use App\Models\app\Planpago\ReciboPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReciboController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function index(Request $request)
    {
        $representant_id = (!empty($request->representant_id)) ? $request->representant_id : null;
        $help_representante = (!empty($request->help_representante)) ? $request->help_representante : null;

        $num_caashs = (!empty($request->num_caashs)) ? $request->num_caashs : null;
        $num_changes = (!empty($request->num_changes)) ? $request->num_changes : null;
        $num_pagos = (!empty($request->num_pagos)) ? $request->num_pagos : null;

        $list_comment_arr['representant'] = Representant::COLUMN_COMMENTS;
        $list_comment_arr['recibos'] = Recibo::COLUMN_COMMENTS;
        $list_comment_arr['recibo_cashes'] = ReciboCash::COLUMN_COMMENTS;
        $list_comment_arr['recibo_changes'] = ReciboChange::COLUMN_COMMENTS;
        $list_comment_arr['recibo_pagos'] = ReciboPago::COLUMN_COMMENTS;
        $list_representant = Representant::list_representant();
        $list_quota = ReciboPago::LIST_QUOTA;
        $list_divisas = ['1'=>'1','2'=>'2','5'=>'5','10'=>'10','20'=>'20','50'=>'50','100'=>'100'];

        $compact = ['representant_id','help_representante','list_representant','list_comment_arr','num_caashs','num_changes','num_pagos','list_quota','list_divisas'];
        return view('administracion.receibts.recibos.index',compact($compact));
    }

    public function store(Request $request)
    {
        $arr = [
            'representant_id'=>$request->representant_id,
            'user_id'=>$request->user_id
        ];
        $recibo = Recibo::create($arr);

        for ($i=0; $i < $request->num_caashs ; $i++) {
            $arr = [
                'recibo_id'=>$recibo->id,
                'serial'=>$request->cashs_serial[$i],
                'exchange_ammount'=>$request->cashs_exchange_ammount[$i],
            ];
            $cash = ReciboCash::create($arr);
        }

        for ($i=0; $i < $request->num_changes ; $i++) {
            $arr = [
                'recibo_id'=>$recibo->id,
                'serial'=>$request->changes_serial[$i],
                'exchange_ammount'=>$request->changes_exchange_ammount[$i],
            ];
            $change = ReciboCash::create($arr);
        }

        for ($i=0; $i < $request->num_pagos ; $i++) {
            $arr = [
                'recibo_id'=>$recibo->id,
                'quota'=>$request->quota[$i],
                'exchange_ammount'=>$request->quota_exchange_ammount[$i],
            ];
            $pago = ReciboPago::create($arr);
        }


        // $coll_nivel = CollNivel::create($request->all());
        $btn = ' <a title="Generar PDF" class="btn btn-dark btn-sm"  href="'.route('administracion.receibts.recibo.pdf',$recibo->id).'" role="button" target="_blank"> Generar PDF <i class="fa fa-file-pdf" aria-hidden="true"></i></a>';
        $messenge = trans('db_oper_result.create_ok'). $btn;
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        return redirect()->route('administracion.receibts.recibos.index');
    }

    public function crud()
    {
        $recibos = Recibo::all()->sortByDesc('created_at');
        $compact = ['recibos'];
        return view('administracion.receibts.recibos.crud',compact($compact));
    }

}
