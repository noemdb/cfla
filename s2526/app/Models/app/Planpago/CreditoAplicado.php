<?php

namespace App\Models\app\Planpago;

use App\Models\app\Estudiante\CreditoAFavor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditoAplicado extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registro_pago_id',
        'credito_a_favor_id',
        'credito_aplicado_observations',
        'ammount_applied',
        'exchange_ammount_applied',
    ];

    protected $casts = [
        'ammount_applied' => 'decimal:2',
        'exchange_ammount_applied' => 'decimal:2',
    ];

    public function registropago()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPago','registro_pago_id');
    }
    
    public function credito_a_favor()
    {
        return $this->belongsTo('App\Models\app\Estudiante\CreditoAFavor','credito_a_favor_id ');
    }

    public function getCreditoTrashAttribute()
    {
        return CreditoAFavor::withTrashed()->where('id',$this->credito_a_favor_id)->first();
    }
    public function getAllCreditoAttribute()
    {
        return CreditoAFavor::withTrashed()->where('id',$this->credito_a_favor_id)->first();
    }
}
