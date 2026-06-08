<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\Estudiant;
use App\Models\app\Planpago\Functions\ConceptoPago\Lists;
use App\Models\app\Planpago\Functions\ConceptoPago\Exchanges;

class ConceptoPago extends Model
{
    use SoftDeletes;
    use Lists;
    use Exchanges;

    // protected $guarded = ['id','deleted_at','created_at','updated_at'];
    protected $fillable = [
        'cuentaxpagar_id','quota_id','nom_concepto_pago_id','concepto_description','concepto_observations',
        'concepto_ammount','exchange_ammount','status_modifiable','status_discount','status_active'
    ];

    const COLUMN_COMMENTS = [
        'cuentaxpagar_id' => 'Nombre del concepto',
        'quota_id' => 'Cuota de morosidad',
        'nom_concepto_pago_id' => 'Nombre de la cuenta',
        'concepto_description'=>'Descripción',
        'concepto_observations'=>'Observaciones',
        'concepto_ammount'=>'Monto Moneda local',
        'exchange_ammount'=>'Monto Cambiario (Divisas)',
        'status_modifiable'=>'Permite manipular el costo al facturar',
        'status_discount'=>'Permite aplicación de descuentos - planes benéficos',
        'status_active'=>'Estado del Concepto'
    ];

    public function cuentaxpagar()
    {
        return $this->belongsTo('App\Models\app\Planpago\Cuentaxpagar','cuentaxpagar_id');
    }
    public function nomconceptopago()
    {
        return $this->belongsTo('App\Models\app\Planpago\NomConceptoPago','nom_concepto_pago_id');
    }
    public function getFullActiveAttribute()
    {
        return $this->where('status_active','true');

    }
    public function conceptocancelados()
    {
        return $this->hasMany('App\Models\app\Planpago\ConceptoCancelado');
    }


    /*--------------------------------------------------------------------------*/

    public static function list_type($type='GENERAL')
    {
        $concepto_pagos = ConceptoPago::select('concepto_pagos.*')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.type',$type)
            ->wherenull('cuentaxpagars.deleted_at')
            ->get()
            ;
        return $concepto_pagos;
    }

    public function getStatusEditAttribute()
    {
        return ( empty($this->conceptocancelados->count())) ? true : false ;
    }

    public function getStatusDeleteAttribute()
    {
        return ( empty($this->conceptocancelados->count())) ? true : false ;
    }

