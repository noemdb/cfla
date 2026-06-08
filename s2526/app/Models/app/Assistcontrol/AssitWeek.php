<?php

namespace App\Models\app\Assistcontrol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssitWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','assit_schedule_id','number_week'
    ];

    const COLUMN_COMMENTS = [
        'assit_schedule_id' => 'Horario',
        'name' => 'Nombre',
        'number_week' => 'Número de la semana'
    ];

    public function assit_schedule()
    {
        return $this->belongsTo('App\Models\app\Assistcontrol\AssitSchedule');
    }

    public function assit_days()
    {
        return $this->hasMany('App\Models\app\Assistcontrol\AssitDay','assit_week_id');
    }
}


/*

Schema::create('assit_weeks', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('assit_schedule_id');
    $table->string('name')->comment('Nombre');
    $table->smallInteger('number_week')->comment('Número de semana');
    $table->timestamps();
    $table->foreign('assit_schedule_id')->references('id')->on('assit_schedules')->onDelete('cascade')->onUpdate('cascade');
});


 */
