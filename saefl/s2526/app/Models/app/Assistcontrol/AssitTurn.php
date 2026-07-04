<?php

namespace App\Models\app\Assistcontrol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssitTurn extends Model
{
    use HasFactory;

    protected $fillable = [
        'assit_day_id','name','number'
    ];

    const COLUMN_COMMENTS = [
        'assit_day_id' => 'Día',
        'name' => 'Nombre',
        'number' => 'Número del turno'
    ];

    public function assit_day()
    {
        return $this->belongsTo('App\Models\app\Assistcontrol\AssitDay');
    }

    public function assit_hours()
    {
        return $this->hasMany('App\Models\app\Assistcontrol\AssitHour');
    }
}


/*
Schema::create('assit_turns', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('assit_day_id');
    $table->string('name')->comment('Nombre');
    $table->smallInteger('number')->unsigned()->comment('Hora');
    $table->timestamps();
    $table->foreign('assit_day_id')->references('id')->on('assit_days')->onDelete('cascade')->onUpdate('cascade');
});

*/
