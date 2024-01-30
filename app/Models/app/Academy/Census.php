<?php

namespace App\Models\app\Academy;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Census extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','ci_estudiant','lastname','name','cellphone','gender','date_birth',
        'age','town_hall_birth','state_birth','country_birth','dir_address','pestudio_id',
        'grado_id','grupo_estable_id','pending_matter','blood_type','weight','height','laterality','institution',
        'order_born','group_family','status_brother','literal','ci_representant','name_representant',
        'relationship','profession_representant','phone_representant','cellphone_representant','email_representant','twitter','instagram','status_admite'
    ];
    protected $dates = ['created_at','updated_at'];

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

    public function enrollments()
    {
        return $this->belongsTo(Enrollment::class, 'ci_estudiant');
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

    public function getFullNameAttribute()
    {
        return "{$this->lastname} {$this->name}";
    } 

    public static function getStatusEstudiantEnable($ci)
    {
        $census = Census::where('ci_estudiant', $ci)->whereDoesntHave('enrollments')->first();
        return ($census) ? true : false;
    }

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'ci_estudiant' => 'Cédula de identidad',
        'lastname' => 'Apellidos',
        'name' => 'Nombres',
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
        'status_brother' => 'Hermanos en el colegio',
        'literal' => 'Literal de promosión, (sólo primaria)',

        'ci_representant' => 'Cédula de identidad',
        'name_representant' => 'Nombre del representante',
        'lastname_representant' => 'Apellidos',
        'relationship' => 'Parentesco',
        'profession_representant' => 'Profesión',
        'phone_representant' => 'Número de teléfono fijo',
        'cellphone_representant' => 'Número de teléfono celular [WhatsApp, Telegram]',
        'email_representant' => 'Correo electrónico',
        'twitter' => 'Usuario Twitter',
        'instagram' => 'Usuario Instagram',
        'created_at' => 'F.Registro',
        'status_admite' => 'Admitido',
    ];
}
