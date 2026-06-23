<?php

namespace App\Models\app\Cobranzas;

use Illuminate\Database\Eloquent\Model;

class CollMessege extends Model
{
    protected $fillable = [
        'user_id','coll_nivel_id','subject','title','subtitle','greeting','consider','sentence','waiting','footer','status'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'coll_nivel_id' => 'Nivel',
        'subject' => 'Asunto',
        'title' => 'Título',
        'subtitle' => 'Subtítulo',
        'greeting' => 'Saludo formal',
        'consider' => 'Considerando',
        'sentence' => 'Solicitud',
        'waiting' => 'Esperando pronta respuesta',
        'footer' => 'Despedida',
        'status' => 'Estado'
    ];

    public function coll_nivel()
    {
        return $this->belongsTo('App\Models\app\Cobranzas\CollNivel');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}


/*
$table->integer('user_id')->unsigned();
$table->smallinteger('coll_nivel_id')->unsigned();
$table->string('subject')->comment('asunto');
$table->string('title')->comment('Título');
$table->string('subtitle')->comment('Subtítulo');
$table->string('greeting')->comment('Saludo formal');
$table->string('consider')->comment('Considerando');
$table->string('sentence')->comment('Solicitud');
$table->string('waiting')->comment('Esperando pronta respuesta');
$table->string('footer')->comment('Despedida');
*/
