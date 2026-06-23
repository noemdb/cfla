<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DescuentoAplicado extends Model
{
    use SoftDeletes;
    protected $guarded = ['id','deleted_at','created_at','updated_at'];

    public function registropago()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPago');
    }
    public function descuento()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Descuento');
    }
}
