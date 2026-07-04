<?php

namespace App\Models\app\Assistcontrol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssitDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'assit_week_id','name','number_day'
    ];

    const COLUMN_COMMENTS = [
        'assit_week_id' => 'Semana',
        'name' => 'Nombre',
        'number_day' => 'Número del día'
    ];

    public function assit_week()
    {
        return $this->belongsTo('App\Models\app\Assistcontrol\AssitWeek');
    }

    public function assit_turns()
    {
        return $this->hasMany('App\Models\app\Assistcontrol\AssitTurn','assit_day_id');
    }


}


/*

$table->id();
$table->unsignedBigInteger('assit_schedule_id');
$table->string('name')->comment('Nombre');
$table->smallInteger('number_day')->comment('Número del día');
$table->timestamps();

*/
