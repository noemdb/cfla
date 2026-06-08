<?php
namespace App\Models\app\Planpago\Functions\RegistroPagoCombinado;

trait Relations {
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }
    public function registropagos()
    {
        return $this->hasMany('App\Models\app\Planpago\RegistroPago');
    }
    public function recursos()
    {
        return $this->hasMany('App\Models\app\Planpago\Recurso');
    }

    public function refunds()
    {
        return $this->hasMany('App\Models\app\Planpago\Refund');
    }
    // public function ingresos()
    // {
    //     return $this->hasManyThrough('App\Models\app\Estudiante\Ingreso', 'App\Models\app\Planpago\RegistroPago');
    // }

    public function ingresos()
    {
        return $this->hasManyThrough(
            'App\Models\app\Estudiante\Ingreso',
            'App\Models\app\Planpago\Pago',
            'registro_pago_id', // Foreign key en tabla pagos
            'id', // Foreign key en tabla ingresos
            'id', // Local key en registro_pago_combinados
            'ingreso_id' // Local key en pagos
        )->through('App\Models\app\Planpago\RegistroPago');
    }

    /**
     * Relación alternativa más directa para ingresos
     */
    public function ingresosDirectos()
    {
        return $this->hasManyThrough(
            'App\Models\app\Estudiante\Ingreso',
            'App\Models\app\Planpago\RegistroPago',
            'registro_pago_combinado_id',
            'id',
            'id',
            'id'
        )->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
        ->join('ingresos', 'pagos.ingreso_id', '=', 'ingresos.id')
        ->select('ingresos.*');
    }


}
