<?php
namespace App\Models\app\Planpago\Functions\Cuentaxpagar;

trait Relations {
        /*INI RELACIONES*/
        public function planpago()
        {
            return $this->belongsTo('App\Models\app\Planpago','planpago_id');
        }
        public function conceptopagos()
        {
            return $this->hasMany('App\Models\app\Planpago\ConceptoPago');
        }
        public function concepto_pagos()
        {
            return $this->hasMany('App\Models\app\Planpago\ConceptoPago');
        }
        public function registropagos()
        {
            return $this->hasMany('App\Models\app\Planpago\RegistroPago');
        }
        public function estudiant()
        {
            return $this->belongsTo('App\Models\app\Estudiant');
        }
        public function concepto_cancelados()
        {
            return $this->hasManyThrough('App\Models\app\Planpago\ConceptoCancelado', 'App\Models\app\Planpago\ConceptoPago');
        }
    /*FIN RELACIONES*/
}
