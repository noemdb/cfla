<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;

//helpers
use Illuminate\Support\Facades\DB;

//trait
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\app\Planpago\Functions\RegistroPagoCombinado\FixDB;
use App\Models\app\Planpago\Functions\RegistroPagoCombinado\Relations;
use App\Models\app\Planpago\Functions\RegistroPagoCombinado\Functions;
use App\Models\app\Planpago\Functions\RegistroPagoCombinado\Exchanges;

//model's
use App\Models\app\Estudiant;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Institucion;

class RegistroPagoCombinado extends Model
{
    use Relations;
    use FixDB;
    use Functions;
    use Exchanges;
    use SoftDeletes;

    protected $fillable = ['representant_id', 'description', 'created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];


    public function getCorrelativeAttribute()
    {
        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $corelative = $this->id + $institucion->last_number_bill_config;
        return $corelative;
    }

    public function getAbonosAplicadosAttribute()
    {
        $registro_pagos = RegistroPago::select(
            'registro_pagos.*',
            'abonos.abono_description',
            'ingresos.ingreso_observations',
            'ingresos.id as ingreso_id',
            'ingresos.number_i_pay',
            'ingresos.ingreso_ammount',
            'ingresos.exchange_ammount',
            'ingresos.exchange_ammount as ingresos_exchange_ammount',
            'ingresos.date_transaction',
            'ingresos.date_payment',
            'bancos.name as banco_name',
            'exchange_rates.ammount as exchange_rates_ammount'
        )
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('abono_aplicados', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
            ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->leftjoin('exchange_rates', 'exchange_rates.id', '=', 'ingresos.exchange_rate_id')
            ->Where('registro_pago_combinados.id', $this->id)
            ->wherenull('registro_pago_combinados.deleted_at')
            ->get();
        return $registro_pagos;
    }

    public function getAmmountAbonosAplicadosAttribute()
    {
        return (!empty($this->abonos_aplicados)) ? $this->abonos_aplicados->sum('ingreso_ammount') : 0;
    }

    public function getAmmountAbonosAplicadosExchangeAttribute()
    {
        return (!empty($this->abonos_aplicados)) ? $this->abonos_aplicados->sum('ingresos_exchange_ammount') : 0;
    }

    public function getCreditosAplicadosAttribute()
    {

        $reg_pag_ids = DB::table('registro_pagos')
            ->select('registro_pagos.*')
            ->Where('registro_pago_combinado_id', $this->id)
            ->wherenull('registro_pagos.deleted_at')
            ->pluck('id')
            ->toArray();

        $caf_ids = DB::table('credito_a_favors')
            ->select('credito_a_favors.*')
            ->WhereIn('registro_pago_id', $reg_pag_ids)
            ->pluck('id')
            ->toArray();

        $credito_a_favors = DB::table('credito_a_favors')
            ->select(
                'credito_a_favors.*',
                'bancos.name as banco_name',
                'ingresos.number_i_pay',
                'ingresos.ingreso_ammount',
                'ingresos.date_transaction'
            )
            ->join('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
            ->leftjoin('ingresos', 'ingresos.id', '=', 'credito_a_favors.ingreso_id')
            ->leftjoin('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->WhereIn('credito_aplicados.registro_pago_id', $reg_pag_ids)
            ->WhereNotIn('credito_a_favors.id', $caf_ids)
            ->get();
        return $credito_a_favors;
    }
    public function getAmmountCreditosAplicadosAttribute()
    {
        $ammont = $this->creditos_aplicados->sum('credito_ammount');
        return $ammont;
    }
    public function getAmmountCreditosAplicadosExchangeAttribute()
    {
        $ammont = $this->creditos_aplicados->sum('exchange_ammount');
        return $ammont;
    }

    public function getCreditosGeneradosAttribute()
    {
        $reg_pag_ids = DB::table('registro_pagos')
            ->select('registro_pagos.*')
            ->Where('registro_pago_combinado_id', $this->id)
            ->wherenull('registro_pagos.deleted_at')
            ->pluck('id')
            ->toArray();
        $ca_ids = DB::table('credito_aplicados')
            ->select('credito_aplicados.*')
            ->WhereIn('registro_pago_id', $reg_pag_ids)
            ->pluck('credito_a_favor_id')
            ->toArray();

        $credito_a_favors = DB::table('credito_a_favors')
            ->select('credito_a_favors.*')
            ->WhereIn('registro_pago_id', $reg_pag_ids)
            ->WhereNotIn('id', $ca_ids)
            ->get();

        return $credito_a_favors;
    }

    public function getCreditoAFavorDisponiblesAttribute()
    {
        $cafs = CreditoAFavor::select('credito_a_favors.*')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->where('registro_pago_combinados.id', $this->id)
            ->where('credito_a_favors.status_omitted', 'false')
            ->whereNull('registro_pago_combinados.deleted_at')
            ->whereNull('registro_pagos.deleted_at')
            ->get();
        return $cafs;
    }

    public function getAmmountCreditosGeneradosExchangeAttribute ()
    {
        return $this->creditos_generados->sum('exchange_ammount');
    }

    public function getAmmountCreditosGeneradosAttribute ()
    {
        return $this->creditos_generados->sum('credito_ammount');
    }

    public function getPagosAttribute()
    {
        $pagos = Pago::select('registro_pagos.*', 'pagos.pagos_ammount as pagos_ammount', 'pagos.exchange_ammount as exchange_ammount')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->Where('registro_pago_combinados.id', $this->id)
            ->wherenull('registro_pago_combinados.deleted_at')
            ->wherenull('pagos.deleted_at')
            ->get();
        return $pagos;
    }
    public function getAmmountPagadoAttribute()
    {
        return $this->pagos->sum('pagos_ammount');
    }
    public function getAmmountPagadoExchangeAttribute()
    {
        return $this->pagos->sum('exchange_ammount');
    }

    public function getIngresosAttribute()
    {
        $ingresos = Ingreso::select('ingresos.*', 'bancos.name as banco_name', 'exchange_rates.ammount as exchange_rates_ammount')
            ->join('pagos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->leftjoin('exchange_rates', 'exchange_rates.id', '=', 'ingresos.exchange_rate_id')
            ->Where('registro_pago_combinados.id', $this->id)
            ->wherenull('registro_pago_combinados.deleted_at')
            ->wherenull('pagos.deleted_at')
            ->wherenull('ingresos.deleted_at')
            ->GroupBy('ingresos.number_i_pay')
            ->get();
        return $ingresos;
    }

    public function getAmmountIngresosAttribute()
    {
        return (!empty($this->ingresos)) ? $this->ingresos->sum('ingreso_ammount') : 0;
    }
    public function getAmmountIngresosExchangeAttribute()
    {
        return (!empty($this->ingresos)) ? $this->ingresos->sum('exchange_ammount') : 0;
    }

    /********************with_trahs************************/
    public function getAllPagosAttribute()
    {
        $pagos = Pago::withTrashed()
            ->select('registro_pagos.*', 'pagos.pagos_ammount as pagos_ammount')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->Where('registro_pago_combinados.id', $this->id)
            ->get();
        return $pagos;
    }
    public function getAllAmmountPagadoAttribute()
    {
        return $this->pagos->sum('pagos_ammount');
    }

    /******************* fix db********* */
    public static function fix_registro_pago_combinados() /* usada para restaurar registro de pagos combinados mal anulados*/
    {
        $fix_registro_pago_combinados =
            RegistroPagoCombinado::onlyTrashed()
            ->select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->whereNull('registro_pagos.deleted_at')
            ->get();

        foreach ($fix_registro_pago_combinados as $registro_pago_combinado) {
            $registro_pago_combinado->restore();
        }

        dd($fix_registro_pago_combinados);
    }

    public function getUpdateExchangeRateAttribute()
    {
        $data = collect();
        $datas = collect();

        $total_recursos_exchange_ammount = null;
        $total_ingreso_exchange_ammount = null;
        $total_credito_exchange_ammount = null;
        $total_credito_generado_exchange_ammount = null;
        $total_ammount_pagado = $this->ammount_pagado;

        $registropagos = $this->registropagos; //dd($registropagos);

        if ($registropagos->isNotEmpty()) {

            //recursos
            $recursos = $this->recursos;
            foreach ($recursos as $recurso) {
                $ingreso = $recurso->ingreso;
                if ($ingreso) {
                    $total_ingreso_exchange_ammount += $ingreso->exchange_ammount;
                }

                $credito = $recurso->all_credito_a_favor;
                if ($credito) {
                    $total_credito_exchange_ammount += $credito->exchange_ammount;
                }
            }
            $total_recursos_exchange_ammount = $total_ingreso_exchange_ammount + $total_credito_exchange_ammount;

            //credito generado
            $creditos_generados = $this->creditos_generados;
            foreach ($creditos_generados as $credito) {
                $total_credito_generado_exchange_ammount += $credito->exchange_ammount;
            }
            $total_recursos_exchange_ammount = $total_recursos_exchange_ammount - $total_credito_generado_exchange_ammount;

            foreach ($registropagos as $registropago) {
                $estudiant = $registropago->estudiant;
                $cuentaxpagar = $registropago->cuentaxpagar;
                if ($cuentaxpagar->status_exchange) {
                    $pago = $registropago->pago;
                    if ($pago) {
                        $pagos_ammount = $pago->pagos_ammount;
                        $factor_parcial_pagado = $pagos_ammount / $total_ammount_pagado;
                        $parcial_pagado = $factor_parcial_pagado * $total_recursos_exchange_ammount;

                        $pago->update(['exchange_ammount' => $parcial_pagado]); //dd($pago,$recursos);

                    }
                }
            }
        }
    }

    public function getFixCreditoGeneradoExchangeAttribute()
    {
        $data = collect();
        $datas = collect();

        $total_recursos_exchange_ammount = null;
        $total_ingreso_exchange_ammount = null;
        $total_credito_exchange_ammount = null;
        $total_credito_generado_exchange_ammount = null;
        $total_ammount_pagado = $this->ammount_pagado;

        $registropagos = $this->registropagos; //dd($registropagos);

        if ($registropagos->isNotEmpty()) {

            //recursos
            $recursos = $this->recursos;
            foreach ($recursos as $recurso) {
                $ingreso = $recurso->ingreso;
                if ($ingreso) {
                    $total_ingreso_exchange_ammount += $ingreso->exchange_ammount;
                }

                $credito = $recurso->all_credito_a_favor;
                if ($credito) {
                    $total_credito_exchange_ammount += $credito->exchange_ammount;
                }
            }
            $total_recursos_exchange_ammount = $total_ingreso_exchange_ammount + $total_credito_exchange_ammount;

            //credito generado
            $creditos_generados = $this->creditos_generados;
            foreach ($creditos_generados as $credito) {
                $total_credito_generado_exchange_ammount += $credito->exchange_ammount;
            }
            //$total_recursos_exchange_ammount = $total_recursos_exchange_ammount - $total_credito_generado_exchange_ammount;

            foreach ($registropagos as $registropago) {
                $estudiant = $registropago->estudiant;
                $cuentaxpagar = $registropago->cuentaxpagar;
                if ($cuentaxpagar->status_exchange) {
                    $pago = $registropago->pago;
                    if ($pago) {

                        $total_exchange_monto_cuentas_x_paagar_adeudado = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);

                        if ($total_recursos_exchange_ammount >= $total_exchange_monto_cuentas_x_paagar_adeudado) {
                            $total_recursos_exchange_ammount = $total_recursos_exchange_ammount - $total_credito_generado_exchange_ammount;
                        } else {
                            $creditos_generados = $this->creditos_generados;
                            foreach ($creditos_generados as $credito) {
                                $creditos_aplicado = CreditoAplicado::where('credito_a_favor_id', $credito->id);
                                $creditos_aplicado->forceDelete();
                                $credito->forceDelete();
                            }
                        }

                        $pagos_ammount = $pago->pagos_ammount;
                        $factor_parcial_pagado = $pagos_ammount / $total_ammount_pagado;

                        $parcial_pagado = $factor_parcial_pagado * $total_recursos_exchange_ammount;

                        $pago->update(['exchange_ammount' => $parcial_pagado]); //dd($pago,$recursos);

                        $total_recursos_exchange_ammount = $total_recursos_exchange_ammount - $parcial_pagado;
                    }
                }
            }
        }
    }
}
