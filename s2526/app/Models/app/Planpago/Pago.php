<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Estudiante\Ingreso;

class Pago extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registro_pago_id',
        'ingreso_id',
        'caf_ids',
        'abono_ids',
        'pagos_ammount',
        'exchange_ammount',
        'exchange_rate_id'
    ];

    public function registro_pago()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPago');
    }
    public function ingreso()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Ingreso');
    }

    public function getPagosCombinadosAttribute()
    {
        $registro_pagos = RegistroPago::select('registro_pagos.*')
        ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
        ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
        ->where('ingresos.id',$this->ingreso_id)
        ->where('registro_pagos.id','<>',$this->registro_pago_id)
        ->get();

        // dd($registro_pagos);
        return $registro_pagos;
    }

    public function getRegistroPagosCombinadoAttribute()
    {
        $registro_pagos = RegistroPago::select('registro_pagos.*')
        ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
        ->where('registro_pagos.id',$this->registro_pago_id)
        ->get(); dd($registro_pagos);

        return $registro_pagos;
    }

    public function getExchangeIngresoAttribute()
    {
        $ingreso = Ingreso::Where('id',$this->ingreso_id)->first(); //dd($ingreso);

        return ($ingreso) ? $ingreso->exchange: null ;
    }

    public function getUpdateExchangeRateAttribute()
    {
        $data = collect();
        $exchange_abono = collect(); $total_ammount_abono = 0; $total_exchange_ammount_abono = 0;
        $total_ammount_credito = 0; $total_exchange_ammount_credito = 0;
        $exchange_ingreso = collect(); $total_ammount_ingreso = 0; $total_exchange_ammount_ingreso = 0;
        $abono = null;
        $credito = null;

        // $registro_pago = $this->registro_pago_combinado;
        $registro_pago = $this->registro_pago;
        if ($registro_pago) {

            /*Abono*/
            $abono_aplicados = $registro_pago->abono_aplicados;
            $total_ammount_abono = 0; $total_exchange_ammount_abono = 0;
            foreach ($abono_aplicados as $abono_aplicado) {
                $abono = $abono_aplicado->all_abono;
                if ($abono) {
                    $exchange_abono = $abono->exchange;
                    if ($exchange_abono) {
                        // $ingreso_ammount = ($exchange_abono) ? $exchange_abono->ingreso_ammount : null ;
                        // $ingreso_ammount = ($exchange_abono) ? $exchange_abono->ingreso_ammount : null ;
                        $total_ammount_abono = $total_ammount_abono + $exchange_abono->ingreso_ammount ;
                        $total_exchange_ammount_abono = $total_exchange_ammount_abono + $exchange_abono->ammount_exchage;
                    }
                }
            }

            /*CAF*/
            $creditoaplicados = $registro_pago->creditoaplicados;
            $total_ammount_credito = 0; $total_exchange_ammount_credito = 0;
            foreach ($creditoaplicados as $creditoaplicado) {
                $credito = $creditoaplicado->all_credito;
                if ($credito) {
                    $total_ammount_credito = $total_ammount_credito + $credito->credito_ammount;
                    $total_exchange_ammount_credito = $total_exchange_ammount_credito + $credito->exchange_ammount;
                }
            }

            /* Ingresos */
            $ingreso = $this->ingreso ;
            $total_exchange_ammount_ingreso = 0; $total_exchange_ammount_ingreso = 0;
            if ($ingreso) {
                $exchange_ingreso = $this->exchange_ingreso;
                $total_exchange_ammount_ingreso = ( $exchange_ingreso) ?  $exchange_ingreso->ammount_exchage : null;
            }

            $total_anb_caf_ammount = $total_ammount_abono + $total_ammount_credito;

            $total_exchange_ammount = $total_exchange_ammount_abono + $total_exchange_ammount_credito;

            $exchange_ingreso_pagado = 0;

            $pagos_ammount = $this->pagos_ammount;
            if ($pagos_ammount > $total_anb_caf_ammount) {
                if ($ingreso) {
                    $ammount = $pagos_ammount - $total_anb_caf_ammount;
                    $exchange = $ingreso->exchange;
                    $ammount_rate = ( $exchange) ?  $exchange->ammount_rate : null;
                    $exchange_ingreso_pagado = ($ammount_rate) ? ($ammount / $ammount_rate) : null ;

                    $total_exchange_ammount = $total_exchange_ammount + $exchange_ingreso_pagado;
                }
            }

            $this->update(['exchange_ammount'=>$total_exchange_ammount]);

            /////////////////////////////////////////////////////////////////////////

            $data->put('pago',$this);
            $data->put('abono_aplicados',$abono_aplicados);
            $data->put('creditoaplicados',$creditoaplicados);
            $data->put('abono',$abono);
            $data->put('credito',$credito);
            $data->put('ingreso',$ingreso);
            $data->put('total_exchange_ammount_abono',$total_exchange_ammount_abono);
            $data->put('total_exchange_ammount_credito',$total_exchange_ammount_credito);
            $data->put('exchange_ingreso',$exchange_ingreso);
            $data->put('total_exchange_ammount_ingreso',$total_exchange_ammount_ingreso);
            $data->put('exchange_ingreso_pagado',$exchange_ingreso_pagado);
            $data->put('total_exchange_ammount',$total_exchange_ammount);

            // if ($this->registro_pago_id==920) {
            //     dd($data);
            // }

            // if ($total_exchange_ammount==0) {
            //     dd($data);
            // }
            // $datas->push($data);

            return $data;
        }

    }

}
