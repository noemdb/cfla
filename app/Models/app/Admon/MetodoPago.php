<?php

namespace App\Models\app\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;
    protected $guarded = ['id','deleted_at','created_at','updated_at'];

    public function ingresos()
    {
        return $this->hasMany('App\Models\app\Estudiante\Ingreso');
    }
    public function abono()
    {
        return $this->hasMany('App\Models\app\Estudiante\Abono');
    }
    // public function pagos()
    // {
    //     return $this->hasMany('App\Models\app\Planpago\Pago');
    // }

    public function scopePublic($query, $flag=true)
    {
        return $query->where('metodo_pagos.is_public', $flag);
    }

    public function createAcronym($onlyCapitals = false) {
        $string = $this->name;
        $output = null;
        $token = strtok($string, ' ');
        while ($token !== false) { $character = mb_substr($token, 0, 1);
            if ($onlyCapitals and mb_strtoupper($character) !== $character) {
                $token = strtok(' '); continue;
            }
            $output .= $character; $token = strtok(' ');
        }
        return $output;
    }

    public static function method_pay_list() /* usada para llenar los objetos de formularios select*/
    {
        $method_pay_list = MetodoPago::public()->pluck('name','id')->toArray();;

        return $method_pay_list;
    }

    public static function list_metodo_pago() /* usada para llenar los objetos de formularios select*/
    {
        $list_metodo_pago = MetodoPago::where('status_active',true)->where('is_intern','true')->orderby('name','asc')->pluck('name', 'id');

        return $list_metodo_pago;
    }

}
