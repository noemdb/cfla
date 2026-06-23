<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\Currency;
use App\Models\app\Estudiante\Representant;

class CreditoAFavorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
        $this->currency_primary = Currency::where('status_primary',true)->orderBy('created_at','asc')->first();
        $this->currency_secondary = Currency::where('status_secondary',true)->orderBy('created_at','asc')->first();
    }

    public function omit(Request $request)
    {
        $creditoafavors = collect();
        $total_credito = null;

        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $ci                 = (!empty($request->ci)) ? $request->ci : null  ;
        $active             = (!empty($request->active)) ? $request->active : null  ;
        $status_omitted_request     = (!empty($request->status_omitted_request)) ? $request->status_omitted_request : null  ;
        $creditoafavor_id = (!empty($request->creditoafavor_id)) ? $request->creditoafavor_id: null;

        /*******************inicializaciones***************************************************/
        $creditoafavors = collect();
        $selected = ($creditoafavor_id) ? CreditoAFavor::find($creditoafavor_id) : null ;
        $modeSetUp = ($selected) ? true : false ;

        if (count($request->all())>0) {

            $creditoafavors = CreditoAFavor::select('credito_a_favors.*')
            ->join('representants', 'representants.id', '=', 'credito_a_favors.representant_id')
            ->leftjoin('estudiants', 'estudiants.id', '=', 'credito_a_favors.estudiant_id')
            ->whereNull('credito_a_favors.deleted_at')
            ->OrderBy('created_at','desc');

            $creditoafavors = (isset($finicial)) ? $creditoafavors->wheredate('credito_a_favors.created_at','>=',$finicial) : $creditoafavors;
            $creditoafavors = (isset($ffinal)) ? $creditoafavors->wheredate('credito_a_favors.created_at','<=',$ffinal) : $creditoafavors;

            $creditoafavors = (isset($ci)) ? $creditoafavors->where('representants.ci_representant', 'like', "%".$ci."%") : $creditoafavors;

            $creditoafavors = (isset($status_omitted_request)) ? $creditoafavors->where('credito_a_favors.status_omitted',$status_omitted_request) : $creditoafavors;

            if ($active=='SI') {
                $creditoafavors = $creditoafavors
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->where('estudiants.status_active','true')
                ->where('representants.status_active','true')
                ->where('credito_a_favors.exchange_ammount','>',0.009);
            }

            $creditoafavors = $creditoafavors->get();

        }

        $currency_primary = $this->currency_primary;
        $currency_secondary = $this->currency_secondary;
        $list_comment = CreditoAFavor::COLUMN_COMMENTS;

        $compact = [
            'creditoafavors','selected','selected','creditoafavor_id','modeSetUp',
            'currency_primary','currency_secondary',
            'request','finicial','ffinal','ci','active','status_omitted_request',
            'list_comment'
        ];

        return view('administracion.creditoafavors.omit',compact($compact));
    }

    public function setOmit(Request $request )
    {
        $status_omitted         = (!empty($request->status_omitted)) ? $request->status_omitted : null ;
        $status_omitted_request = (!empty($request->status_omitted_request)) ? $request->status_omitted_request : null ;
        $observations_user      = (!empty($request->observations_user)) ? $request->observations_user : null ;
        $finicial               = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal                 = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $ci                     = (!empty($request->ci)) ? $request->ci : null  ;
        $active                 = (!empty($request->active)) ? $request->active : null  ;
        $creditoafavor_id       = (!empty($request->creditoafavor_id)) ? $request->creditoafavor_id: null;

        $creditoafavor = CreditoAFavor::findOrFail($creditoafavor_id);
        $creditoafavor->fill(['status_omitted'=>$status_omitted,'observations_user'=>$observations_user]);
        $creditoafavor->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);

        $inputs = [
            'finicial'=>$finicial,
            'ffinal'=>$ffinal,
            'ci'=>$ci,
            'active'=>$active,
            'status_omitted_request'=>$status_omitted_request,
            'creditoafavor_id'=>$creditoafavor->id,
        ];

        return redirect()->route('administracion.creditoafavors.omit',$inputs);
    }

    public function setAjaxOmit($id, Request $request)
    {
        $creditoafavor = CreditoAFavor::findOrFail($id);
        $creditoafavor->fill(['status_omitted'=>'true']);
        $creditoafavor->save();

        $messenge = trans('db_oper_result.update_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }
    }


    public function crud(Request $request)
    {
        $creditoafavors = collect();
        $total_credito = null;

        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $ci                 = (!empty($request->ci)) ? $request->ci : null  ;
        $state              = (!empty($request->state)) ? $request->state : null  ;
        $active             = (!empty($request->active)) ? $request->active : null  ;
        $status_omitted     = (!empty($request->status_omitted)) ? $request->status_omitted : null  ;

        if (count($request->all())>0) {

            $creditoafavors = CreditoAFavor::withTrashed()
            ->select('credito_a_favors.*')
            ->join('representants', 'representants.id', '=', 'credito_a_favors.representant_id')
            //->leftjoin('estudiants', 'estudiants.id', '=', 'credito_a_favors.estudiant_id')
            // ->groupby('credito_a_favors.id')
            // ->groupby('representants.id')
            ->OrderBy('created_at','desc');

            $creditoafavors = (isset($finicial)) ? $creditoafavors->wheredate('credito_a_favors.created_at','>=',$finicial) : $creditoafavors;
            $creditoafavors = (isset($ffinal)) ? $creditoafavors->wheredate('credito_a_favors.created_at','<=',$ffinal) : $creditoafavors;

            if ($ci) {
                // $creditoafavors = $creditoafavors->whereNotNull('estudiants.id');
                // $creditoafavors = $creditoafavors->where('estudiants.ci_estudiant', 'like', "%".$ci."%");
                $creditoafavors = $creditoafavors->where('representants.ci_representant', 'like', "%".$ci."%");
            }

            if ($status_omitted=='SI') { $creditoafavors = $creditoafavors->where('credito_a_favors.status_omitted','true'); }
            if ($status_omitted=='NO') { $creditoafavors = $creditoafavors->where('credito_a_favors.status_omitted','false'); }

            if ($state=='APLICADO') {
                $creditoafavors = $creditoafavors
                    ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
                    ->join('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
                    ->whereNull('credito_aplicados.deleted_at')
                    ->whereNull('registro_pagos.deleted_at')
                    ->whereNotNull('credito_a_favors.deleted_at')
                    ;
            }
            if ($state=='NO APLICADO') { $creditoafavors = $creditoafavors->whereNull('credito_a_favors.deleted_at'); }

            if ($active=='SI') {
                $creditoafavors = $creditoafavors
                ->join('estudiants', 'estudiants.id', '=', 'credito_a_favors.estudiant_id')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->where('estudiants.status_active','true')
                ->where('representants.status_active','true')
                //->where('credito_a_favors.exchange_ammount','>',0.009)
                ;
            }

            $creditoafavors = $creditoafavors->get();

        }

        $currency_primary = $this->currency_primary;
        $currency_secondary = $this->currency_secondary;

        return view('administracion.creditoafavors.crud',compact('creditoafavors','currency_primary','currency_secondary','request','finicial','ffinal','ci','state','active','status_omitted'));
    }

    public function destroy($id, Request $request)
    {
        $creditoafavor = CreditoAFavor::withTrashed()->find($id);
        // $creditoafavor = CreditoAFavor::withTrashed()->where('id',$id)->first();
        $creditoafavor->forceDelete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.creditoafavors.crud');
    }

    public function edit($id, Request $request)
    {
        $creditoafavor = CreditoAFavor::findOrFail($id);
        $representant = ($creditoafavor) ? $creditoafavor ->representant : null;
        $help_representante = ($representant) ? $representant->ci_representant: null;
        $list_comment = CreditoAFavor::COLUMN_COMMENTS;
        $list_representant = Representant::list_representant();
        return view('administracion.creditoafavors.edit',compact('creditoafavor','list_comment','list_representant','help_representante'));
    }
    public function update(Request $request, $id)
    {
        // $help_representante = (!empty($request->help_representante)) ? $request->help_representante : null ;
        $creditoafavor = CreditoAFavor::findOrFail($id);
        $creditoafavor->fill($request->all());
        $creditoafavor->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.creditoafavors.edit',$id);
    }
}
