<?php

namespace App\Models\app\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;
    protected $fillable = ['registro_pago_id','representant_id','estudiant_id','method_pay_id','banco_id','caf_id','number_i_pay','date_transaction','date_payment','date_reported','ingreso_ammount','exchange_rate_id','exchange_ammount_rate','exchange_ammount','status_late_payment','exchange_ammount_late_payment','ingreso_observations','person_bill_ci','person_bill_name','terminal_pos','approval_pos','sequence_pos','trace_pos','deleted_at',];

    const COLUMN_COMMENTS = [
        'representant_id' => 'Representante',
        'method_pay_id' => 'Método de pago',
        'banco_id' => 'Banco',
        'caf_id' => 'CAF_ID',
        'number_i_pay' => 'Número de la transacción',
        'date_transaction' => 'Fecha en Banco',
        'date_payment' => 'Fecha del Pago',
        'date_reported' => 'Fecha en que fue reportada',
        'ingreso_ammount' => 'Monto del Ingreso',
        'exchange_ammount' => 'Monto Cambiario',
        'status_late_payment' => 'Pago extemporaneo',
        'exchange_ammount_late_payment' => 'Monto cambiario del pago extemporaneo',
        'ingreso_observations' => 'Observaciones del Ingreso',
        'person_bill_ci' => 'Cédula del titular de la trasnferencia',
        'person_bill_name' => 'Nombre del titular de la trasnferencia',
        'terminal_pos' => 'Número Terminal POS',
        'approval_pos' => 'Número Trace POS',
        'sequence_pos' => 'Número Referencia POS',
        'trace_pos' => 'Número Trace POS',
        'comment' => 'Comentario',
        'status_approved' => 'Verificación/Concialición',
        'status_apply' => 'Aplicación en un pago',
    ];

    public function metodo_pago()
    {
        return $this->belongsTo('App\Models\app\Planpago\MetodoPago', 'method_pay_id');
    }
    public function banco()
    {
        return $this->belongsTo('App\Models\app\Institucion\Banco');
    }
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }
}
