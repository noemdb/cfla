<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    // 'registro_pago_combinado_id', 'credito_a_favor_id', 'method_pay_id', 'banco_id', 'representant_id', 'number_i_pay', 'date_transaction', 'ammount', 'ammount_exchange', 'observations',

    protected $dates = ['date_transaction','date_payment','deleted_at','created_at','updated_at'];
    protected $fillable = [
        'registro_pago_combinado_id',
        'credito_a_favor_id',
        'method_pay_id',
        'banco_id',
        'representant_id',
        'number_i_pay',
        'date_transaction',
        'ammount',
        'ammount_exchange',
        'observations',
        'status_return',
    ];

    const COLUMN_COMMENTS = [
        'registro_pago_combinado_id' => 'Pago Combinado',
        'credito_a_favor_id' => 'Crédito a favor',
        'method_pay_id' => 'Método de pago',
        'banco_id' => 'Banco',
        'representant_id' => 'Representante',
        'number_i_pay' => 'Número de la transacción',
        'date_transaction' => 'Fecha',
        'ammount' => 'Monto de la devolución',
        'ammount_exchange' => 'Monto Cambiario',
        'observations' => 'Observaciones',
        'status_return' => 'Presente en libro de devoluciones',
    ];

    public function registro_pago_combinado()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPagoCombinado','registro_pago_combinado_id');
    }
    public function credito_a_favor()
    {
        return $this->hasOne('App\Models\app\Estudiante\CreditoAFavor','credito_a_favor_id');
    }
    public function metodo_pago()
    {
        return $this->belongsTo('App\Models\app\Planpago\MetodoPago','method_pay_id');
    }
    public function banco()
    {
        return $this->belongsTo('App\Models\app\Institucion\Banco','banco_id');
    }
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant','representant_id');
    }
}

/*

registro_pago_combinado_id
credito_a_favor_id
method_pay_id
banco_id
representant_id

*/
