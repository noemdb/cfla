<?php

namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait Exchanges
{

    /**
     * Relación: cuotas de recargo por morosidad generadas para este estudiante.
     * 
     * Se identifican por:
     * - status_late_payment = true
     * - enable_late_payment = false
     */
    public function recargosMorosidad()
    {
        return $this->hasMany(Cuentaxpagar::class, 'estudiant_id')
            ->where('status_late_payment', true)
            ->where('enable_late_payment', false);
    }

    /**
     * Accesor: cantidad de recargos por morosidad activos.
     * 
     * Uso: $estudiante->cantidad_recargos_morosidad
     */
    public function getCantidadRecargosMorosidadAttribute()
    {
        return $this->recargosMorosidad()->count();
    }

    public function getTotalMontoCuentasXPagarPagadoCuotaName($cuotaName)
    {
        $cuotaName = mb_strtoupper(trim($cuotaName), 'UTF-8');

        $row = DB::table('registro_pagos')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->whereNull('registro_pagos.deleted_at')
            ->whereNull('pagos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at')
            ->where('registro_pagos.estudiant_id', $this->id)
            ->where('cuentaxpagars.name', $cuotaName)
            ->where('cuentaxpagars.status_bad', 'false')
            ->where('cuentaxpagars.status_exchange', 1)
            ->selectRaw('SUM(pagos.pagos_ammount) AS ammount_local')
            ->first();

        return $row ? (float) round($row->ammount_local, 8) : null;
    }

    public function getTotalExchangeMontoCuentasXPagarPagadoCuotaName($cuotaName)
    {
        $cuotaName = mb_strtoupper(trim($cuotaName), 'UTF-8');

        $row = DB::table('registro_pagos')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->whereNull('registro_pagos.deleted_at')
            ->whereNull('pagos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at')
            ->where('registro_pagos.estudiant_id', $this->id)
            ->where('cuentaxpagars.name', $cuotaName)
            ->where('cuentaxpagars.status_bad', 'false')
            ->where('cuentaxpagars.status_exchange', 1)
            ->selectRaw('SUM(pagos.exchange_ammount) AS ammount')
            ->first();

        return $row ? (float) round($row->ammount, 8) : null;
    }


    public function getAllQuotas()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $estudiant_id = $this->id;
        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.status_exchange', true)
            ->Where('cuentaxpagars.status_bad', 'false') //se excleyen cunetas incobrales

            ->Where(function ($query) use ($estudiant_id) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere('cuentaxpagars.estudiant_id', $estudiant_id);
            })

            ->OrderBy('cuentaxpagars.date_calendar_start', 'asc')
            // ->OrderBy('cuentaxpagars.date_expiration', 'asc')
        ;

        $cuentaxpagars = $cuentaxpagars->get();

        return $cuentaxpagars;
    }

    public function getQuotasPayment()
    {
        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->join('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->Where('registro_pagos.estudiant_id', $this->id)
            ->OrderBy('cuentaxpagars.date_expiration', 'asc')
            ->groupBy('cuentaxpagars.id')
            ->get();

        return $cuentaxpagars;
    }

    public function getExchangeAmmountExpireBillAttribute()
    {
        $id = $this->id;
        $total = 0;
        $total_pagadas = 0;
        $total_x_pagar = 0;
        $cuentaxpagars = $this->exchange_expire_bills;
        foreach ($cuentaxpagars as $cuentaxpagar) {
            $total_x_pagar = $cuentaxpagar->TotalExchangeMontoCuentasXPagar($this->id); //dd($total_x_pagar);
            $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($this->id); //dd($total_pagadas);
            $total = $total + ($total_x_pagar - $total_pagadas);
        }
        return round($total, 8);
    }

    public function getExchangeExpireBillsAttribute()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $estudiant_id = $this->id;
        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->Where('date_expiration', '<=', Carbon::now())
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.status_exchange', true)
            ->Where('cuentaxpagars.status_bad', 'false') //se excleyen cunetas incobrales

            ->Where(function ($query) use ($estudiant_id) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere('cuentaxpagars.estudiant_id', $estudiant_id);
            })

            ->OrderBy('cuentaxpagars.date_calendar_start', 'asc')
            ->OrderBy('cuentaxpagars.name', 'asc');

        $cuentaxpagars = $cuentaxpagars->get();

        return $cuentaxpagars;
    }

    public function getExchangeLatePaymentBillsAttribute()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $estudiant_id = $this->id;
        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->Where('date_late_payment', '<=', Carbon::now())
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.status_exchange', true)
            ->Where('cuentaxpagars.enable_late_payment', 1)
            ->Where('cuentaxpagars.status_bad', 'false') //se excleyen cunetas incobrales

            ->Where(function ($query) use ($estudiant_id) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere('cuentaxpagars.estudiant_id', $estudiant_id);
            })

            ->OrderBy('cuentaxpagars.date_calendar_start', 'asc')
            ->OrderBy('cuentaxpagars.name', 'asc');

        $cuentaxpagars = $cuentaxpagars->get();

        return $cuentaxpagars;
    }

    public function getBadExchangeAmmountExpireBillAttribute()
    {
        $id = $this->id;
        $total = 0;
        $total_pagadas = 0;
        $total_x_pagar = 0;
        $cuentaxpagars = $this->bad_exchange_expire_bills;
        foreach ($cuentaxpagars as $cuentaxpagar) {
            $total_x_pagar = $cuentaxpagar->TotalExchangeMontoCuentasXPagar($this->id); //dd($total_x_pagar);
            $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($this->id); //dd($total_pagadas);
            $total = $total + ($total_x_pagar - $total_pagadas);
        }
        return round($total, 8);
    }

    public function getBadExchangeExpireBillsAttribute()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $estudiant_id = $this->id;
        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->Where('date_expiration', '<=', Carbon::now())
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.status_exchange', true)
            ->Where('cuentaxpagars.status_bad', 'true')

            ->Where(function ($query) use ($estudiant_id) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere('cuentaxpagars.estudiant_id', $estudiant_id);
            })

            ->OrderBy('cuentaxpagars.date_calendar_start', 'asc')
            ->get();

        return $cuentaxpagars;
    }

    public function getExchangeAmmountExpireBillQuota($date_start, $date_end)
    {
        $id = $this->id;
        $total = 0;
        $total_pagadas = 0;
        $total_x_pagar = 0;
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $user_id = $this->id;

        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->where('cuentaxpagars.planpago_id', $planpago_id)
            ->Where(function ($query) use ($user_id) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere('cuentaxpagars.estudiant_id', $user_id);
            })
            ->Where('cuentaxpagars.status_exchange', true);

        $cuentaxpagars = $cuentaxpagars->whereBetween('cuentaxpagars.date_expiration', [$date_start, $date_end]);

        $cuentaxpagars = $cuentaxpagars->get();

        foreach ($cuentaxpagars as $cuentaxpagar) {
            $total_x_pagar = $cuentaxpagar->TotalExchangeMontoCuentasXPagar($user_id);
            $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($user_id);
            $total = $total + ($total_x_pagar - $total_pagadas);
        }

        return $total;
    }

    public function getExchangeAmmountExpireBillDate($date = null, $morosidad = false)
    {
        $id = $this->id;
        $total = 0;
        $total_pagadas = 0;
        $total_x_pagar = 0;
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $user_id = $this->id;

        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->where('cuentaxpagars.planpago_id', $planpago_id)
            ->Where(function ($query) use ($user_id) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere('cuentaxpagars.estudiant_id', $user_id);
            })
            ->Where('cuentaxpagars.status_exchange', true)
            ;

        $cuentaxpagars = ($date) ? $cuentaxpagars->whereDate('cuentaxpagars.date_expiration', '<=', $date) : $cuentaxpagars;

        $cuentaxpagars = ($morosidad) ? $cuentaxpagars->whereNull('quota_original_id') : $cuentaxpagars;

        $cuentaxpagars = $cuentaxpagars->get();

        // if ($user_id == 1575) { dd($cuentaxpagars, $date); }

        foreach ($cuentaxpagars as $cuentaxpagar) {
            $total_x_pagar = $cuentaxpagar->TotalExchangeMontoCuentasXPagar($user_id);
            $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($user_id);
            $total = $total + ($total_x_pagar - $total_pagadas);
        }

        return $total;
    }

    public function getStatusBillDate($date = null)
    {
        // $pendientes = $this->exchange_expire_bill_pendientes; //dd($pendientes);
        $pendientes = $this->getExchangeExpireBillPendientesDecimal(); //dd($pendientes);
        $pendientes = ($date) ? $pendientes->where('date_expiration', '<=', $date) : $pendientes; //dd($pendientes);

        return ($pendientes->isNotEmpty()) ? true : false;
    }

    public function getBillsDate($date)
    {
        $pendientes = collect();
        $datas = collect();
        $monto_total = array();
        $monto_totalBs = array();
        $expire_bills = $this->expire_bills;

        foreach ($expire_bills as $expire_bill) {
            $data = collect();
            if ($date >= $expire_bill->date_expiration) {

                $monto = $expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($this->id);
                $montoBs = $expire_bill->TotalMontoCuentasXPagarAdeudado($this->id);

                if (!array_key_exists($expire_bill->name, $monto_total)) {
                    $monto_total[$expire_bill->name] = 0;
                    $monto_totalBs[$expire_bill->name] = 0;
                }

                $monto_total[$expire_bill->name] += $monto;
                $monto_totalBs[$expire_bill->name] += $montoBs;
                $data->put('expire_bill_name', $expire_bill->name);
                $data->put('ammount', $monto_total[$expire_bill->name]);
                $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                $data->put('date_expiration', $expire_bill->date_expiration);
                $pendientes->put($expire_bill->name, $data);
            }
        }
        return $pendientes;
    }

    public function getExchangeExpireBillPendientesDecimal($decimal = 2)
    {
        $pendientes = collect();
        $datas = collect();
        $monto_total = array();
        $monto_totalBs = array();
        $expire_bills = $this->expire_bills;

        foreach ($expire_bills as $expire_bill) {
            $data = collect();
            $monto = round($expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($this->id), $decimal);
            $montoBs = round($expire_bill->TotalMontoCuentasXPagarAdeudado($this->id), $decimal);
            if ($monto > 0) {
                if (!array_key_exists($expire_bill->name, $monto_total)) {
                    $monto_total[$expire_bill->name] = 0;
                    $monto_totalBs[$expire_bill->name] = 0;
                }
                $monto_total[$expire_bill->name] += $monto;
                $monto_totalBs[$expire_bill->name] += $montoBs;
                $data->put('expire_bill_name', $expire_bill->name);
                $data->put('ammount', $monto_total[$expire_bill->name]);
                $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                $data->put('date_expiration', $expire_bill->date_expiration);
                $pendientes->put($expire_bill->name, $data);
            }
        }
        return $pendientes;
    }

    public function getExchangeExpireBillPendientesAttribute()
    {
        $pendientes = collect();
        $datas = collect();
        $monto_total = array();
        $monto_totalBs = array();
        $expire_bills = $this->expire_bills;

        foreach ($expire_bills as $expire_bill) {
            $data = collect();
            $monto = $expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($this->id);
            $montoBs = $expire_bill->TotalMontoCuentasXPagarAdeudado($this->id);
            if ($monto > 0) {
                if (!array_key_exists($expire_bill->name, $monto_total)) {
                    $monto_total[$expire_bill->name] = 0;
                    $monto_totalBs[$expire_bill->name] = 0;
                }
                $monto_total[$expire_bill->name] += $monto;
                $monto_totalBs[$expire_bill->name] += $montoBs;
                $data->put('expire_bill_name', $expire_bill->name);
                $data->put('ammount', $monto_total[$expire_bill->name]);
                $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                $data->put('date_expiration', $expire_bill->date_expiration);
                $data->put('status_late_payment', $expire_bill->status_late_payment);
                $data->put('enable_late_payment', $expire_bill->enable_late_payment);

                $data->put('date_late_payment', $expire_bill->date_late_payment);
                $data->put('date_calendar_start', $expire_bill->date_calendar_start);
                $data->put('date_calendar_end', $expire_bill->date_calendar_end);
                $pendientes->put($expire_bill->name, $data);
            }
        }
        return $pendientes;
    }

    public function getExchangeExpireBillPendientesAutoPaymentAttribute()
    {
        $pendientes = collect();
        $datas = collect();
        $monto_total = array();
        $monto_totalBs = array();
        $expire_bills = $this->expire_bills;

        foreach ($expire_bills as $expire_bill) {
            $data = collect();
            $monto = $expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($this->id);
            $montoBs = $expire_bill->TotalMontoCuentasXPagarAdeudado($this->id);
            if ($monto > 0) {
                if (!array_key_exists($expire_bill->name, $monto_total)) {
                    $monto_total[$expire_bill->name] = 0;
                    $monto_totalBs[$expire_bill->name] = 0;
                }
                $monto_total[$expire_bill->name] += $monto;
                $monto_totalBs[$expire_bill->name] += $montoBs;
                $data->put('expire_bill_name', $expire_bill->name);
                $data->put('ammount', $monto_total[$expire_bill->name]);
                $data->put('ammountBs', $monto_totalBs[$expire_bill->name]);
                $data->put('date_expiration', $expire_bill->date_expiration);
                $name = $expire_bill->date_expiration . '|' . $expire_bill->name;
                $pendientes->put($name, $data);
            }
        }
        return $pendientes;
    }


    public function getBsExchangeAmmountExpireBillAttribute()
    {
        $exchange_rate_current = ExchangeRate::whereDate('date', Carbon::now())->first();
        $exchange_ammount_expire_bill = $this->exchange_ammount_expire_bill;
        return ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
    }

    public function getExchangeUnexpiredBillsAttribute()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $estudiant_id = $this->id;
        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->Where('date_expiration', '>', Carbon::now())
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.status_exchange', true)
            ->Where('cuentaxpagars.status_bad', 'false')

            ->Where(function ($query) use ($estudiant_id) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere('cuentaxpagars.estudiant_id', $estudiant_id);
            })

            // ->Where('cuentaxpagars.type','GENERAL')
            // ->orWhere('cuentaxpagars.estudiant_id',$this->id)

            // ->OrderBy('cuentaxpagars.date_calendar_start','DESC')
            ->OrderBy('cuentaxpagars.date_calendar_start', 'asc')
            ->get(); //dd('getExchangeUnexpiredBillsAttribute',$cuentaxpagars);
        //if ($this->id == 180) { dd($cuentaxpagars); }
        return $cuentaxpagars;
    }

    public function getExchangeAmmountUnexpiredBillAttribute()
    {
        $id = $this->id;
        $total = 0;
        $total_pagadas = 0;
        $total_x_pagar = 0;
        $cuentaxpagars = $this->exchange_unexpired_bills; //dd($cuentaxpagars);
        foreach ($cuentaxpagars as $cuentaxpagar) {
            $total_x_pagar = $cuentaxpagar->TotalExchangeMontoCuentasXPagar($this->id); //dd($total_x_pagar);
            $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($this->id); //dd($total_pagadas);
            $total = $total + ($total_x_pagar - $total_pagadas);
        }
        return $total;
    }

    public function TotalExchangeMontoCuentasXPagarPagado()
    {
        $total = 0;
        $total_pagadas = 0;
        $cuentaxpagars = $this->getQuotasPayment();
        foreach ($cuentaxpagars as $cuentaxpagar) {
            $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($this->id);
            $total = $total + $total_pagadas;
        }

        return $total;
    }

    public function TotalExchangeMontoCuentaXPagarPagado($cuentaxpagarId)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagarId);
        return $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($this->id);
    }

    public function getExchangeAmmountUnexpiredBillPaidAttribute()
    {
        $total = 0;
        $total_pagadas = 0;
        $cuentaxpagars = $this->exchange_unexpired_bills;
        foreach ($cuentaxpagars as $cuentaxpagar) {
            $total_pagadas = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($this->id);
            $total = $total + $total_pagadas;
        }
        return $total;
    }

    public function getConcetoPagos($date_start = null, $date_end = null)
    {
        $estudiant_id = $this->id;
        $planpago_id = ($this->planpago) ? $this->planpago->id : null;
        
        $concepto_pagos = ConceptoPago::select('concepto_pagos.*', 'cuentaxpagars.date_expiration')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.planpago_id', $planpago_id)
            ->where(function ($query) use ($estudiant_id) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere(function ($q) use ($estudiant_id) {
                        $q->where('cuentaxpagars.type', 'INDIVIDUAL')
                            ->where('cuentaxpagars.estudiant_id', $estudiant_id); // CORRECCIÓN: where() en lugar de whereColumn()
                    });
            })
            ->whereNull('concepto_pagos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at');

        // Aplicar filtros de fecha condicionalmente
        if ($date_start) {
            $concepto_pagos->whereDate('cuentaxpagars.date_calendar_start', '>=', $date_start);
        }
        
        if ($date_end) {
            $concepto_pagos->whereDate('cuentaxpagars.date_calendar_end', '<=', $date_end);
        }

        $concepto_pagos = $concepto_pagos->get();

        // Calcular montos con descuento
        foreach ($concepto_pagos as $concepto_pago) {
            $exchange_amount = $concepto_pago->exchange_ammount;
            
            if ($concepto_pago->status_discount == 'true') {
                $descuento_amount = $this->getDiscountAmmount($concepto_pago->cuentaxpagar_id);
                
                if ($descuento_amount > 0 && $descuento_amount < 100) {
                    $factor = 1 - ($descuento_amount / 100);
                    $exchange_amount = $factor * $exchange_amount;
                    $concepto_pago->exchange_ammount = $exchange_amount;
                }
            }
        }

        return $concepto_pagos;
    }

    public function getTotalExchangeAmmountConcetoPago2($date_start = null, $date_end = null)
    {
        return $this->getConcetoPagos($date_start, $date_end)->sum('exchange_ammount');
    }

    public function getTotalExchangeAmmountConcetoPago($date_start = null, $date_end = null)
    {
        $total = 0;
        $planpago_id = ($this->planpago) ? $this->planpago->id : null;
        $concepto_pagos = DB::table('concepto_pagos')
            ->select('concepto_pagos.*', 'cuentaxpagars.date_expiration')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.planpago_id', $planpago_id)
            ->Where('cuentaxpagars.status_bad', 'false')
            ->whereNull('concepto_pagos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at');

        $concepto_pagos = ($date_start) ? $concepto_pagos->whereDate('cuentaxpagars.date_calendar_start', '>=', $date_start) : $concepto_pagos;
        $concepto_pagos = ($date_end) ? $concepto_pagos->whereDate('cuentaxpagars.date_calendar_end', '<=', $date_end) : $concepto_pagos;

        $concepto_pagos = $concepto_pagos->get();

        foreach ($concepto_pagos as $concepto_pago) {
            $exchange_ammount = $concepto_pago->exchange_ammount;
            if ($concepto_pago->status_discount == 'true') {
                // $descuento_ammount = $this->getDiscountAmmount($concepto_pago->cuentaxpagar_id);
                $descuento_ammount = 0;
                $factor = 1 - ($descuento_ammount / 100);
                $exchange_ammount = $factor * $exchange_ammount;
            }
            $total = $total + $exchange_ammount;
        }

        return $total;
    }
}
