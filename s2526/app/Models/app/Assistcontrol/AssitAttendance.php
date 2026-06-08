<?php

namespace App\Models\app\Assistcontrol;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssitAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user','work_id','card_id','date','time','timestamp','in_out','event_code'
    ];
    protected $dates = ['date','timestamp'];
    protected $dateFormat = 'd-m-Y';

    protected $casts = [
        'date'  => 'date:d-m-Y',
        'timestamp' => 'datetime:d-m-Y H:00',
    ];

    const COLUMN_COMMENTS = [
        'user' => 'Nombre de Usuario',
        'card_id' => 'Identificador del Trabajado',
        'date' => 'Fecha',
        'time' => 'hora',
        'timestamp' => 'Marca UNIX',
        'in_out' => 'Entrada/Salida',
        'status' => 'Estado de activación',
        'ident' => 'Identificador',
        'work_id' => 'Indent. Trabajador',
        'card_no' => 'Número de Tarjeta',
        'date_time' => 'Fecha - Hora',
        'inout' => 'Entrada/Salida',
        'event_string' => 'Evento',
        'event_code' => 'Cod.Evento',
        'firstname' => 'Nombre',
        'lastname' => 'Apellido',
        'status_registrer' => 'Estado de registro',
    ];
}


/*

$table->id();
$table->string('user')->comment('Nombre de Usuario');
$table->string('work_id')->comment('Identificador del Trabajado');
$table->string('card_id')->nullable()->comment('Num. de Tarjeta');
$table->string('date')->comment('fecha');
$table->string('time')->comment('hora');
$table->timestamp('timestamp');
$table->string('in_out')->comment('Entrada/Salida');
$table->string('event_code')->comment('Evento');
$table->timestamps();

+"ident": "'CCORTEZ"
+"work_id": 2
+"card_no": "'NULL"
+"date": "2021-09-07"
+"inout": "03:58:12"
+"event_code": "IN"
+"firstName": "CARMIN ANDREINA"
+"lastname": "CORTEZ ALEJOS"

*/
