<?php

namespace App\Models\app\Cobranzas;

use Illuminate\Database\Eloquent\Model;

class CollPromise extends Model
{
    protected $fillable = [
        'coll_political_id','representant_id','date','ammount','exchange_ammount','status','description','observation','created_at'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'coll_political_id' => 'Política de Cobranza',
        'representant_id' => 'Representante',
        'date' => 'Fecha del cumplimiento',
        'ammount' => 'Monto',
        'exchange_ammount' => 'Monto Cambiario',
        'status' => 'Estado',
        'description' => 'Descripción',
        'observation' => 'Observaciones',
    ];

    public function coll_political()
    {
        return $this->belongsTo('App\Models\app\Cobranzas\CollPolitical');
    }
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }

}

/*

$table->bigIncrements('id');
$table->smallinteger('coll_political_id')->unsigned();
$table->bigInteger('representant_id')->unsigned()->comment('Representante');
$table->date('date')->comment('Fecha del cumplimiento de la promesa');
$table->float('ammount',16,2)->comment('Monto');
$table->float('exchange_ammount',10,6)->comment('Monto Cambiario');
$table->enum('status',['true','false'])->default('true')->comment('Estado');
$table->string('description')->nullable()->comment('Descripción');
$table->string('observation')->nullable()->comment('Observaciones');

*/