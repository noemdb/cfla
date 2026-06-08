<?php

namespace App\Models\app\Planpago;

use App\Models\app\Estudiante\Ingreso;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prepago extends Model
{
    protected $fillable = [
        'representant_id','method_pay_id','banco_id','number_i_pay','date_transaction','ingreso_ammount'
        ,'ingreso_observations','person_bill_ci','person_bill_name','comment','status_approved','status_apply'
    ];
    protected $dates = ['created_at','updated_at','date_transaction'];

    const COLUMN_COMMENTS = [
        'representant_id' => 'Representante',
        'method_pay_id' => 'Método de pago',
        'banco_id' => 'Banco',
        'number_i_pay' => 'Número de la transacción',
        'date_transaction' => 'Fecha de la transacción',
        'date_payment' => 'Fecha del Pago',
        'ingreso_ammount' => 'Monto del Ingreso',
        'ingreso_observations' => 'Observaciones del Ingreso',
        'person_bill_ci' => 'Cédula del titular de la trasnferencia',
        'person_bill_name' => 'Nombre del titular de la trasnferencia',
        'comment' => 'Comentario',
        'status_approved' => 'Verificación/Concialición',
        'status_apply' => 'Aplicación en un pago',

    ];

    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }
    public function mbancario()
    {
        return $this->belongsTo('App\Models\app\Planpago\Mbancario','number_i_pay');
    }
    public function banco()
    {
        return $this->belongsTo('App\Models\app\Institucion\Banco');
    }
    public function metodo_pago()
    {
        return $this->belongsTo('App\Models\app\Planpago\MetodoPago','method_pay_id');
    }

    public function getErrorExistAttribute ()
    {
        $mbancario = Mbancario::select('mbancarios.*')
            ->where('mbancarios.number_i_pay', $this->number_i_pay)
            ->first();
        return ($mbancario) ? false : true ;
    }
    public function getErrorApplyAttribute ()
    {
        $mbancario = Ingreso::select('ingresos.*')
            ->where('ingresos.number_i_pay', $this->number_i_pay)
            ->first();
        return ($mbancario) ? true : false ;
    }
    public function getErrorDateAttribute ()
    {
        $mbancario = Mbancario::select('mbancarios.*')
            ->where('mbancarios.number_i_pay', $this->number_i_pay)
            ->where('mbancarios.date_transaction', $this->date_transaction)
            ->first();
        return ($mbancario) ? false : true ;
    }
    public function getErrorAmmountAttribute ()
    {
        $mbancario = Mbancario::select('mbancarios.*')
            ->where('mbancarios.number_i_pay', $this->number_i_pay)
            ->where('mbancarios.ingreso_ammount', $this->ingreso_ammount)
            ->first();
        return ($mbancario) ? false : true ;
    }
    public function getErrorBankAttribute ()
    {
        $mbancario = Mbancario::select('mbancarios.*')
            ->where('mbancarios.number_i_pay', $this->number_i_pay)
            ->where('mbancarios.banco_id', $this->banco_id)
            ->first();
        return ($mbancario) ? false : true ;
    }
    public function getErrorRepresentantAttribute ()
    {
        $mbancario = Mbancario::select('mbancarios.*')
            ->where('mbancarios.number_i_pay', $this->number_i_pay)
            ->where('mbancarios.banco_id', $this->banco_id)
            ->first();
        return ($mbancario) ? false : true ;
    }

    public function scopeEnable($query, $flag='true')
    {
        return $query->where('prepagos.status_approved', $flag)->where('prepagos.status_apply','<>', $flag);
    }

    public function getStatusEnableAttribute()
    {
        return ( !empty($this->representant) && $this->status_approved == 'true' && empty($this->status_apply) ) ? true : false ;
    }

    public function getFullMbancarioAttribute()
    {
        $mbancario = Mbancario::where('number_i_pay',$this->number_i_pay)->first();
        if ($mbancario) {
            $ammount = f_float($mbancario->ingreso_ammount);
            $date_transaction = f_date($mbancario->date_transaction);
            return "MOV. BANCARIO: {$mbancario->number_i_pay} || {$date_transaction} || {$ammount}";
        }
        return 'NUM. REFERENCIA NO ENCONTRADO';
    }

    public function getFullReferenceAttribute()
    {
        $ammount = f_float($this->ingreso_ammount);
        $date_transaction = f_date($this->date_transaction);
        return "{$this->number_i_pay} - {$date_transaction} - {$ammount}";
    }
}
