<?php
namespace App\Models\app\Planpago\Functions\RegistroPago;

trait Relations {
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }
    public function cuentaxpagar()
    {
        return $this->belongsTo('App\Models\app\Planpago\Cuentaxpagar');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function approval()
    {
        return $this->belongsTo('App\User','approval_user_id');
    }

    public function cancellation()
    {
        return $this->belongsTo('App\User','cancellation_user_id');
    }

    public function registro_pago_combinado()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPagoCombinado','registro_pago_combinado_id');
    }
    public function conceptocancelados()
    {
        return $this->hasMany('App\Models\app\Planpago\ConceptoCancelado');
    }
    public function descuentoaplicados()
    {
        return $this->hasMany('App\Models\app\Planpago\DescuentoAplicado');
    }
    public function creditoaplicados()
    {
        return $this->hasMany('App\Models\app\Planpago\CreditoAplicado');
    }

    public function creditos_aplicados()
    {
        return $this->hasMany('App\Models\app\Planpago\CreditoAplicado');
    }

    public function abono_aplicados()
    {
        return $this->hasMany('App\Models\app\Planpago\AbonoAplicado');
    }

    public function abonos_aplicados()
    {
        return $this->hasMany('App\Models\app\Planpago\AbonoAplicado');
    }

    public function pagos()
    {
        return $this->hasMany('App\Models\app\Planpago\Pago');
    }
    public function getpagoAttribute()
    {
        return (!empty($this->pagos)) ? $this->pagos->first() : null ;
    }
    public function ingresos()
    {
        return $this->hasMany('App\Models\app\Estudiante\Ingreso');
    }
    public function creditoafavor()
    {
        return $this->hasOne('App\Models\app\Estudiante\CreditoAFavor');
    }
}
