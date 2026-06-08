<?php

namespace App\Models\sys;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Rol extends Model
{
	use Notifiable;

    protected $fillable = [
        'user_id','area','rol','cargo_id','group','assit_schedule_id','descripcion','finicial','ffinal','status_census_taker','status_schedule'
    ];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'area' => 'Area',
        'rol' => 'Rol',
        'cargo_id' => 'Cargo',
        'group' => 'Agrupacion',
        'assit_schedule_id' => 'Asitencia',
        'descripcion' => 'Descripcion',
        'finicial' => 'F. Inicial',
        'ffinal' => 'F. Final',
        'status_census_taker' => 'Censador',
        'status_schedule' => 'Aplica para la sistencia',
    ];


    protected $dates = ['finicial','ffinal'];

    // Formatear fecha para visualización
    public function getFinicialFormattedAttribute()
    {
        return optional($this->finicial)->format('d-m-Y');
    }

    // Obtener fecha en formato para input date (YYYY-MM-DD)
    public function getFinicialInputAttribute()
    {
        return optional($this->finicial)->format('Y-m-d');
    }

    // Para asegurar el formato correcto al guardar
    public function setFinicialAttribute($value)
    {
        $this->attributes['finicial'] = $value ? Carbon::parse($value) : null;
    }

	/*INI relaciones entre modelos*/
	public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function cargo()
    {
        return $this->belongsTo('App\Models\sys\Cargo');
    }
    public function assit_schedule()
    {
        return $this->belongsTo('App\Models\app\Assistcontrol\AssitSchedule');
    }
    /*FIN relaciones entre modelos*/

    public static function list_rols_group()
    {
        //'imployeds','adviders','collaborators','all'
        return ['imployeds'=>'Empleados','adviders'=>'Asesores','collaborators'=>'Colaboradores'];
    }

    public static function list_area() /* usada para llenar los objetos de formularios select*/
    {
        return [
            'DIRECCION'=>'DIRECCION',
            'AUTORIDAD'=>'AUTORIDAD',
            'ADMINISTRACION'=>'ADMINISTRACION',
            'ADMINISTRATIVO'=>'ADMINISTRATIVO',
            'ACADEMICO'=>'ACADEMICO',
            'EVALUACION'=>'EVALUACION',
            'PROYECTO'=>'PROYECTO',
            'CONOCIMIENTO'=>'CONOCIMIENTO',
            'BIENESTAR'=>'BIENESTAR',
            'CONTROL ESTUDIO'=>'CONTROL ESTUDIO',
            'PROFESORADO'=>'PROFESORADO',
            'REPRESENTANTE'=>'REPRESENTANTE',
        ];
    }

    public static function list_rol() /* usada para llenar los objetos de formularios select*/
    {
        return [
            'DIRECTOR'=>'DIRECTOR',
            'SUBDIRECTOR'=>'SUBDIRECTOR',
            'AUTORIDAD1'=>'AUTORIDAD1',
            'AUTORIDAD2'=>'AUTORIDAD2',
            'AUTORIDAD3'=>'AUTORIDAD3',
            'AUTORIDAD4'=>'AUTORIDAD4',
            'ADMINISTRADOR'=>'ADMINISTRADOR',
            'COORDINADOR'=>'COORDINADOR',
            'SUPERVISOR'=>'SUPERVISOR',
            'PROFESOR'=>'PROFESOR',
            'ASISTENTE'=>'ASISTENTE',
            'USUARIO'=>'USUARIO',
            'ESTUDIANTE'=>'ESTUDIANTE',
            'REPRESENTANTE'=>'REPRESENTANTE',
            'INIVITADO'=>'INIVITADO',
            'PERSONAL'=>'PERSONAL',
            'INICIAL'=>'INICIAL',
        ];
    }

}

/*

ALTER TABLE `rols` CHANGE `rol` `rol` ENUM('DIRECTOR','SUBDIRECTOR','AUTORIDAD1','AUTORIDAD2','AUTORIDAD3','AUTORIDAD4','ADMINISTRADOR','COORDINADOR','SUPERVISOR','PROFESOR','ASISTENTE','USUARIO','ESTUDIANTE','REPRESENTANTE','INIVITADO','PERSONAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USUARIO';
ALTER TABLE `rols` CHANGE `area` `area` ENUM('SISTEMA','DIRECCION','AUTORIDAD','ADMINISTRACION','CONTROL ESTUDIO','PROFESORADO','ESTUDIANTIL','REPRESENTANTE','ACADEMICO','ADMINISTRATIVO','BIENESTAR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ESTUDIANTIL';

*/