    public function AmmountParcial($estudiant_id)
    {
        // dd($this);
        $concepto_pagos =
            Cuentaxpagar::select('concepto_pagos.status_discount as status_discount',
            'concepto_pagos.concepto_ammount as concepto_ammount','nom_concepto_pagos.name as concepto_name',
            'concepto_pagos.id as concepto_pago_id', 'concepto_cancelados.id as concepto_cancelados_id ',
            'concepto_cancelados.status_paid as concepto_cancelados_status_paid',
            'concepto_cancelados.concepto_ammount AS ammount_pago_parcial',
            'concepto_cancelados.exchange_ammount AS exchange_ammount_parcial'
            )
                ->join('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                ->join('concepto_cancelados', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
                ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
                ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
                ->where('cuentaxpagars.id',$this->cuentaxpagar_id)
                ->where('concepto_pagos.id',$this->id)
                ->where('registro_pagos.estudiant_id',$estudiant_id)
                ->wherenull('registro_pagos.deleted_at')
                // ->where('concepto_cancelados.status_partial','true')
                ->where('concepto_cancelados.status_paid','false')
                ->wherenull('concepto_cancelados.deleted_at')
                ->wherenull('concepto_pagos.deleted_at')
                ->orderby('concepto_pagos.id','asc')
                ->get();

        // dd('concepto_pagos',$concepto_pagos->toarray());

        return $concepto_pagos;
    }

    public function getTotalPagado($estudiant_id)
    {
        $concepto_cancelados = ConceptoCancelado::select('concepto_cancelados.id','concepto_cancelados.concepto_ammount as ammount','registro_pagos.id as registro_pago_id')
            ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')

            ->where('concepto_pagos.id',$this->id)
            ->where('cuentaxpagars.id',$this->cuentaxpagar_id)
            ->where('registro_pagos.estudiant_id',$estudiant_id)

            ->wherenull('concepto_cancelados.deleted_at')
            ->wherenull('concepto_pagos.deleted_at')
            ->wherenull('cuentaxpagars.deleted_at')
            ->wherenull('registro_pagos.deleted_at')

            ->get();

            $total = null;

            foreach ($concepto_cancelados as $concepto_cancelado) {
                $descuento_aplicados = DescuentoAplicado::where('registro_pago_id',$concepto_cancelado->registro_pago_id)->get();
                $ammount = $concepto_cancelado->ammount;
                foreach ($descuento_aplicados as $descuento_aplicado) {
                    $descuento_ammount = ($descuento_aplicado->descuento) ? $descuento_aplicado->descuento->descuento_ammount : null ;
                    $factor_descuento = 1 - ( $descuento_ammount / 100) ;
                    $ammount = $factor_descuento * $ammount;
                }
                $total = $total + $ammount;
            }

            return $total;
            // return (!empty($concepto_cancelados)) ? $concepto_cancelados->sum('ammount'):0;
    }

    public function getTotalXPagar($estudiant_id)
    {
        return $this->MontoConceptoDescuento($estudiant_id) - $this->getTotalPagado($estudiant_id);
    }

    public function getPagadoAttribute()
    {
        $concepto_pagos =
            ConceptoPago::select('concepto_pagos.*')
                ->join('concepto_cancelados', 'concepto_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
                // ->join('registro_pagos', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
                // ->where('registro_pagos.estudiant_id',$estudiant_id)
                ->where('concepto_pagos.id',$this->id)
                ->wherenull('concepto_cancelados.deleted_at')
                ->wherenull('concepto_pagos.deleted_at')
                ->first();
        return (empty($concepto_pagos)) ? false:true;
    }

    public function MontoConceptoDescuento ($estudiant_id)
    {
        $concepto = ConceptoPago::findorfail($this->id);
        $cuentaxpagar = $concepto->cuentaxpagar;
        $estudiant = Estudiant::findorfail($estudiant_id);

        $ammount = $concepto->concepto_ammount;
        // $descuento =  1 - ($estudiant->descuento_ammount($cuentaxpagar->id) / 100);

        if ($concepto->status_discount=="true") {

            $descuento =  $estudiant->descuento($cuentaxpagar->id); //dd($descuento);
            $descuento_ammount = ($descuento) ? $descuento->descuento_ammount : null ; //dd($ammount,$descuento_ammount);

            $factor_descuento = 1 - ( $descuento_ammount / 100) ; //dd($factor_descuento);

            $ammount = round(($ammount * $factor_descuento),2); //dd($ammount);
        }
        return $ammount;
    }
    public static function conceptos_pagados($cuentaxpagar_id,$estudiant_id)
    {
        $concepto_pagos =

            ConceptoPago::select('nom_concepto_pagos.name as concepto_name', 'concepto_pagos.id as concepto_pago_id', 'concepto_cancelados.id as concepto_cancelados_id ', 'concepto_pagos.concepto_ammount as concepto_ammount')
                ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
                ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
                ->leftJoin('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                ->leftJoin('concepto_cancelados', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
                ->whereNotNull('concepto_cancelados.concepto_pago_id')
                ->where('cuentaxpagars.id',$cuentaxpagar_id)
                ->where('registro_pagos.estudiant_id',$estudiant_id)
                ->orderby('concepto_pagos.id','asc')
                ->get();

        return $concepto_pagos;
    }

    public static function conceptos_x_pagar($cuentaxpagar_id,$estudiant_id)
    {
        $arr_id = ConceptoPago::conceptos_pagados($cuentaxpagar_id,$estudiant_id)->pluck('concepto_pago_id');

        $conceptos_x_pagar =

            ConceptoPago::select('nom_concepto_pagos.name as concepto_name', 'concepto_pagos.id as concepto_pago_id', 'concepto_pagos.concepto_ammount as concepto_ammount')
                ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
                ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
                ->whereNotIn('concepto_pagos.id',$arr_id)
                ->where('cuentaxpagars.id',$cuentaxpagar_id)
                ->orderby('concepto_pagos.concepto_ammount','asc')
                ->get();

        return $conceptos_x_pagar;

    }
    public static function sum_conceptos_x_pagar($cuentaxpagar_id,$estudiant_id)
    {
        $arr_id = ConceptoPago::conceptos_pagados($cuentaxpagar_id,$estudiant_id)->pluck('concepto_pago_id');

        $sum =

            ConceptoPago::select('nom_concepto_pagos.name as concepto_name', 'concepto_pagos.id as concepto_pago_id', 'concepto_pagos.concepto_ammount as concepto_ammount')
                ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
                ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
                ->whereNotIn('concepto_pagos.id',$arr_id)
                ->where('cuentaxpagars.id',$cuentaxpagar_id)
                ->orderby('concepto_pagos.id','asc')
                ->sum('concepto_pagos.concepto_ammount');

        return $sum;

    }

}
