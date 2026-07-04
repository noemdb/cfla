<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;
use App\Models\app\Estudiante\Ingreso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExchangeRate extends Model
{
    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'currency_id' => 'Moneda',
        'currency_referential_id' => 'Moneda Referencial',
        'date'=>'Fecha',
        'ammount'=>'Monto Venta',
        'ammount_buy'=>'Monto Compra',
        'source'=>'Fuente de la información',
        'name'=>'Nombre',
        'observations'=>'Observaciones',
        'image'=>'Imagen Informativa',
        'status_official'=>'Oficial',
        'user_id'=>'Usuario'
    ];

    protected $fillable = [
        'currency_id',
        'currency_referential_id',
        'date',
        'ammount',
        'ammount_buy',
        'source',
        'name',
        'observations',
        'image',
        'status_official',
        'user_id'
    ];

    protected $dates = ['created_at','updated_at','date'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\app\Planpago\Currency');
    }

    public function currency_referential()
    {
        return $this->belongsTo('App\Models\app\Planpago\ReferentialCurrency');
    }

    /* no se va a usar */
    public function getFillAmmountExchangeIngresosAttribute()
    {
        $ingresos = Ingreso::whereDate('date_payment',$this->date)->get(); //dd($this,$ingresos);

        foreach ($ingresos as $ingreso) {
            $data = $ingreso->update_exchange_rate;
        }
    }

    public function getFillAmmountExchangeCafsAttribute()
    {
        $cafs = DB::table('credito_a_favors')
            ->select('credito_a_favors.*','ingresos.id as ingreso_id','ingresos.date_payment')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->where('ingresos.date_payment',$this->date)
            ->get();

        foreach ($cafs as $caf) {
            $exchange_ammount = $caf->credito_ammount / $this->ammount ;
            $caf_affected = DB::table('credito_a_favors')->where('id', $caf->id)->update(['exchange_rate_id'=>$this->id,'exchange_ammount'=>$exchange_ammount]);
        }
    }

    public static function getAmmountExchange ()
    {
        $exchange_ammount = ExchangeRate::whereDate('date',Carbon::now())->whereNotNull('ammount')->first() ;
        return ($exchange_ammount) ? $exchange_ammount->ammount : null;
    }

    public static function getAmmountExchangeNear ($date = null)
    {
        $date = ($date) ? $date : Carbon::now();
        $exchange_ammount = ExchangeRate::whereDate('date','<=',$date)->whereNotNull('ammount')->orderBy('date','desc')->first() ; //dd($exchange_ammount);
        return ($exchange_ammount) ? $exchange_ammount->ammount : null;
    }

}
