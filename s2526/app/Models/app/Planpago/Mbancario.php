<?php

namespace App\Models\app\Planpago;

use App\Models\app\Estudiante\Ingreso;
use Illuminate\Database\Eloquent\Model;

class Mbancario extends Model
{
    protected $fillable = [ 'banco_id','number_i_pay','date_transaction','ingreso_ammount' ];
    protected $dates = ['created_at','updated_at','date_transaction'];

    const COLUMN_COMMENTS = [
        'banco_id' => 'Banco',
        'number_i_pay' => 'Número de la transacción',
        'date_transaction' => 'Fecha de la transacción',
        'ingreso_ammount' => 'Monto del Ingreso',

    ];

    public function banco()
    {
        return $this->belongsTo('App\Models\app\Institucion\Banco');
    }

    public function getStatusApplyAttribute()
    {
        $ingreso = Ingreso::Where('number_i_pay',$this->number_i_pay)->first();
        $status = ($ingreso) ? true:false ;
        return $status ;
    }

    public function getStatusAssociatedAttribute()
    {
        $ingreso = Prepago::Where('number_i_pay',$this->number_i_pay)->first();
        $status = ($ingreso) ? true:false ;
        return $status ;
    }

}
