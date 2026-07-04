<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $fillable = [  'representant_id',  'user_id'  ];

    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'representant_id'=>'Representante',
        'user_id'=>'Usuario'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }
    public function recibo_cashes()
    {
        return $this->hasMany('App\Models\app\Planpago\ReciboCash');
    }
    public function recibo_changes()
    {
        return $this->hasMany('App\Models\app\Planpago\ReciboChange');
    }
    public function recibo_pagos()
    {
        return $this->hasMany('App\Models\app\Planpago\ReciboPago');
    }

    public function getCashSerialsAttribute()
    {
        $recibo_cashs = $this->recibo_cashes;
        $serials = null;
        foreach ($recibo_cashs as $recibo_cash) {
            $serials .= $recibo_cash->serial.', ';
        }
        return $serials;
    }

    public function getChangeSerialsAttribute()
    {
        $recibo_changes = $this->recibo_changes;
        $serials = null;
        foreach ($recibo_changes as $recibo_change) {
            $serials .= $recibo_change->serial.', ';
        }
        return $serials;
    }

    public function getPagoQuotasAttribute()
    {
        $recibo_pagos = $this->recibo_pagos;
        $quotas = null;
        foreach ($recibo_pagos as $recibo_pago) {
            $quotas .= $recibo_pago->quota.', ';
        }
        return $quotas;
    }

    public function getAmmountCashesAttribute ()
    {
        return $this->recibo_cashes->sum('exchange_ammount');
    }
    public function getAmmountChangesAttribute ()
    {
        return $this->recibo_changes->sum('exchange_ammount');
    }
    public function getAmmountPagosAttribute ()
    {
        return $this->recibo_pagos->sum('exchange_ammount');
    }

}
