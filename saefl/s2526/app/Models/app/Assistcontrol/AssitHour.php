<?php

namespace App\Models\app\Assistcontrol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssitHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'assit_turn_id','h','m','type'
    ];

    const COLUMN_COMMENTS = [
        'assit_turn_id' => 'Turno',
        'h' => 'Hora',
        'm' => 'Minutos',
        'type' => 'Tipo',
    ];


    public function assit_turn()
    {
        return $this->belongsTo('App\Models\app\Assistcontrol\AssitTurn');
    }
    public function getNameAttribute()
    {
        $hour = ($this->h < 10) ? str_pad($this->h,2,'0',STR_PAD_LEFT): $this->h;
        $minut = ($this->m < 10) ? str_pad($this->m,2,'0',STR_PAD_LEFT): $this->m;
        $type =  ($this->type) ? 'Entrada' : 'Salida' ;
        return "{$hour}:{$minut} || {$type}" ;
    }
}



/*

Schema::create('assit_hours', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('assit_turn_id');
    $table->smallInteger('h')->unsigned()->comment('Hora');
    $table->smallInteger('m')->unsigned()->comment('Minutos');
    $table->boolean('type')->comment('Entrada/Salida');
    $table->timestamps();
    $table->foreign('assit_turn_id')->references('id')->on('assit_turns')->onDelete('cascade')->onUpdate('cascade');
});

*/
