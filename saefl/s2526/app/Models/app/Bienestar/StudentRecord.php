<?php

namespace App\Models\app\Bienestar;

use App\Models\app\Bienestar\Traits\StudentRecordTrait;
use App\Models\app\Estudiant;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentRecord extends Model
{
    use HasFactory;
    use StudentRecordTrait;
    protected $fillable = [
        'user_id','estudiant_id','coexistence','status_transport_private_vehicle','status_transport_public_vehicle',
        'status_transport_walking','status_transport_other','transport_other','status_vaccination_schedule','status_sports_potential','sports_potential',
        'place_where_he_practices','status_illness_cardiovascular','status_illness_cancer','status_illness_lupus',
        'status_illness_diabetes','status_illness_renal_problems','status_illness_overweight','status_illness_other',
        'illness_other','status_conditions_intellectual_disability','status_conditions_motor_disability',
        'status_conditions_visual_disability','status_conditions_hearing_impairment','status_conditions_outstanding_attitudes',
        'status_conditions_autism','status_conditions_other','conditions_other','status_treated_by_specialist','specialist',
        'status_take_medication','medication','mother_name','mother_lastname','mother_ci','mother_profession','mother_phones',
        'mother_address','father_name','father_lastname','father_ci','father_profession','father_phones','father_address',
    ];

    protected $dates = ['created_at','updated_at'];

    protected $dateFormat = 'Y-m-d H:i:s';

    const COLUMN_COMMENTS = [
        'user_id'=>'Usuario',
        'estudiant_id'=>'Estudiante',
        'coexistence'=>'Con quién vive?',
        'status_transport_private_vehicle'=>'Vehículo particular',
        'status_transport_public_vehicle'=>'Trasnporte público',
        'status_transport_walking'=>'Caminando',
        'status_transport_other'=>'Otro (especifique)',
        'transport_other'=>'Nombre del otro medio de transporte',
        'status_vaccination_schedule'=>'Esquema de vacunación',
        'status_sports_potential'=>'Posee potencial deportivo/cultural u otro',
        'sports_potential'=>'Especifique actividad deportiva/cultural u otro',
        'place_where_he_practices'=>'Lugar dónde practica',
        'status_illness_cardiovascular'=>'Cardiovascular',
        'status_illness_cancer'=>'Cáncer',
        'status_illness_lupus'=>'Lupus',
        'status_illness_diabetes'=>'Diabetes',
        'status_illness_renal_problems'=>'Problemas Renales',
        'status_illness_overweight'=>'Sobrepeso',
        'status_illness_other'=>'Otras enfermedades graves',
        'illness_other'=>'Otra enfermedad de gravedad',
        'status_conditions_intellectual_disability'=>'Discapacidad intelectual',
        'status_conditions_motor_disability'=>'Discapacidad motriz',
        'status_conditions_visual_disability'=>'Discapacidad visual',
        'status_conditions_hearing_impairment'=>'Discapacidad auditiva',
        'status_conditions_outstanding_attitudes'=>'Actitudes sobresalientes',
        'status_conditions_autism'=>'Autismo',
        'status_conditions_other'=>'Otra (especifique)',
        'conditions_other'=>'Otra condición de gravedad',
        'status_treated_by_specialist'=>'Tratado por algún especialista',
        'specialist'=>'Especialista',
        'status_take_medication'=>'Toma algún medicamento',
        'medication'=>'Medicamento',
        'mother_name'=>'Nombres de La madre',
        'mother_lastname'=>'Apellidos de la madre',
        'mother_ci'=>'Cédula de la madre',
        'mother_profession'=>'Profesión de la madre',
        'mother_phones'=>'Teléfonos de la madre',
        'mother_address'=>'Dirección y contacto del trabajo de la madre',
        'father_name'=>'Nombres del padre',
        'father_lastname'=>'Apellidos del padre',
        'father_ci'=>'Cédula del padre',
        'father_profession'=>'Profesión de la padre',
        'father_phones'=>'Teléfonos del padre',
        'father_address'=>'Dirección y contacto del trabajo de la padre',
    ];

    const LIST_POTENCIAL = [
        'Ajedrez'=>'Ajedrez',
        'Atletismo'=>'Atletismo',
        'Badminton'=>'Badminton',
        'Basquetbol'=>'Basquetbol',
        'Beisbol'=>'Beisbol',
        'Boliche'=>'Boliche',
        'Box'=>'Box',
        'Ciclismo'=>'Ciclismo',
        'Futbol rápido'=>'Futbol rápido',
        'Futbol soccer'=>'Futbol soccer',
        'Grupos de animación'=>'Grupos de animación',
        'Halterofilia'=>'Halterofilia',
        'Handball'=>'Handball',
        'Judo'=>'Judo',
        'Karate do'=>'Karate do',
        'Lucha olímpica'=>'Lucha olímpica',
        'Natación'=>'Natación',
        'Raquetbol'=>'Raquetbol',
        'Softbol'=>'Softbol',
        'Tae kwon do'=>'Tae kwon do',
        'Tenis'=>'Tenis',
        'Tenis de mesa'=>'Tenis de mesa',
        'Voleibol'=>'Voleibol',
        'Voleibol de playa'=>'Voleibol de playa',
        'Canto'=>'Canto',
        'Baile'=>'Baile',
        'Instrumento músical'=>'Instrumento músical',
        'Otros'=>'Otros',
    ];

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}


