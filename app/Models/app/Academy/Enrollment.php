<?php

namespace App\Models\app\Academy;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'ci_estudiant', 'lastname', 'name', 'photo', 'cellphone', 'gender', 'date_birth',
        'age', 'town_hall_birth', 'state_birth', 'country_birth', 'dir_address', 'pestudio_id',
        'grado_id', 'grupo_estable_id', 'pending_matter', 'blood_type', 'weight', 'height', 'laterality', 'institution',
        'order_born', 'group_family', 'status_brother', 'literal', 'ci_representant', 'name_representant',
        'relationship', 'profession_representant', 'phone_representant', 'cellphone_representant', 'email_representant', 'twitter', 'instagram', 'recommended_by',

        'coexistence', 'status_transport_private_vehicle', 'status_transport_public_vehicle',
        'status_transport_walking', 'status_transport_other', 'transport_other', 'status_vaccination_schedule', 'status_sports_potential', 'sports_potential',
        'place_where_he_practices', 'status_illness_cardiovascular', 'status_illness_cancer', 'status_illness_lupus',
        'status_illness_diabetes', 'status_illness_renal_problems', 'status_illness_overweight', 'status_illness_other',
        'illness_other', 'status_conditions_intellectual_disability', 'status_conditions_motor_disability',
        'status_conditions_visual_disability', 'status_conditions_hearing_impairment', 'status_conditions_outstanding_attitudes',
        'status_conditions_autism', 'status_conditions_other', 'conditions_other', 'status_treated_by_specialist', 'specialist',
        'status_take_medication', 'medication', 'mother_name', 'mother_lastname', 'mother_ci', 'mother_profession', 'mother_phones',
        'mother_address', 'father_name', 'father_lastname', 'father_ci', 'father_profession', 'father_phones', 'father_address',

    ];
    protected $dates = ['created_at', 'updated_at'];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'ci_estudiant' => 'Cédula de identidad',
        'lastname' => 'Apellidos',
        'name' => 'Nombres',
        'fullname' => 'Nombre',
        'photo' => 'Foto',
        'image' => 'Foto',
        'photo_url' => 'Foto',
        'cellphone' => 'Número de teléfono celular',
        'gender' => 'Género',
        'date_birth' => 'Fecha de nacimiento',

        'age' => 'Edad',
        'town_hall_birth' => 'Municipio de nacimiento',
        'state_birth' => 'Estado de nacimiento',
        'country_birth' => 'País de nacimiento',
        'dir_address' => 'Dirección de residencia',

        'pestudio_id' => 'Programa de Estudio',
        'grado_id' => 'Nivel', 'grado' => 'Nivel',
        'grupo_estable_id' => 'Grupo Estable',
        'pending_matter' => 'Materia Pendiente separadas por comas',
        'blood_type' => 'Grupo Sanguineo',
        'weight' => 'Peso (Kg)',
        'height' => 'Estatura (cm)',
        'laterality' => 'Lateralidad',
        'institution' => 'Institución de procedencia',

        'order_born' => 'Orden de Nacimiento',
        'group_family' => 'Grupo Familiar',
        'status_brother' => 'Tiene hermanos en el colegio?',
        'literal' => 'Literal de promosión, (sólo primaria)',

        'ci_representant' => 'Cédula de identidad',
        'name_representant' => 'Nombre del representante',
        'lastname_representant' => 'Apellidos',
        'relationship' => 'Parentesco',
        'profession_representant' => 'Profesión',
        'phone_representant' => 'Número de teléfono',
        'cellphone_representant' => 'Número de teléfono celular [WhatsApp, Telegram, etc]',
        'email_representant' => 'Correo electrónico',
        'twitter' => 'Usuario Twitter',
        'instagram' => 'Usuario Instagram',
        'created_at' => 'F.Registro',
        'recommended_by' => 'Recomendado por:',

        'coexistence' => 'Con quién vive?',
        'status_transport_private_vehicle' => 'Vehículo particular',
        'status_transport_public_vehicle' => 'Trasnporte público',
        'status_transport_walking' => 'Caminando',
        'status_transport_other' => 'Otro (especifique)',
        'transport_other' => 'Nombre del otro medio de transporte',
        'status_vaccination_schedule' => 'Esquema de vacunación',
        'status_sports_potential' => 'Posee algún potencial cultural o de otro tipo?',
        'sports_potential' => 'Especifique actividad deportiva/cultural u otro',
        'place_where_he_practices' => 'Lugar dónde practica',
        'status_illness_cardiovascular' => 'Cardiovascular',
        'status_illness_cancer' => 'Cáncer',
        'status_illness_lupus' => 'Lupus',
        'status_illness_diabetes' => 'Diabetes',
        'status_illness_renal_problems' => 'Problemas Renales',
        'status_illness_overweight' => 'Sobrepeso',
        'status_illness_other' => 'Otra (Especifique)',
        'illness_other' => 'Otra enfermedad de gravedad',
        'status_conditions_intellectual_disability' => 'Discapacidad intelectual',
        'status_conditions_motor_disability' => 'Discapacidad motriz',
        'status_conditions_visual_disability' => 'Discapacidad visual',
        'status_conditions_hearing_impairment' => 'Discapacidad auditiva',
        'status_conditions_outstanding_attitudes' => 'Actitudes sobresalientes',
        'status_conditions_autism' => 'Autismo',
        'status_conditions_other' => 'Otra (especifique)',
        'conditions_other' => 'Otra condición de gravedad',
        'status_treated_by_specialist' => 'Tratado por algún especialista',
        'specialist' => 'Especialista',
        'status_take_medication' => 'Toma algún medicamento',
        'medication' => 'Medicamento',
        'mother_name' => 'Nombres de La madre',
        'mother_lastname' => 'Apellidos de la madre',
        'mother_ci' => 'Cédula de la madre',
        'mother_profession' => 'Profesión/Oficio de la madre',
        'mother_phones' => 'Teléfonos de la madre',
        'mother_address' => 'Dirección y contacto del trabajo de la madre',
        'father_name' => 'Nombres del padre',
        'father_lastname' => 'Apellidos del padre',
        'father_ci' => 'Cédula del padre',
        'father_profession' => 'Profesión/Oficio del padre',
        'father_phones' => 'Teléfonos del padre',
        'father_address' => 'Dirección y contacto del trabajo de la padre',
    ];

    const LIST_POTENCIAL = [
        'Ajedrez' => 'Ajedrez',
        'Atletismo' => 'Atletismo',
        'Badminton' => 'Badminton',
        'Basquetbol' => 'Basquetbol',
        'Beisbol' => 'Beisbol',
        'Boliche' => 'Boliche',
        'Box' => 'Box',
        'Ciclismo' => 'Ciclismo',
        'Futbol rápido' => 'Futbol rápido',
        'Futbol soccer' => 'Futbol soccer',
        'Grupos de animación' => 'Grupos de animación',
        'Halterofilia' => 'Halterofilia',
        'Handball' => 'Handball',
        'Judo' => 'Judo',
        'Karate do' => 'Karate do',
        'Lucha olímpica' => 'Lucha olímpica',
        'Natación' => 'Natación',
        'Raquetbol' => 'Raquetbol',
        'Softbol' => 'Softbol',
        'Tae kwon do' => 'Tae kwon do',
        'Tenis' => 'Tenis',
        'Tenis de mesa' => 'Tenis de mesa',
        'Voleibol' => 'Voleibol',
        'Voleibol de playa' => 'Voleibol de playa',
        'Canto' => 'Canto',
        'Baile' => 'Baile',
        'Instrumento músical' => 'Instrumento músical',
        'Otros' => 'Otros',
    ];

    public static function list_coexistence()
    {
        return ["Con mis padres" => "Con mis padres", "Con mis abuelos" => "Con mis abuelos", "Con mis padres adoptivos" => "Con mis padres adoptivos", "Con mis hermanos" => "Con mis hermanos", "Con mis tíos o tías" => "Con mis tíos o tías", "Con mis primos" => "Con mis primos", "Con un tutor" => "Con un tutor", "Con un cuidador informal" => "Con un cuidador informal", "No vivo con nadie" => "No vivo con nadie"];
    }

    public static function list_sports_potential()
    {
        return ["Tenis"=>"Tenis","Futbol rápido"=>"Futbol rápido","Voleibol"=>"Voleibol","Baile"=>"Baile","Futbol soccer"=>"Futbol soccer","Instrumento músical"=>"Instrumento músical","Otros"=>"Otros","Karate do"=>"Karate do","Ciclismo"=>"Ciclismo","Beisbol"=>"Beisbol","Tae kwon do"=>"Tae kwon do","Canto"=>"Canto","Basquetbol"=>"Basquetbol","Natación"=>"Natación","Box"=>"Box","Tenis de mesa"=>"Tenis de mesa","Ajedrez"=>"Ajedrez","Halterofilia"=>"Halterofilia","Atletismo"=>"Atletismo"];
    }

    public static function list_profession()
    {
        return ["Músico"=>"Músico","Profesor"=>"Profesor","Médico"=>"Médico","Enfermero"=>"Enfermero","Policía"=>"Policía","Militar"=>"Militar","Bombero"=>"Bombero","Abogado"=>"Abogado","Juez"=>"Juez","Fiscal"=>"Fiscal","Funcionario de gobierno"=>"Funcionario de gobierno","Empresario"=>"Empresario","Gerente"=>"Gerente","Director"=>"Director","Ejecutivo"=>"Ejecutivo","Profesional técnico"=>"Profesional técnico","Profesional especializado"=>"Profesional especializado","Trabajador manual"=>"Trabajador manual","Trabajador no calificado"=>"Trabajador no calificado","Artista"=>"Artista","Escritor"=>"Escritor","Periodista"=>"Periodista","Deportista"=>"Deportista","Religioso"=>"Religioso","Voluntario"=>"Voluntario","Otro/a"=>"Otro/a"];
    }

    public static function list_blood_type()
    {
        return ['A+'=>'A+','A-'=>'A-','B+'=>'B+','B-'=>'B-','O+'=>'O+','O-'=>'O-'];
    }

    public static function list_relationship()
    {
        return ['Madre'=>'Madre','Padre'=>'Padre','Hermano(a)'=>'Hermano(a)','Abuelo(a)'=>'Abuelo(a)','Otro'=>'Otro'];
    }

    public static function list_laterality()
    {
        return ['IZQUIERDA'=>'IZQUIERDA','DERECHA'=>'DERECHA'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function censuses()
    {
        return $this->belongsTo(Census::class, 'ci_estudiant');
    }

    public function getDayAttribute()
    {
        return ($this->date_birth) ? Carbon::parse($this->date_birth)->format('d') : null;
    }
    public function getMonthAttribute()
    {
        return ($this->date_birth) ? Carbon::parse($this->date_birth)->format('m') : null;
    }
    public function getYearAttribute()
    {
        return ($this->date_birth) ? Carbon::parse($this->date_birth)->format('Y') : null;
    }

    
}
