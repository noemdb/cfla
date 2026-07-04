<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;

class ReciboCash extends Model
{
    protected $fillable = [ 'recibo_id', 'serial', 'exchange_ammount' ];

    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'recibo_id'=>'Identificador del Recibo',
        'serial'=>'Serial',
        'exchange_ammount'=>'Monto cambiario del billete'
    ];

    public function recibo()
    {
        return $this->belongsTo('App\Models\app\Planpago\Recibo');
    }

}


/*

Schema::create('recibo_cashes', function (Blueprint $table) {
$table->bigIncrements('id');
$table->bigInteger('recibo_id')->unsigned()->comment('Identificador del Recibo');
$table->string('serial')->comment('Serial');
$table->float('ammount',32,8)->comment('Monto del billete');
$table->float('exchange_ammount',16,5)->nullable()->comment('Monto cambiario del efectivo');
$table->timestamps();
$table->foreign('recibo_id')->references('id')->on('recibos')->onDelete('cascade')->onUpdate('cascade');
});

 *
 */
