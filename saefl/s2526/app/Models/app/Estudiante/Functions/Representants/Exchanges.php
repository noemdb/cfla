<?php

namespace App\Models\app\Estudiante\Functions\Representants;

use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait Exchanges
{

    public function getStatusSolventExchangeAttribute()
    {
        return (round($this->exchange_ammount_expire_bill, 2) <= 0) ? true : false;
    }

    public static function getRepresentantsForCoutas($cuotas = null)
    {
        $representants = new Collection();
        $representants_formaly = Representant::representantFormaly();
        foreach ($representants_formaly as $item) {
            $count = $item->exchange_expire_bill_pendientes->count();
            if ($count >= $cuotas) {
                $representants->add($item);
            }
        }
        return $representants;
    }

    public function getExchangeAmmountExpireBillAttribute()
    {
        $total = 0;
        $estudiants = $this->estudiants; //dd($estudiants);

        foreach ($estudiants as $estudiant) {
            $total = $total + $estudiant->exchange_ammount_expire_bill;
        }

        return $total;
    }

    public function getBadExchangeAmmountExpireBillAttribute()
    {
        $total = 0;
        $estudiants = $this->estudiants;
        foreach ($estudiants as $estudiant) {
            $total = $total + $estudiant->bad_exchange_ammount_expire_bill;
        }
        return $total;
    }

    public function getLateIndexAttribute()
    {
        $late_payment = $this->late_payment;
        $late_index = round((100 * $late_payment), 1);
        return $late_index;
    }

    public function getTotalExchangeAmmountExpireBillAttribute()
    {
        $ammount_bill = $this->exchange_ammount_expire_bill;
        $ammount_caf = $this->total_credito_exchange;
        $ammount_abono = $this->total_abono_exchange;
        $saldo_a_favor = $ammount_caf + $ammount_abono;
        $ammount = ($ammount_bill > $saldo_a_favor) ? $ammount_bill - $saldo_a_favor : 0;
        return $ammount;
    }

    public function getExchangeAjusteAttribute()
    {
        $date = Carbon::now()->format('Y-m-d');
        $exchange_rate_current = ExchangeRate::whereDate('date', $date)->first();
        $ingreso_exchange_rate_id = ($exchange_rate_current) ? $exchange_rate_current->id : null;

        $total = null;

        $estudiants = $this->estudiants;
        foreach ($estudiants as $estudiant) {
            $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;
            $total_recursos_exchange = $exchange_ammount_expire_bill;
            if ($exchange_ammount_expire_bill < 0.01) {
                $combinado = RegistroPagoCombinado::create(['representant_id' => $this->id, 'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: ' . $date]);

                $cuentaxpagars  = $estudiant->exchange_expire_bills;
                foreach ($cuentaxpagars as $cuentaxpagar) {

                    //INI Ingreso
                    $ingreso_ammount = ($exchange_rate_current) ? $exchange_ammount_expire_bill * $exchange_rate_current->ammount : null;
                    $total_recursos_ammount = $ingreso_ammount;
                    $ingreso = Ingreso::create([
                        'estudiant_id' => $estudiant->id,
                        'representant_id' => $this->id,
                        'method_pay_id' => 3,
                        'banco_id' => 7,
                        'number_i_pay' => time(),
                        'date_transaction' => $date,
                        'date_payment' => $date,
                        'ingreso_ammount' => $ingreso_ammount,
                        'exchange_rate_id' => $ingreso_exchange_rate_id,
                        'exchange_ammount' => $exchange_ammount_expire_bill,
                        'ingreso_observations' => 'AJUSTE GENERADO AUTOMATICAMENTE',
                        'person_bill_ci' => $this->ci_representant,
                        'person_bill_name' => $this->name,
                    ]);
                    //FIN Ingreso

                    //INI RegistroPago
                    $registro = RegistroPago::create([
                        'estudiant_id' => $estudiant->id,
                        'representant_id' => $this->id,
                        'registro_pago_combinado_id' => $combinado->id,
                        'cuentaxpagar_id' => $cuentaxpagar->id,
                        'user_id' => Auth::user()->id
                    ]);
                    //FIN RegistroPago

                    //INI Concepto Cancelado
                    $conceptopagos = $cuentaxpagar->conceptopagos;
                    foreach ($conceptopagos as $conceptopago) {

                        $concepto_ammount =  $conceptopago->concepto_ammount;
                        $exchange_ammount =  $conceptopago->exchange_ammount;

                        if ($conceptopago->status_discount == 'true') {
                            $descuento_ammount = $estudiant->descuento_ammount($cuentaxpagar->id);
                            if ($descuento_ammount) {
                                $descuento_ammount = 1 - $descuento_ammount / 100;
                                $concepto_ammount =  $concepto_ammount * $descuento_ammount;
                                $exchange_ammount =  $exchange_ammount * $descuento_ammount;
                            }
                        }

                        $count = $conceptopagos->count();
                        $concepto_cancelado_ammount_exchange = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($estudiant->id) / $count;

                        if ($exchange_ammount > $concepto_cancelado_ammount_exchange) {

                            $total_concepto_ammount_exchange = $exchange_ammount - $concepto_cancelado_ammount_exchange;

                            if ($total_concepto_ammount_exchange > $total_recursos_exchange) {
                                $total_concepto_ammount_exchange = $total_recursos_exchange;
                            }

                            $factor = $total_concepto_ammount_exchange / $total_recursos_exchange;
                            $total_concepto_ammount = $factor * $total_recursos_ammount;

                            $status_partial = ($concepto_cancelado_ammount_exchange > 0) ? 'true' : 'false';

                            $concepto_cancelado_create = ConceptoCancelado::create([
                                'registro_pago_id' => $registro->id,
                                'concepto_pago_id' => $conceptopago->id,
                                'status_partial' => $status_partial,
                                'concepto_ammount' => $total_concepto_ammount,
                                'exchange_ammount' => $total_concepto_ammount_exchange,
                            ]);
                        }
                    }
                    //FIN Concepto Cancelado


                    //INI Pagado
                    $pago = Pago::create([
                        'registro_pago_id' => $registro->id,
                        'ingreso_id' => $ingreso->id,
                        'pagos_ammount' => $ingreso_ammount,
                        'exchange_ammount' => $exchange_ammount_expire_bill
                    ]);
                    //FIN Pagado
                    $total = $total + $exchange_ammount_expire_bill;
                }
            }
        }

        return $total;
    }

    public function getExchangeAmmountExpireBillQuota($date_start, $date_end)
    {
        $total = 0;
        $estudiants = $this->estudiants;

        foreach ($estudiants as $estudiant) {
            $total = $total + $estudiant->getExchangeAmmountExpireBillQuota($date_start, $date_end);
        }

        return $total;
    }

    public function getExchangeAmmountExpireBillDate($date = null, $morosidad = false)
    {
        $total = 0;
        $estudiants = $this->estudiants; //dd($date,$estudiants);

        foreach ($estudiants as $estudiant) {
            $total = $total + $estudiant->getExchangeAmmountExpireBillDate($date, $morosidad);
        }

        return $total;
    }

    public function getBsExchangeAmmountExpireBillAttribute()
    {
        $exchange_rate_current = ExchangeRate::whereDate('date', Carbon::now())->first();
        $exchange_ammount_expire_bill = $this->exchange_ammount_expire_bill;
        return ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
    }

    public function getExchangeExpireBillsAttribute()
    {
        $cuentaxpagars = collect();
        foreach ($this->estudiants as $estudiant) {
            $bills = $estudiant->exchange_expire_bills;
            if ($bills->count() > 0) {
                $cuentaxpagars = $cuentaxpagars->merge($bills);
            }
        }
        return $cuentaxpagars;
    }

    public function getExchangeExpireBillsCombinateAttribute()
    {
        $cuentaxpagars = collect();
        foreach ($this->estudiants as $estudiant) {
            $bills = $estudiant->exchange_expire_bills;
            if ($bills->count() > 0) {
                $cuentaxpagars = $cuentaxpagars->merge($bills);
            }
        }

        $cuentaxpagars = $cuentaxpagars->filter(function ($value, $key) {
            return is_null($value->quota_original_id);
        });

        return $cuentaxpagars->groupBy('name');
    }

    public function getTotalAbonoExchangeAttribute()
    {
        return $this->abonos_disponibles_exchange->sum('exchange_ammount');
    }

    public function getTotalAbonosMatriculationsExchangeAttribute()
    {
        return $this->abonos_matriculations_exchange->sum('exchange_ammount');
    }

    public function getAbonosDisponiblesExchangeAttribute()
    {
        $abonos =
            Abono::select('abonos.*', 'ingresos.exchange_ammount')
            ->Join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->leftJoin('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->where('abonos.representant_id', $this->id)
            ->whereNull('abono_aplicados.abono_id')
            ->wherenull('ingresos.deleted_at')
            ->wherenull('abonos.deleted_at')
            ->whereNotNull('ingresos.exchange_ammount')
            ->where('abonos.status_matriculations', false) // Excluyen abonos destinados para el aseguramiento de matriculas
            ->orderby('abonos.id', 'asc')
            ->get();
        return $abonos;
    }

    public function getAbonosMatriculationsExchangeAttribute()
    {
        $abonos =
            Abono::select('abonos.*', 'ingresos.exchange_ammount')
            ->Join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->leftJoin('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->where('abonos.representant_id', $this->id)
            ->whereNull('abono_aplicados.abono_id')
            ->wherenull('ingresos.deleted_at')
            ->wherenull('abonos.deleted_at')
            ->whereNotNull('ingresos.exchange_ammount')
            ->where('abonos.status_matriculations', true) // Excluyen abonos destinados para el aseguramiento de matriculas
            ->orderby('abonos.id', 'asc')
            ->get();
        return $abonos;
    }

    public function getCreditosDisponiblesExchangeAttribute()
    {

        $creditos =
            CreditoAFavor::select('credito_a_favors.*')
            // ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->leftJoin('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
            ->where('credito_a_favors.representant_id', $this->id)
            ->whereNull('credito_aplicados.credito_a_favor_id')
            ->wherenull('credito_a_favors.deleted_at')
            ->where('credito_a_favors.status_omitted', 'false')
            // ->wherenull('registro_pagos.deleted_at')
            // ->where('credito_a_favors.exchange_ammount','>',0.009)
            ->orderby('credito_a_favors.id', 'asc')
            ->get();
        //dd($creditos);
        return $creditos;
    }

    public function getTotalCreditoExchangeAttribute()
    {
        return $this->creditos_disponibles_exchange->sum('exchange_ammount');
    }

    public function getExchangeAmmountMeansAttribute()
    {
        $total_credito_exchange = $this->total_credito_exchange;
        $total_abono_exchange = $this->total_abono_exchange;
        $means = $total_credito_exchange + $total_abono_exchange;
        return $means;
    }

    public function getExchangeExpireBillPendientesDecimal($decimal = 2)
    {
        $estudiants = $this->estudiants; //dd($estudiants);
        $pendientes = collect();
        $datas = collect();
        $monto_total = array();
        $monto_totalBs = array();
        foreach ($estudiants as $estudiant) {
            $expire_bills = $estudiant->expire_bills;
            foreach ($expire_bills as $expire_bill) {
                $data = collect();
                $monto = round($expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id), $decimal);
                $montoBs = round($expire_bill->TotalMontoCuentasXPagarAdeudado($estudiant->id), $decimal);
                if ($monto > 0) {
                    if (!array_key_exists($expire_bill->name, $monto_total)) {
                        $monto_total[$expire_bill->name] = 0;
                        $monto_totalBs[$expire_bill->name] = 0;
                    }
                    $monto_total[$expire_bill->name] += $monto;
                    $monto_totalBs[$expire_bill->name] += $montoBs;
                    $data->put('expire_bill_name', $expire_bill->name); //date_expiration
                    $data->put('date_expiration', $expire_bill->date_expiration);
                    $data->put('ammount', $monto_total[$expire_bill->name]);
                    $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                    $pendientes->put($expire_bill->name, $data);
                }
            }
        }
        return $pendientes;
    }

    public function getTotalAmmountExchangePendientesAttribute()
    {
        try {
            // Obtener total de conceptos de pago
            $total_concepto_pago = $this->getTotalExchangeAmmountConcetoPago();
            
            // Obtener total pagado
            $total_pagado = $this->TotalExchangeMontoCuentasXPagarPagado();
            
            // Calcular pendiente (asegurar que no sea negativo)
            $pendiente = $total_concepto_pago - $total_pagado;
            
            return max(0, $pendiente); // Retornar al menos 0 para evitar valores negativos
        } catch (\Exception $e) {
            // Log del error si es necesario
            // Log::error('Error calculando pendientes: ' . $e->getMessage());
            return 0;
        }
    }

    public function getTotalAmmountExchangeExpireBillPendientesAttribute()
    {
        $pendientes = $this->exchange_expire_bill_pendientes;
        
        // Si no hay pendientes, retornar 0
        if ($pendientes->isEmpty()) {
            return 0;
        }
        
        // Sumar todos los valores de 'ammount' en la colección
        return $pendientes->sum('ammount');
    }

    public function getExchangeExpireBillPendientesAttribute()
    {
        $estudiants = $this->estudiants;
        $pendientes = collect();
        $monto_total = array();
        $monto_totalBs = array();
        foreach ($estudiants as $estudiant) {
            $expire_bills = $estudiant->expire_bills;
            foreach ($expire_bills as $expire_bill) {
                $data = collect();
                $monto = $expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                $montoBs = $expire_bill->TotalMontoCuentasXPagarAdeudado($estudiant->id);
                if ($monto > 0) {
                    if (!array_key_exists($expire_bill->name, $monto_total)) {
                        $monto_total[$expire_bill->name] = 0;
                        $monto_totalBs[$expire_bill->name] = 0;
                    }
                    $monto_total[$expire_bill->name] += $monto;
                    $monto_totalBs[$expire_bill->name] += $montoBs;
                    $data->put('expire_bill_name', $expire_bill->name); //date_expiration
                    $data->put('date_expiration', $expire_bill->date_expiration);
                    $data->put('ammount', $monto_total[$expire_bill->name]);
                    $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                    $data->put('status_late_payment', $expire_bill->status_late_payment);
                    $data->put('enable_late_payment', $expire_bill->enable_late_payment);
                    $data->put('date_late_payment', $expire_bill->date_late_payment);
                    $data->put('date_calendar_start', $expire_bill->date_calendar_start);
                    $data->put('date_calendar_end', $expire_bill->date_calendar_end);
                    $pendientes->put($expire_bill->name, $data);
                }
            }
        }
        return $pendientes;
    }

    public function getExchangeExpireBillPendientesAutoPaymentAttribute()
    {
        $estudiants = $this->estudiants; //dd($estudiants);
        $pendientes = collect();
        $datas = collect();
        $monto_total = array();
        $monto_totalBs = array();
        foreach ($estudiants as $estudiant) {
            // $expire_bills = $estudiant->expire_bills;
            $expire_bills = $estudiant->exchange_expire_bills;
            foreach ($expire_bills as $expire_bill) {
                $data = collect();
                $monto = $expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                $montoBs = $expire_bill->TotalMontoCuentasXPagarAdeudado($estudiant->id);
                if ($monto > 0) {
                    if (!array_key_exists($expire_bill->name, $monto_total)) {
                        $monto_total[$expire_bill->name] = 0;
                        $monto_totalBs[$expire_bill->name] = 0;
                    }
                    $monto_total[$expire_bill->name] += $monto;
                    $monto_totalBs[$expire_bill->name] += $montoBs;
                    $data->put('id', $expire_bill->id); //cuentaxpagar_id
                    $data->put('estudiant_id', $estudiant->id); //cuentaxpagar_id
                    $data->put('cuentaxpagar_id', $expire_bill->id); //cuentaxpagar_id
                    $data->put('expire_bill_name', $expire_bill->name); //date_expiration
                    $data->put('date_expiration', $expire_bill->date_expiration);
                    $data->put('ammount', $monto_total[$expire_bill->name]);
                    $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                    $name = $expire_bill->date_expiration . '|' . $expire_bill->name . '|' . $estudiant->id;
                    $pendientes->put($name, $data);
                }
            }
        }
        return $pendientes;
    }

    public function getExchangeUnexpiredBillPendientesAutoPaymentAttribute()
    {
        $estudiants = $this->estudiants; //dd($estudiants);
        $pendientes = collect();
        $datas = collect();
        $monto_total = array();
        $monto_totalBs = array();
        foreach ($estudiants as $estudiant) {
            $expire_bills = $estudiant->exchange_unexpired_bills;
            foreach ($expire_bills as $expire_bill) {
                $data = collect();
                $monto = $expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                $montoBs = $expire_bill->TotalMontoCuentasXPagarAdeudado($estudiant->id);
                if ($monto > 0) {
                    if (!array_key_exists($expire_bill->name, $monto_total)) {
                        $monto_total[$expire_bill->name] = 0;
                        $monto_totalBs[$expire_bill->name] = 0;
                    }
                    $monto_total[$expire_bill->name] += $monto;
                    $monto_totalBs[$expire_bill->name] += $montoBs;
                    $data->put('id', $expire_bill->id); //cuentaxpagar_id
                    $data->put('estudiant_id', $estudiant->id); //cuentaxpagar_id
                    $data->put('cuentaxpagar_id', $expire_bill->id); //cuentaxpagar_id
                    $data->put('expire_bill_name', $expire_bill->name); //date_expiration
                    $data->put('date_expiration', $expire_bill->date_expiration);
                    $data->put('ammount', $monto_total[$expire_bill->name]);
                    $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                    $name = $expire_bill->date_expiration . '|' . $expire_bill->name . '|' . $estudiant->id;
                    $pendientes->put($name, $data);
                }
            }
        }
        return $pendientes;
    }

    public function getExchangeUnexpireBillPendientesAttribute()
    {
        $estudiants = $this->estudiants; //dd($estudiants);


        $unexpire_bills = collect();
        $datas = collect();
        $monto_total = array();
        $monto_totalBs = array();
        foreach ($estudiants as $estudiant) {

            $expire_bills = $estudiant->exchange_unexpired_bills;

            foreach ($expire_bills as $expire_bill) {
                $data = collect();
                $monto = $expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                $montoBs = $expire_bill->TotalMontoCuentasXPagarAdeudado($estudiant->id);
                if ($monto > 0) {
                    if (!array_key_exists($expire_bill->name, $monto_total)) {
                        $monto_total[$expire_bill->name] = 0;
                        $monto_totalBs[$expire_bill->name] = 0;
                    }
                    $monto_total[$expire_bill->name] += $monto;
                    $monto_totalBs[$expire_bill->name] += $montoBs;
                    $data->put('date_calendar_start', $expire_bill->date_calendar_start); //date_expiration
                    $data->put('expire_bill_name', $expire_bill->name); //date_expiration
                    $data->put('date_expiration', $expire_bill->date_expiration);
                    $data->put('ammount', $monto_total[$expire_bill->name]);
                    $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                    $unexpire_bills->put($expire_bill->name, $data);
                }
            }
        }
        return $unexpire_bills->sortBy('date_calendar_start');
    }

    public function getExchangeAmmountUnexpiredBillAttribute()
    {
        $total = 0;
        $total_pagadas = 0;
        $total_x_pagar = 0;
        $estudiants = $this->estudiants; //dd($estudiants);
        foreach ($estudiants as $estudiant) {
            $cuentaxpagars = $estudiant->exchange_unexpired_bills; //dd($cuentaxpagars);
            foreach ($cuentaxpagars as $cuentaxpagar) {
                $total_x_pagar = $cuentaxpagar->TotalExchangeMontoCuentasXPagar($estudiant->id); //dd($total_x_pagar);
                $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($estudiant->id); //dd($total_pagadas);
                $total = $total + ($total_x_pagar - $total_pagadas);
            }
        }
        return $total;
    }

    public function TotalExchangeMontoCuentasXPagarPagado()
    {
        $total = 0;
        $total_pagadas = 0;
        $estudiants = $this->estudiants;
        foreach ($estudiants as $estudiant) {
            $cuentaxpagars = $estudiant->getQuotasPayment();
            foreach ($cuentaxpagars as $cuentaxpagar) {
                $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($estudiant->id);
                $total = $total + $total_pagadas;
            }
        }

        return $total;
    }

    public function getRegistroPagos()
    {
        $registropagos = RegistroPago::select('registro_pagos.*', 'cuentaxpagars.name as cuentaxpagar_name')
            ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
            ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_ammount')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('registro_pagos.representant_id', $this->id)
            ->orderBy('cuentaxpagars.date_expiration', 'desc')
            ->orderBy('cuentaxpagars.id', 'desc')
            // ->orderBy('registro_pagos.created_at','asc')
            // ->orderBy('registro_pagos.created_at','desc')
            ->groupBy('cuentaxpagars.id')
            ->get(); //dd($registropagos);
        return $registropagos;
    }

    public function getRegistroPagosCombinados()
    {
        $registropagos = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
            ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_ammount')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('registro_pago_combinados.representant_id', $this->id)
            ->orderBy('cuentaxpagars.date_expiration', 'desc')
            ->groupBy('cuentaxpagars.id')
            ->get(); //dd($registropagos);
        return $registropagos;
    }

    public function getExchangeAmmountUnexpiredBillPaidAttribute()
    {
        $total = 0;
        foreach ($this->estudiants as $estudiant) {
            $total = $total + $estudiant->exchange_ammount_unexpired_bill_paid;
        }
        return $total;
    }

    public function getTotalExchangeAmmountConcetoPago($date_start = null, $date_end = null)
    {
        $total = 0;
        foreach ($this->estudiants as $estudiant) {
            $total = $total + $estudiant->getTotalExchangeAmmountConcetoPago2($date_start, $date_end);
        }
        return $total;
    }

    public function getTotalExchangeAmmountIngreso($date_payment_end = null, $banco_id = null)
    {
        return $this->getIngresos($date_payment_end, $banco_id)->sum('ingresos.exchange_ammount');
    }

    public function getIngresos($date_start = null, $date_end = null, $banco_id = null)
    {
        $q = DB::table('ingresos')
            ->where('ingresos.representant_id', $this->id)
            ->whereNull('ingresos.deleted_at');

        $q = ($date_start) ? $q->whereDate('ingresos.date_payment', '>=', $date_start) : $q;
        $q = ($date_end) ? $q->whereDate('ingresos.date_payment', '<=', $date_end) : $q;
        $q = ($banco_id) ? $q->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')->where('bancos.id', $banco_id) : $q;

        return $q->get();
    }
}
