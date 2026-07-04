<?php
namespace App\Models\app\Estudiante\Functions\RegistroPagoCombinado;

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
    // public function ingresos()
    // {
    //     return $this->hasManyThrough('App\Models\app\Estudiante\Ingreso', 'App\Models\app\Planpago\RegistroPago');
    // }
}
