<?php

namespace App\Models\app\Assistcontrol;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssitSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','number_turn','description','observations','frecuency','status'
    ];

    const COLUMN_COMMENTS = [
        'name' => 'Nombre',
        'number_turn' => 'Cantidad de Turnos',
        'description' => 'Descripción',
        'observations' => 'Observación',
        'frecuency' => 'Frecuencia',
        'status' => 'Estado',
        'assit_schedule' => 'Horario Laboral'
    ];

    public function assit_days()
    {
        return $this->hasMany('App\Models\app\Assistcontrol\AssitDay');
    }

    public function getWorkersAttribute()
    {
        $fecha = Carbon::now();
        $workers = User::select('users.*','rols.id as rol_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->join('assit_schedules', 'assit_schedules.id', '=', 'rols.assit_schedule_id')
            ->where('assit_schedules.id',$this->id)
            ->whereNotNull('users.work_id')
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->get()
            ;
        return $workers;
    }

    public static function list_assit_schedule() /* usada para llenar los objetos de formularios select*/
    {
        return AssitSchedule::where('status','true')->pluck('name','id');
    }

    public static function getWorkers($id)
    {
        $fecha = Carbon::now();
        $workers = User::select('users.*','rols.id as rol_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->join('assit_schedules', 'assit_schedules.id', '=', 'rols.assit_schedule_id')
            ->where('assit_schedules.id',$id)
            ->whereNotNull('users.work_id')
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->get()
            ;
        return $workers;
    }
}

/*

$table->id();
$table->string('name')->comment('Nombre');
$table->smallInteger('number_turn')->unsigned()->comment(Cantidad de Turnos);
$table->text('description')->comment('Descripción');
$table->text('observations')->nulllable()->comment('Observación');
$table->enum('frecuency',['SEMANAL','QUINCENAL','MENSUAL'])->default('SEMANAL')->comment('Frecuencia');
$table->enum('status',['true','false'])->default('true')->comment('Estado');
$table->timestamps();

*/
