<?php

namespace App\Models\app\Estudiante\Functions\Representants;

use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\Prepago;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\Payment;
use Carbon\Carbon; //$ammount = round($representant->exchange_ammount_expire_bill,2);

trait Administrativo
{

    public function getRegistroPagosForRepresentanId ()
    {
        return RegistroPago::select('registro_pagos.*','cuentaxpagars.name as cuentaxpagar_name')
        ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
        ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_pagos_ammount')
        ->selectRaw('sum(credito_a_favors.credito_ammount) as total_credito_ammount')
        ->Join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
        ->Join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
        ->leftJoin('credito_a_favors', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
        ->where('registro_pagos.representant_id',$this->id)
        ->groupBy('registro_pagos.cuentaxpagar_id')
        ->orderBy('registro_pagos.created_at','desc')
        ->get();
    }

    public function getRegistroPagosGr()
    {
        $registro_pagos = RegistroPago::select('registro_pagos.*')
        ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
        ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_ammount')
        ->join('pagos','registro_pagos.id','=','pagos.registro_pago_id')
        ->join('cuentaxpagars', 'cuentaxpagars.id','=','registro_pagos.cuentaxpagar_id')
        ->where('registro_pagos.representant_id',$this->id)
        ->orderBy('cuentaxpagars.date_expiration','desc')
        ->groupBy('cuentaxpagars.id')
        ->get();
        return $registro_pagos;
    }

    public function getPaymentsAttribute()
    {
        $payments = Payment::where('ci_representant',$this->ci_representant)->get();
        return $payments;
    }
    public function getAmmountUnexpiredBillPaidAttribute()
    {
        $total=0;
        foreach ($this->estudiants as $estudiant) {
            $total = $total + $estudiant->ammount_unexpired_bill_paid;
        }
        return $total;
    }

    public function getCuentaxpagarsAttribute()
    {
        $planpago_id = ( !empty($this->administrativa->planpago_id) ) ? $this->administrativa->planpago_id : '0' ;
        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
                ->join('planpagos','planpagos.id','=','cuentaxpagars.planpago_id')
                ->join('administrativas','planpagos.id','=','administrativas.planpago_id')
                ->join('estudiants','estudiants.id','=','administrativas.estudiant_id')
                ->join('representants','representants.id','=','estudiants.representant_id')
                ->Where('date_expiration','<',Carbon::now())
                ->Where('cuentaxpagars.type','GENERAL')
                ->Where('representants.id',$this->id)
                ->OrderBy('cuentaxpagars.date_expiration','asc')
                ->get();

        return $cuentaxpagars;
    }

    public function getCountExpiredBillsAttribute()
    {
        $estudiants = $this->estudiants; //dd($estudiants); estudiants_formaly
        $count = null;
        $bills = array();
        foreach ($estudiants as $estudiant) {
            $expire_bills = $estudiant->expire_bills; //dd($expire_bills);
            foreach ($expire_bills as $expire_bill) {
                $status_pay = $expire_bill->StatusPayExchange($estudiant->id); //dd($status_pay);
                if (!$status_pay && !in_array($expire_bill->name, $bills)) {
                    array_push($bills, $expire_bill->name);
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getLatePaymentAttribute()
    {
        $estudiants = $this->estudiants; //dd($estudiants); estudiants_formaly
        $goal = null;
        $real = null;
        foreach ($estudiants as $estudiant) {
            $expire_bills = $estudiant->expire_bills; //dd($expire_bills);
            $goal = $expire_bills->count() + $goal; //dd($goals );
            foreach ($expire_bills as $expire_bill) {
                $status_pay = $expire_bill->StatusPayExchange($estudiant->id); //dd($status_pay);
                $real = ($status_pay) ? $real : ($real + 1) ;
            }
        }
        $indice = ($goal) ? ( $real / $goal ) : null;
        return $indice;
    }

    public function getMeetPaymentAttribute()
    {
        $estudiants = $this->estudiants; //dd($estudiants); estudiants_formaly
        $goal = null;
        $real = null;
        foreach ($estudiants as $estudiant) {
            $expire_bills = $estudiant->expire_bills; //dd($expire_bills);
            $goal = $expire_bills->count() + $goal; //dd($goals );
            foreach ($expire_bills as $expire_bill) {
                $status_pay = $expire_bill->StatusPayExchange($estudiant->id); //dd($status_pay);
                $real = ($status_pay) ? ($real + 1) : $real ;
            }
        }
        $indice = ($goal) ? ( $real / $goal ) : null;
        return $indice;
    }

    public function getCuentasPagadasAttribute()
    {
        $registropagos = RegistroPago::select('registro_pagos.*')
        ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
        ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_ammount')
        ->join('pagos','registro_pagos.id','=','pagos.registro_pago_id')
        ->join('cuentaxpagars', 'cuentaxpagars.id','=','registro_pagos.cuentaxpagar_id')
        ->where('registro_pagos.representant_id',$this->id)
        ->orderBy('cuentaxpagars.date_expiration','desc')
        ->groupBy('cuentaxpagars.id')
        ->get(); //dd($registropagos);
        return $registropagos;
    }

    public function getTotalAbonoAttribute()
    {
        $total = 0;
        foreach ($this->abonos_disponibles as $abono) {
            $total = $total + $abono->ingreso->ingreso_ammount;
        }
        return $total;
    }

    public function getAbonosDisponiblesAttribute()
    {
        $abonos =
            Abono::select('abonos.*')
                ->Join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                ->leftJoin('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
                ->where('abonos.representant_id',$this->id)
                ->whereNull('abono_aplicados.abono_id')
                ->wherenull('ingresos.deleted_at')
                ->wherenull('abonos.deleted_at')
                ->where('abonos.status_matriculations',false) // Excluyen abonos destinados para el aseguramiento de matriculas
                // ->wherenotnull('abono_aplicados.deleted_at')
                ->orderby('abonos.id','asc')
                ->get();
        // dd($abonos);
        return $abonos;
    }

    public function AbonosDisponiblesTest($abono_id)
    {
        $abonos =
            Abono::select('abonos.*')
                ->Join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                ->leftJoin('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
                ->where('abonos.representant_id',$this->id)
                ->whereNull('abono_aplicados.abono_id')
                ->wherenull('ingresos.deleted_at')
                ->wherenull('abonos.deleted_at')
                ->wherenull('abono_aplicados.deleted_at')
                ->wherenotnull('abono_aplicados.deleted_at')
                ->where('abonos.id',$abono_id)
                ->orderby('abonos.id','asc')
                ->first();
        return $abonos;
    }

    public function getCreditosDisponiblesAttribute()
    {
        $creditos =
            CreditoAFavor::select('credito_a_favors.*')
                ->leftJoin('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
                ->where('credito_a_favors.representant_id',$this->id)
                ->whereNull('credito_aplicados.credito_a_favor_id')
                ->wherenull('credito_a_favors.deleted_at')
                ->where('credito_a_favors.status_omitted','false')
                ->orderby('credito_a_favors.id','asc')
                ->get();
        return $creditos;
    }

    public function CreditosAFavorDisponiblesTest ($credito_a_favor_id)
    {
        $credito =
            CreditoAFavor::select('credito_a_favors.*')

                ->leftJoin('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')

                ->where('credito_a_favors.representant_id',$this->id)
                ->where('credito_a_favors.id',$credito_a_favor_id)

                ->whereNull('credito_aplicados.credito_a_favor_id')
                ->wherenull('credito_aplicados.deleted_at')

                ->orderby('credito_a_favors.id','asc')
                ->first();
        return $credito;
    }

    public function getTotalCreditoAttribute()
    {
        return $this->creditos_disponibles->sum('credito_ammount');
    }

    public function getPrepagoDisponiblesAttribute()
    {
        $prepagos =
            Prepago::select('prepagos.*')
                ->whereNull('prepagos.status_apply')
                ->where('prepagos.status_approved','true')
                ->where('prepagos.representant_id',$this->id)
                ->get();
        return $prepagos;
    }

    public function getTotalPrepagoAttribute()
    {
        return $this->prepago_disponibles->sum('ingreso_ammount');
    }

    public function getAmmountExpireBillAttribute()
    {
        $id = $this->id;
        $total=0;
        foreach ($this->estudiants as $estudiant) {
            $total = $total + $estudiant->ammount_expire_bill;
        }
        return $total;
    }

    public function getAmmountNoExpireBillAttribute()
    {
        $total=0;
        foreach ($this->estudiants as $estudiant) {
            $total = $total + $estudiant->ammount_no_expire_bill;
        }
        return $total;
    }
    /* FIN FINANCIERO-----------------------------------------------------------------------*/

    public static function getSentinela() /* usada para llenar los objetos de formularios select*/
    {
        $sentinela = Representant::where('ci_representant','100000000')->first();
        return ($sentinela) ? $sentinela : null ;
    }

    public function getFullPhoneAttribute()
    {
        $phone = (!empty($this->phone)) ? $this->phone.' ' : null ;

        // $arr_phone_rep = explode("/", $this->phone);

        // foreach ($arr_phone_rep as $k => $v) {

        //     $phone = $v;

        //     foreach ($this->estudiants as $estudiant) {

        //         $arr_phone_est = explode("/", $estudiant->phone);

        //         foreach ($arr_phone_est as $sk => $sv) {

        //             $estudiant_phone = $sv;

        //             $estudiant_phone_sm = substr($estudiant_phone,-6);

        //             $pos_phone =  (!empty($estudiant_phone_sm)) ? strpos($phone, $estudiant_phone_sm) : null;

        //             if ($pos_phone === false && !(empty($estudiant_phone))) {
        //                 $phone .= ' '.$estudiant_phone;
        //             }
        //         }

        //     }
        // }

        return $phone;
    }

    public function getExpireBillPendientesAttribute()
    {
        $estudiants = $this->estudiants; //dd($estudiants);
        $pendientes = collect();
        $datas = collect();
        $monto_total = array();
        foreach ($estudiants as $estudiant) {
            $expire_bills = $estudiant->expire_bills;
            foreach ($expire_bills as $expire_bill) {
                $data = collect();
                $monto = $expire_bill->TotalMontoConceptosXPagar($estudiant->id);
                if ($monto > 0) {
                    if (!array_key_exists($expire_bill->name,$monto_total)) { $monto_total[$expire_bill->name] = 0; }
                    $monto_total[$expire_bill->name] += $monto;
                    $data->put('expire_bill_name',$expire_bill->name);
                    $data->put('ammount',$monto_total[$expire_bill->name]);
                    $pendientes->put($expire_bill->name,$data);
                }
            }
        }
        return $pendientes;
    }

    public function getPlanBeneficosAttribute()
    {
        $profesors = PlanBenefico::select('plan_beneficos.*')
            ->join('estudiants', 'estudiants.id', '=', 'plan_beneficos.estudiant_id')
            ->where('estudiants.representant_id',$this->id)
            ->where('estudiants.status_active','true')
            ->whereNull('estudiants.deleted_at')
            ->groupBy('plan_beneficos.id')
            ->get();
        return $profesors;
    }
}
