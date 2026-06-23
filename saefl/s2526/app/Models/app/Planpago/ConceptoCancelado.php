<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ConceptoCancelado extends Model
{
    use SoftDeletes;
    // protected $guarded = ['id','deleted_at','created_at','updated_at'];
    protected $fillable = ['id','ingreso_id','ingreso','registro_pago_id','concepto_pago_id','concepto_ammount','exchange_ammount','concepto_pago_observations','status_paid','status_partial'];

    public function registropago()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPago');
    }
    public function concepto_pago()
    {
        return $this->belongsTo('App\Models\app\Planpago\ConceptoPago');
    }

    public static function list_type($type='GENERAL')
    {
        $list = DB::table('concepto_cancelados')
            ->select(
                'concepto_cancelados.concepto_pago_id','concepto_cancelados.concepto_ammount',
                'nom_concepto_pagos.name as concepto_pago_name',
                'cuentaxpagars.id as cuentaxpagar_id','cuentaxpagars.name as cuentaxpagar_name','cuentaxpagars.date_expiration'
            )
            ->selectRaw('count(concepto_cancelados.id) as count_concepto_cancelados')
            ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
            ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')

            ->where('concepto_cancelados.status_paid','true')
            ->where('cuentaxpagars.type',$type)

            ->wherenull('concepto_cancelados.deleted_at')
            ->wherenull('concepto_pagos.deleted_at')
            ->wherenull('cuentaxpagars.deleted_at')

            ->groupby('cuentaxpagars.name')
            ->orderBy('cuentaxpagars.date_expiration')
            ->orderBy('cuentaxpagars.id','desc')
            ;
        return $list;
    }

    public function getUpdateExchangeRateAttribute()
    {
        $data = collect();
        $exchange_abono = collect(); $total_ammount_abono = 0; $total_exchange_ammount_abono = 0;
        $total_ammount_credito = 0; $total_exchange_ammount_credito = 0;
        $exchange_ingreso = collect(); $total_ammount_ingreso = 0; $total_exchange_ammount_ingreso = 0;

        $registro_pago = $this->registro_pago;
        if ($registro_pago) {

            /*Abono*/
            $abono_aplicados = $registro_pago->abono_aplicados;
            $total_ammount_abono = 0; $total_exchange_ammount_abono = 0;
            foreach ($abono_aplicados as $abono_aplicado) {
                $abono = $abono_aplicado->all_abono;
                if ($abono) {
                    $exchange_abono = $abono->exchange;
                    $total_ammount_abono = $total_ammount_abono + $exchange_abono->ingreso_ammount;
                    $total_exchange_ammount_abono = $total_exchange_ammount_abono + $exchange_abono->ammount_exchage;
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

            $concepto_ammount = $this->concepto_ammount;
            if ($concepto_ammount > $total_anb_caf_ammount) {
                if ($ingreso) {
                    $ammount = $concepto_ammount - $total_anb_caf_ammount;
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
            $data->put('ingreso',$ingreso);
            $data->put('total_exchange_ammount_abono',$total_exchange_ammount_abono);
            $data->put('total_exchange_ammount_credito',$total_exchange_ammount_credito);
            $data->put('exchange_ingreso',$exchange_ingreso);
            $data->put('total_exchange_ammount_ingreso',$total_exchange_ammount_ingreso);
            $data->put('exchange_ingreso_pagado',$exchange_ingreso_pagado);
            $data->put('total_exchange_ammount',$total_exchange_ammount);
            // $datas->push($data);

            return $data;
        }
    }
}
