<?php

namespace App\Models\app\Planpago;

use App\Models\app\Estudiante\CreditoAFavor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recurso extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registro_pago_combinado_id','ingreso_id','credito_a_favor_id','status_ingreso','status_abono','status_credito'
    ];

    public function registro_pago_combinado()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPagoCombinado');
    }
    public function ingreso()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Ingreso');
    }
    public function credito_a_favor()
    {
        return $this->belongsTo('App\Models\app\Estudiante\CreditoAFavor');
    }

    public function getAllCreditoAFavorAttribute ()
    {
        $credito_generado = CreditoAFavor::withTrashed()->where('id', $this->credito_a_favor_id)->first();

        return $credito_generado;
    }
}
