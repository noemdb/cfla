<?php

namespace App\Models\app\Cobranzas;

use Illuminate\Database\Eloquent\Model;

class CollActivity extends Model
{
    protected $fillable = [
        'user_id','coll_nivel_id','representant_id','description','status_id','status_messege','status_call'
    ];

    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'coll_nivel_id' => 'Nivel de Cobranza',
        'representant_id' => 'Representante',
        'description' => 'Descripción',
        'status_id' => 'Estado de finalización',
        'status_messege' => 'Estado de envío de email',
        'status_call' => 'Estado contacto telefónico',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function coll_nivel()
    {
        return $this->belongsTo('App\Models\app\Cobranzas\CollNivel');
    }
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }
    public function status()
    {
        return $this->belongsTo('App\Models\app\Common\Status');
    }
}



/*
Schema::create('coll_activities', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->integer('user_id')->unsigned();
    $table->smallinteger('coll_nivel_id')->unsigned();
    $table->bigInteger('representant_id')->unsigned()->comment('Representante');
    $table->string('description')->nullable()->comment('Descripción');
    $table->smallinteger('status_id')->unsigned()->comment('Estado');
    $table->enum('status_messege',['true','false'])->default('true')->comment('Estado de envío de email');
    $table->enum('status_call',['true','false'])->default('true')->comment('Estado de aprobación');
    $table->timestamps();
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('coll_nivel_id')->references('id')->on('coll_nivels')->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade')->onUpdate('cascade');
    
});

*/