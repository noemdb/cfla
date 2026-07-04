<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;

class ReciboPago extends Model
{
    protected $fillable = [
        'recibo_id',
        'quota',
        'exchange_ammount'
    ];

    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'recibo_id'=>'Identificador del Recibo',
        'quota'=>'Nombre de la cuota',
        'exchange_ammount'=>'Monto cambiario del pago'
    ];

    const LIST_QUOTA = [
        'I INSCRIPCIÓN AÑO ESCOLAR 2021-2022'=>'I INSCRIPCIÓN AÑO ESCOLAR 2021-2022',
        '1 MES DE SEPTIEMBRE'=>'1 MES DE SEPTIEMBRE',
        '1.1 ABONO MES DE SEPTIEMBRE'=>'1.1 ABONO MES DE SEPTIEMBRE',
        '1.2 DIFERENCIA MES DE SEPTIEMBRE'=>'1.2 DIFERENCIA MES DE SEPTIEMBRE',
        '2 MES DE OCTUBRE'=>'2 MES DE OCTUBRE',
        '2.1 ABONO MES DE OCTUBRE'=>'2.1 ABONO MES DE OCTUBRE',
        '2.2 DIFERENCIA MES DE OCTUBRE'=>'2.2 DIFERENCIA MES DE OCTUBRE',
        '3 MES DE NOVIEMBRE'=>'3 MES DE NOVIEMBRE',
        '3.1 ABONO MES DE NOVIEMBRE'=>'3.1 ABONO MES DE NOVIEMBRE',
        '3.2 DIFERENCIA MES DE NOVIEMBRE'=>'3.2 DIFERENCIA MES DE NOVIEMBRE',
        '4 MES DE DICIEMBRE'=>'4 MES DE DICIEMBRE',
        '4.1 ABONO MES DE DICIEMBRE'=>'4.1 ABONO MES DE DICIEMBRE',
        '4.2 DIFERENCIA MES DE DICIEMBRE'=>'4.2 DIFERENCIA MES DE DICIEMBRE',
        '5 MES DE ENERO'=>'5 MES DE ENERO',
        '5.1 ABONO MES DE ENERO'=>'5.1 ABONO MES DE ENERO',
        '5.2 DIFERENCIA MES DE ENERO'=>'5.2 DIFERENCIA MES DE ENERO',
        '6 MES DE FEBRERO'=>'6 MES DE FEBRERO',
        '6.1 ABONO MES DE FEBRERO'=>'6.1 ABONO MES DE FEBRERO',
        '6.2 DIFERENCIA MES DE FEBRERO'=>'6.2 DIFERENCIA MES DE FEBRERO',
        '7 MES DE MARZO'=>'7 MES DE MARZO',
        '7.1 ABONO MES DE MARZO'=>'7.1 ABONO MES DE MARZO',
        '7.2 DIFERENCIA MES DE MARZO'=>'7.2 DIFERENCIA MES DE MARZO',
        '8 MES DE ABRIL'=>'8 MES DE ABRIL',
        '8.1 ABONO MES DE ABRIL'=>'8.1 ABONO MES DE ABRIL',
        '8.2 DIFERENCIA MES DE ABRIL'=>'8.2 DIFERENCIA MES DE ABRIL',
        '9 MES DE MAYO'=>'9 MES DE MAYO',
        '9.1 ABONO MES DE MAYO'=>'9.1 ABONO MES DE MAYO',
        '9.2 DIFERENCIA MES DE MAYO'=>'9.2 DIFERENCIA MES DE MAYO',
        '10 MES DE JUNIO'=>'10 MES DE JUNIO',
        '10.1 ABONO MES DE JUNIO'=>'10.1 ABONO MES DE JUNIO',
        '10.2 DIFERENCIA MES DE JUNIO'=>'10.2 DIFERENCIA MES DE JUNIO',
        '11 MES DE JULIO'=>'11 MES DE JULIO',
        '11.1 ABONO MES DE JULIO'=>'11.1 ABONO MES DE JULIO',
        '11.2 DIFERENCIA MES DE JULIO'=>'11.2 DIFERENCIA MES DE JULIO',
        '12 MES DE AGOSTO'=>'12 MES DE AGOSTO',
        '12.1 ABONO MES DE AGOSTO'=>'12.1 ABONO MES DE AGOSTO',
        '12.2 DIFERENCIA MES DE AGOSTO'=>'12.2 DIFERENCIA MES DE AGOSTO',
    ];

    public function recibo()
    {
        return $this->belongsTo('App\Models\app\Planpago\Recibo');
    }
}


/*


Schema::create('recibo_pagos', function (Blueprint $table) {
$table->bigIncrements('id');
$table->bigInteger('recibo_id')->unsigned()->comment('Identificador del Recibo');
$table->string('quota')->comment('Cuota');
$table->float('ammount',32,8)->nullable()->comment('Monto del Pago');
$table->float('exchange_ammount',16,5)->nullable()->comment('Monto cambiario del pago');
$table->timestamps();
$table->foreign('recibo_id')->references('id')->on('recibos')->onDelete('cascade')->onUpdate('cascade');
});




'I INSCRIPCIÓN AÑO ESCOLAR 2021-2022',
'1 MES DE SEPTIEMBRE',
'1.1 ABONO MES DE SEPTIEMBRE',
'1.2 DIFERENCIA MES DE SEPTIEMBRE',
'2 MES DE OCTUBRE',
'2.1 ABONO MES DE OCTUBRE',
'2.2 DIFERENCIA MES DE OCTUBRE',
'3 MES DE NOVIEMBRE',
'3.1 ABONO MES DE NOVIEMBRE',
'3.2 DIFERENCIA MES DE NOVIEMBRE',
'4 MES DE DICIEMBRE',
'4.1 ABONO MES DE DICIEMBRE',
'4.2 DIFERENCIA MES DE DICIEMBRE',
'5 MES DE ENERO',
'5.1 ABONO MES DE ENERO',
'5.2 DIFERENCIA MES DE ENERO',
'6 MES DE FEBRERO',
'6.1 ABONO MES DE FEBRERO',
'6.2 DIFERENCIA MES DE FEBRERO',
'7 MES DE MARZO',
'7.1 ABONO MES DE MARZO',
'7.2 DIFERENCIA MES DE MARZO',
'8 MES DE ABRIL',
'8.1 ABONO MES DE ABRIL',
'8.2 DIFERENCIA MES DE ABRIL',
'9 MES DE MAYO',
'9.1 ABONO MES DE MAYO',
'9.2 DIFERENCIA MES DE MAYO',
'10 MES DE JUNIO',
'10.1 ABONO MES DE JUNIO',
'10.2 DIFERENCIA MES DE JUNIO',
'11 MES DE JULIO',
'11.1 ABONO MES DE JULIO',
'11.2 DIFERENCIA MES DE JULIO',
'12 MES DE AGOSTO',
'12.1 ABONO MES DE AGOSTO',
'12.2 DIFERENCIA MES DE AGOSTO',
*/
