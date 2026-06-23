<?php
namespace App\Models\app\Estudiante\Functions\Representants;

trait Relations {
    /*INI relaciones entre modelos*/

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function creditoafavors()
    {
        return $this->hasMany('App\Models\app\Estudiante\CreditoAFavor');
    }
    public function abono()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Abono');
    }
    public function ingresos()
    {
        return $this->hasMany('App\Models\app\Estudiante\Ingreso');
    }
    public function estudiants()
    {
        return $this->hasMany('App\Models\app\Estudiant');
    }
    public function registropagos()
    {
        return $this->hasMany('App\Models\app\Planpago\RegistroPago');
    }
    public function registro_pago_combinados()
    {
        return $this->hasMany('App\Models\app\Planpago\RegistroPagoCombinado');
    }
    public function prepagos()
    {
        return $this->hasMany('App\Models\app\Planpago\Prepago');
    }

}