/*
'estudiant_id','coexistence','status_transport_private_vehicle','status_transport_public_vehicle','status_transport_walking','status_transport_other','vaccination_schedule','sports_potential','place_where_he_practices','status_illness_cardiovascular','status_illness_cancer','status_illness_lupus','status_illness_diabetes','status_illness_renal_problems','status_illness_overweight','status_illness_other','illness_other','status_conditions_intellectual_disability','status_conditions_motor_disability','status_conditions_visual_disability','status_conditions_hearing_impairment','status_conditions_outstanding_attitudes','status_conditions_autism','status_conditions_other','conditions_other','status_treated_by_specialist','specialist','status_take_medication','medication','mother_name','mother_lastname','mother_profession','mother_phones','mother_address','father_name','father_lastname','father_profession','father_phones','father_address',

'estudiant_id'=>'Estudiante',
'coexistence'=>'Con quién vive?',
'status_transport_private_vehicle'=>'Vehículo particular',
'status_transport_public_vehicle'=>'Trasnporte público',
'status_transport_walking'=>'Caminando',
'status_transport_other'=>'Caminando',
'vaccination_schedule'=>'Esquema de vacunación',
'sports_potential'=>'Potencial deportivo',
'place_where_he_practices'=>'Lugar dónde practica',
'status_illness_cardiovascular'=>'Cardiovascular',
'status_illness_cancer'=>'Cáncer',
'status_illness_lupus'=>'Lupus',
'status_illness_diabetes'=>'Diabetes',
'status_illness_renal_problems'=>'Problemas Renales',
'status_illness_overweight'=>'Sobrepeso',
'status_illness_other'=>'Otra (Especifique)',
'illness_other'=>'Otra enfermedad de gravedad',
'status_conditions_intellectual_disability'=>'Discapacidad intelectual',
'status_conditions_motor_disability'=>'Discapacidad motriz',
'status_conditions_visual_disability'=>'Discapacidad visual',
'status_conditions_hearing_impairment'=>'Discapacidad auditiva',
'status_conditions_outstanding_attitudes'=>'Actitudes sobresalientes',
'status_conditions_autism'=>'Autismo',
'status_conditions_other'=>'Otra (especifique)',
'conditions_other'=>'Otra condición de gravedad',
'status_treated_by_specialist'=>'Tratado por algún especialista',
'specialist'=>'Especialista',
'status_take_medication'=>'Toma algún medicamento',
'medication'=>'Medicamento',
'mother_name'=>'Nombres de La madre',
'mother_lastname'=>'Apellidos de la madre',
'mother_profession'=>'Profesión de la madre',
'mother_phones'=>'teléfonos de la madre',
'mother_address'=>'Dirección y contacto del trabajo de la madre',
'father_name'=>'Nombres del padre',
'father_lastname'=>'Apellidos del padre',
'father_profession'=>'Profesión de la padre',
'father_phones'=>'teléfonos del padre',
'father_address'=>'Dirección y contacto del trabajo de la padre',




Estudiante
Con quién vive?
Vehículo particular
Trasnporte público
Caminando
Caminando
Esquema de vacunación
Potencial deportivo
Lugar dónde practica
Cardiovascular
Cáncer
Lupus
Diabetes
Problemas Renales
Sobrepeso
Otra (Especifique)
Otra enfermedad de gravedad
Discapacidad intelectual
Discapacidad motriz
Discapacidad visual
Discapacidad auditiva
Actitudes sobresalientes
Autismo
Otra (especifique)
Otra condición de gravedad
Tratado por algún especialista
Especialista
Toma algún medicamento
Medicamento
Nombres de La madre
Apellidos de la madre
Profesión de la madre
teléfonos de la madre
Dirección y contacto del trabajo de la madre
Nombres del padre
Apellidos del padre
Profesión de la padre
teléfonos del padre
Dirección y contacto del trabajo de la padre



'Ajedrez'=>'Ajedrez',
'Atletismo'=>'Atletismo',
'Badminton'=>'Badminton',
'Basquetbol'=>'Basquetbol',
'Basquetbol'=>'Basquetbol',
'Beisbol'=>'Beisbol',
'Boliche'=>'Boliche',
'Box'=>'Box',
'Ciclismo'=>'Ciclismo',
'Frontón'=>'Frontón',
'Futbol rápido'=>'Futbol rápido',
'Futbol soccer'=>'Futbol soccer',
'Grupos de animación'=>'Grupos de animación',
'Halterofilia'=>'Halterofilia',
'Handball'=>'Handball',
'Judo'=>'Judo',
'Karate do'=>'Karate do',
'Lima lama'=>'Lima lama',
'Lucha olímpica'=>'Lucha olímpica',
'Natación'=>'Natación',
'Raquetbol'=>'Raquetbol',
'Softbol'=>'Softbol',
'Tae kwon do'=>'Tae kwon do',
'Tenis'=>'Tenis',
'Tenis de mesa'=>'Tenis de mesa',
'Tochito'=>'Tochito',
'Voleibol'=>'Voleibol',
'Voleibol de playa'=>'Voleibol de playa',

*/
