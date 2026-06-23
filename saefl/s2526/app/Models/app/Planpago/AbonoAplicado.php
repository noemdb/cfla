<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\Estudiante\Abono;

class AbonoAplicado extends Model
{
    use SoftDeletes;
    protected $guarded = ['id','deleted_at','created_at','updated_at'];

    public function abono()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Abono','abono_id');
    }

    public function registropago()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPago');
    }

    public function getAbonoTrashAttribute()
    {
        // $abono = Abono::withTrashed()->where('id',$this->abono_id);
        return Abono::withTrashed()->where('id',$this->abono_id)->first();
    }

    public function getAllAbonoAttribute()
    {
        $all_abono = Abono::withTrashed()
        ->select('abonos.*','abono_aplicados.id as abono_aplicado_id','abono_aplicados.registro_pago_id','bancos.name as banco_name',
        'ingresos.number_i_pay as number_i_pay','ingresos.ingreso_ammount as abono_ammount',
        'ingresos.deleted_at as ingreso_deleted_at', 'ingresos.ingreso_observations')
        ->join('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
        ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
        ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
        ->where('abono_aplicados.id',$this->id)
        ->first();
        return $all_abono;
    }

}
