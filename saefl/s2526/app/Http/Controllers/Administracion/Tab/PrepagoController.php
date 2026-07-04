<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Administracion\Tab\Functions\Prepago\CargaCSV;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administracion\Estudiant\CreateAbonoRequest;
use App\Http\Requests\Administracion\Planpago\CreatePrepagoRequest;
use App\Http\Requests\Administracion\Planpago\CreateRegistroPagoRequest;
use App\Imports\PrepagosImport;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Descuento;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\Mbancario;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\Prepago;
use App\Models\app\Planpago\Recurso;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PrepagoController extends Controller
{
    use CargaCSV;

    public function create(Request $request)
    {
        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;

        $mbancarios = collect();

        if (count($request->all())>0) {

            $mbancarios = 
                Mbancario::select('mbancarios.*')
                    ->leftjoin('prepagos', 'mbancarios.number_i_pay', '=', 'prepagos.number_i_pay')
                    ->leftjoin('ingresos', 'mbancarios.number_i_pay', '=', 'ingresos.number_i_pay')
                    ->whereNull('prepagos.id')
                    ->whereNull('ingresos.id')
                    ;
            $mbancarios = (isset($finicial)) ? $mbancarios->wheredate('mbancarios.date_transaction','>=',$finicial) : $mbancarios;
            $mbancarios = (isset($ffinal)) ? $mbancarios->wheredate('mbancarios.date_transaction','<=',$ffinal) : $mbancarios;
            $mbancarios = (isset($number_i_pay)) ? $mbancarios->where('mbancarios.number_i_pay',$number_i_pay) : $mbancarios;
            $mbancarios = (isset($banco_id)) ? $mbancarios->where('mbancarios.banco_id',$banco_id) : $mbancarios;

            $mbancarios = $mbancarios->get();
        }

        $list_banco = Banco::list_public_bancos();

        return view('administracion.prepagos.create', compact('mbancarios','finicial','ffinal','banco_id','number_i_pay','list_banco'));
    }

    public function store(Request $request)
    {
        $prepago = Prepago::create($request->all());
        $messenge = trans('db_oper_result.create_ok');        
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.prepagos.create');
    }

    public function validations(Request $request)
    {

        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null  ;
        $status_approved       = (!empty($request->status_approved)) ? $request->status_approved : null  ;

        $prepagos = collect();

        if (count($request->all())>0) {
            
            $prepagos = Prepago::select('prepagos.*');

            $prepagos = (isset($finicial)) ? $prepagos->wheredate('prepagos.date_transaction','>=',$finicial) : $prepagos;
            $prepagos = (isset($ffinal)) ? $prepagos->wheredate('prepagos.date_transaction','<=',$ffinal) : $prepagos;
            $prepagos = (isset($banco_id)) ? $prepagos->where('prepagos.banco_id',$banco_id) : $prepagos;
            $prepagos = (isset($number_i_pay)) ? $prepagos->where('prepagos.number_i_pay', 'like', "%".$number_i_pay."%") : $prepagos;
            $prepagos = (isset($ci_representant)) ? $prepagos->join('representants', 'representants.id', '=', 'prepagos.representant_id')->where('representants.ci_representant', 'like', "%".$ci_representant."%") : $prepagos;
            
            $prepagos = ($status_approved=='SI') ? $prepagos->where('prepagos.status_approved','true') : $prepagos;
            $prepagos = ($status_approved=='NO') ? $prepagos->whereNull('prepagos.status_approved')->orWhere('prepagos.status_approved','false') : $prepagos;

            $prepagos = $prepagos->get();
        }

        $list_banco = Banco::list_public_bancos();

        return view('administracion.prepagos.validations', compact('prepagos','finicial','ffinal','banco_id','ci_representant','number_i_pay','status_approved','list_banco'));
    }

    public function crud(Request $request)
    {

        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null  ;
        $status_approved       = (!empty($request->status_approved)) ? $request->status_approved : null  ;
        $status_apply       = (!empty($request->status_apply)) ? $request->status_apply : null  ;

        $prepagos = collect();        

        if (count($request->all())>0) {
            $prepagos = Prepago::select('prepagos.*');
            $prepagos = (isset($finicial)) ? $prepagos->wheredate('prepagos.date_transaction','>=',$finicial) : $prepagos;
            $prepagos = (isset($ffinal)) ? $prepagos->wheredate('prepagos.date_transaction','<=',$ffinal) : $prepagos;
            $prepagos = (isset($banco_id)) ? $prepagos->where('prepagos.banco_id',$banco_id) : $prepagos;
            $prepagos = (isset($number_i_pay)) ? $prepagos->where('prepagos.number_i_pay', 'like', "%".$number_i_pay."%") : $prepagos;
            $prepagos = (isset($ci_representant)) ? $prepagos->join('representants', 'representants.id', '=', 'prepagos.representant_id')->where('representants.ci_representant', 'like', "%".$ci_representant."%") : $prepagos;

            $prepagos = ($status_approved=='SI') ? $prepagos->where('prepagos.status_approved','true') : $prepagos;
            $prepagos = ($status_approved=='NO') ? $prepagos->whereNull('prepagos.status_approved')->orWhere('prepagos.status_approved','false') : $prepagos;

            $prepagos = ($status_apply=='SI') ? $prepagos->where('prepagos.status_apply','true') : $prepagos;
            $prepagos = ($status_apply=='NO') ? $prepagos->whereNull('prepagos.status_apply')->orWhere('prepagos.status_apply','false') : $prepagos;

            $prepagos = $prepagos->get();
        }

        $list_banco = Banco::list_public_bancos();

        return view('administracion.prepagos.crud', compact('prepagos','finicial','ffinal','banco_id','ci_representant','number_i_pay','status_apply','status_approved','list_banco'));

    }

    public function associated(Request $request)
    {

        // dd($request->all());

        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null  ;
        $status_approved       = (!empty($request->status_approved)) ? $request->status_approved : null  ;
        $status_apply       = (!empty($request->status_apply)) ? $request->status_apply : null  ;

        $prepagos = collect();
        if (count($request->all())>0) {
            $prepagos = 
                Prepago::select('prepagos.*')
                ->where('prepagos.status_approved','true')
                ->whereNull('prepagos.status_apply')
                ;
            $prepagos = ($finicial) ? $prepagos->wheredate('prepagos.date_transaction','>=',$finicial) : $prepagos;
            $prepagos = ($ffinal) ? $prepagos->wheredate('prepagos.date_transaction','<=',$ffinal) : $prepagos;
            $prepagos = ($banco_id) ? $prepagos->where('prepagos.banco_id',$banco_id) : $prepagos;
            $prepagos = ($number_i_pay) ? $prepagos->where('prepagos.number_i_pay', 'like', "%".$number_i_pay."%") : $prepagos;
            // $prepagos = ($ci_representant) ? $prepagos->join('representants', 'representants.id', '=', 'prepagos.representant_id')->where('representants.ci_representant', 'like', "%".$ci_representant."%") : $prepagos;
            $prepagos = ($ci_representant) ? $prepagos->join('representants', 'representants.id', '=', 'prepagos.representant_id')->where('representants.ci_representant',$ci_representant) : $prepagos;

            $prepagos = $prepagos->get();
        }

        $list_banco = Banco::list_public_bancos();

        return view('administracion.prepagos.associated', compact('prepagos','finicial','ffinal','banco_id','ci_representant','number_i_pay','status_apply','status_approved','list_banco'));
    }

    public function storePago(CreatePrepagoRequest $request)
    {

        // dd($request->all());

        // $ingreso_ammount = $request->ingreso_ammount;
        // $ingreso_ammount = $request->ingreso_ammount;
        $ingreso_ammount = (!empty($request->ingreso_ammount)) ? $request->ingreso_ammount : null ;
        $abono_description = (!empty($request->abono_description)) ? $request->abono_description : null ;
        $cuentasxpagars_arr = (!empty($request->cuentasxpagars)) ? $request->cuentasxpagars : array() ;
        $conceptopagos_arr = (!empty($request->conceptopagos)) ? $request->conceptopagos : array() ;
        $descuentos_arr = (!empty($request->descuento)) ? $request->descuento : array() ;
        $abonos_arr = (!empty($request->abonos)) ? $request->abonos : array() ;
        $cafs_arr = (!empty($request->cafs)) ? $request->cafs : array() ;
        $representant_id = (!empty($request->representant_id)) ? $request->representant_id : null ;
        $representant = Representant::findOrfail($representant_id);
        $estudiant_id = (!empty($request->estudiant_id)) ? $request->estudiant_id : null ;
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $prepago_id = (!empty($request->prepago_id)) ? $request->prepago_id : null ;
        $prepago = Prepago::findOrfail($prepago_id);

        $total_bills = 0;
        $total_abonos = 0;
        $total_cafs = 0;

        foreach ($conceptopagos_arr as $estudiant_id => $conceptopago_arr) {
            $ammount = array_sum($conceptopago_arr);
            $total_bills += $ammount;
        }
        foreach ($abonos_arr as $estudiant_id => $abono_arr) {
            $ammount = array_sum($abono_arr);
            $total_abonos += $ammount;
        }
        foreach ($cafs_arr as $estudiant_id => $caf_arr) {
            $ammount = array_sum($caf_arr);
            $total_cafs += $ammount;
        }

        $total_ingreso = $ingreso_ammount + $total_abonos + $total_cafs;

        // dd($request->all(),'total_bills',$total_bills,'total_abonos',$total_abonos,'total_cafs',$total_cafs,'total_ingreso',$total_ingreso);

        $datas = collect();

        if ($total_bills > 0 && $total_ingreso > 0 ) {

            $prepago->fill(['status_apply'=>'true']);
            $prepago->save();

            //INI PAGO COMBINADO
                $data = collect();
                $arr = [
                    'representant_id' => $representant->id,
                    'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: '.Carbon::now()->format('Y-m-d')
                ]; $data->put('COMBINADO',$arr); $datas->push($data);
                $combinado = RegistroPagoCombinado::create($arr);
            //FIN PAGO COMBINADO

            //INI INGRESO
                $data = collect();
                $arr = [
                    'estudiant_id' => $estudiant->id,
                    'representant_id' => $representant->id,
                    'method_pay_id' => $prepago->method_pay_id,
                    'banco_id' => $prepago->banco_id,
                    'number_i_pay' =>$prepago->number_i_pay,
                    'date_transaction' =>$prepago->date_transaction,
                    'ingreso_ammount' => $prepago->ingreso_ammount,
                    // 'ingreso_observations' => $prepago->ingreso_observations,
                    'ingreso_observations' => $prepago->abono_description,
                    'person_bill_ci' => $representant->ci_representant,
                    'person_bill_name' =>$representant->name,
                ]; $data->put('INGRESO',$arr); $datas->push($data);
                $ingreso = Ingreso::create($arr);
                $recurso_ingreso = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso->id,'status_ingreso' => 'true']);
            //FIN INGRESO

            foreach ($conceptopagos_arr as $estudiant_id => $conceptopago_arr) {

                foreach ($conceptopago_arr as $conceptopago_id => $ammount) {

                    if ($total_ingreso > 0) {

                        $data = collect();

                        $estudiant = Estudiant::findOrFail($estudiant_id);

                        $conceptopago = ConceptoPago::findOrFail($conceptopago_id);
                        $cuentaxpagar = $conceptopago->cuentaxpagar;

                        //INI RegistroPago
                            $arr = [
                                'estudiant_id' => $estudiant->id,
                                'representant_id' => $representant->id,
                                'registro_pago_combinado_id' => $combinado->id,
                                'cuentaxpagar_id' => $cuentaxpagar->id,
                                'user_id' => Auth::user()->id
                            ]; $data->put('RegistroPago',$arr);
                            $registro = RegistroPago::create($arr);
                        //FIN RegistroPago

                        //INI PAGO
                            $pagos_ammount = ($total_ingreso > $ammount) ? $ammount : $total_ingreso ;
                            $total_ingreso = ($total_ingreso > $ammount) ? $total_ingreso - $ammount : 0 ;
                            $arr = [
                                'registro_pago_id' => $registro->id,
                                'ingreso_id' => $ingreso->id,
                                'pagos_ammount' => $pagos_ammount
                            ]; $data->put('PAGO',$arr);
                            $pago = Pago::create($arr);
                        //FIN PAGO

                        //INI ConceptoCancelado
                            $status_paid = ( $pagos_ammount >= $ammount ) ? 'true':'false' ;
                            $arr = [
                                'registro_pago_id' => $registro->id,
                                'concepto_pago_id' => $conceptopago->id,
                                'concepto_ammount' => $pagos_ammount,
                                'status_paid' => $status_paid
                            ]; $data->put('ConceptoCancelado',$arr);
                            $concepto_create = ConceptoCancelado::create($arr);
                        //FIN ConceptoCancelado

                        //INI Descuento
                            if (array_key_exists($estudiant_id,$descuentos_arr)) {
                                $descuento_arr = $descuentos_arr[$estudiant_id];
                                foreach ($descuento_arr as $descuento_id => $ammount) {
                                    $data = collect();
                                    $descuento = Descuento::find($descuento_id);
                                    if ($descuento) {
                                        $arr = [
                                            'registro_pago_id' => $registro->id,
                                            'descuento_id' => $descuento->id
                                        ]; $data->put('DescuentoAplicado',$arr);
                                        $descuento = DescuentoAplicado::create($arr);
                                        $datas->push($data);
                                    }
                                }
                            }
                        //FIN Descuento

                        $datas->push($data);

                    }
                }

            }

            //INI AbonoAplicados
                foreach ($abonos_arr as $estudiant_id => $abono_arr) {
                    foreach ($abono_arr as $abono_id => $ammount) {
                        $data = collect();
                        $abono = Abono::find($abono_id);
                        $arr = [
                            'registro_pago_id' => $registro->id,
                            'abono_id' => $abono->id
                        ]; $data->put('AbonoAplicado',$arr);
                        $abono_aplicado = AbonoAplicado::create($arr);
                        $ingreso_aplicado = Ingreso::findOrFail($abono->ingreso_id);
                        $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso_aplicado->id,'status_abono' => 'true']);
                        $abono->delete();
                        $datas->push($data);
                        // dd($abono,$arr,$abono_aplicado);
                    }
                }
            //FIN AbonoAplicados

            //INI CreditosAplicados
                foreach ($cafs_arr as $estudiant_id => $credito_arr) {
                    foreach ($credito_arr as $credito_id => $ammount) {
                        $data = collect();
                        $credito = CreditoAFavor::find($credito_id);
                        if ($credito) {
                            $arr = [
                                'registro_pago_id' => $registro->id,
                                'credito_a_favor_id' => $credito->id,
                            ]; $data->put('CreditosAplicados',$arr);
                            $credito_aplicado = CreditoAplicado::create($arr);
                            $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'credito_a_favor_id' => $credito->id,'status_credito' => 'true']);
                            $credito->delete();
                            $datas->push($data);
                        }
                    }
                }
            //FIN CreditosAplicados

            /* INI Creditos a favor generados */
                $registro_id = 1; $combinado_id = 1; $ingreso_id = 1;
                $data = collect();
                if ($total_ingreso > 0 ) {
                    $credito_ammount = round($total_ingreso,2);
                    $arr = [
                        'representant_id' => $representant->id,
                        'estudiant_id' => $estudiant->id,
                        'registro_pago_id' => $registro->id,
                        'ingreso_id' => $ingreso->id,
                        'credito_description' => 'CAF: REGCID'.$combinado_id.';F'.Carbon::now(),
                        'credito_observations' => 'Crédito generado en la fecha: '.Carbon::now().' | Derivado de los registro de pago ID: '.$registro->id.' | Transacción: ID: '.$ingreso->id.' - Número: '.$prepago->number_i_pay,
                        'credito_ammount' => $credito_ammount,
                    ]; $data->put('CreditoAFavor',$arr);
                    $credito_a_Favor = CreditoAFavor::create($arr);
                    $datas->push($data);
                }
            /* FIN Creditos a favor generados */

            $messenge = 'Registros guardado exitosamente';
            $operation = 'operp_ok';

        }
        else {
            $messenge = 'Ingresos insuficientes';
            $operation = 'operp_not_ok';
        }

        // dd($combinado,$ingreso,$registro,$pago,$descuento,$abono,$credito,$credito_a_Favor,$datas);

        $messenge = trans('db_oper_result.oper_ok');
        $operation= 'create';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        $status_apply = 'false';
        Session::flash($operation,$messenge);
        return redirect()->route('administracion.prepagos.crud',compact('status_apply'));


    }

    public function storeAbono(CreateAbonoRequest $request)
    {
        // dd($request->all());
        $estudiant_id = $request->estudiant_id;
        $representant_id = $request->representant_id;
        $prepago_id = $request->prepago_id;
        $estudiant = Estudiant::findOrFail($estudiant_id);
        $representant = Representant::findOrFail($representant_id);
        $prepago = Prepago::findOrFail($prepago_id);

        $ingreso = Ingreso::create([
            'estudiant_id' => $estudiant->id,
            'representant_id' => $representant->id,
            'method_pay_id' => $request->method_pay_id,
            'banco_id' => $request->banco_id,
            'number_i_pay' =>$request->number_i_pay,
            'date_transaction' =>$request->date_transaction,
            'ingreso_ammount' =>$request->ingreso_ammount,
            'ingreso_observations' => $request->ingreso_observations,
            'person_bill_ci' => $representant->ci_representant,
            'person_bill_name' =>$representant->name,
        ]);

        $abono = Abono::create([
            'representant_id' => $estudiant->representant->id,
            'estudiant_id' => $estudiant->id,
            'ingreso_id' => $ingreso->id,
            'abono_description' => $request->abono_description,
        ]);

        $prepago->fill([ 'status_apply'=>'true' ]);
        $prepago->save();

        $messenge = trans('db_oper_result.oper_ok');
        $operation= 'create';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        $status_apply = 'false';

        Session::flash('operp_ok','Registros guardado exitosamente');
        return redirect()->route('administracion.prepagos.crud',compact('status_apply'));
    }

    public function destroy($id, Request $request)
    {
        $prepago = Prepago::findOrFail($id);
        $prepago->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.prepagos.carga.csv');
    }

    public function update(Request $request, $id)
    {
        $prepago = Prepago::findOrFail($id);
        $ci_representant = (!empty($request->ci_representant)) ? $request->ci_representant : null ;
        $banco_id = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $ingreso_ammount = (!empty($request->ingreso_ammount)) ? $request->ingreso_ammount : null ;
        $date_transaction = (!empty($request->date_transaction)) ? $request->date_transaction : null ;
        $ingreso_observations = (!empty($request->ingreso_observations)) ? $request->ingreso_observations : null ;
        $status_approved = (!empty($request->status_approved)) ? $request->status_approved : null ;
        
        $arr = [
            'banco_id'=>$banco_id,
            'ingreso_ammount'=>$ingreso_ammount,
            'date_transaction'=>$date_transaction,
            'ingreso_observations'=>$ingreso_observations,
            'status_approved'=>$status_approved,
        ];
        if ($ci_representant) {
            $representant = Representant::where('ci_representant',$ci_representant)->first();
            $representant_id = ($representant) ? $representant->id:null;
            if ($representant_id) {
                $arr['representant_id'] = $representant_id;
            }
        }

        $prepago->fill($arr);
        $prepago->save();

        $messenge = trans('db_oper_result.update_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.prepagos.validations');
    }

}
