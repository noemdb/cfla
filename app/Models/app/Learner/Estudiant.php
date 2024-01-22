<?php

namespace App\Models\app\Learner;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Admon\Administrativa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Estudiant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'planpago_id', 'grado_inicial_id', 'seccion_inicial', 'type_ci_id', 'ci_estudiant', 'ci_estudiant_temp', 'lastname', 'name', 'gender',
        'date_birth', 'city_birth', 'town_hall_birth', 'state_birth', 'country_birth', 'dir_address', 'phone', 'cellphone', 'email', 'gsemail', 'representant_ci',
        'representant_id', 'status_active', 'status_blacklist'
    ];
    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'planpago_id' => 'Plan de pago',
        'grado_inicial_id' => 'Grado Inicial',
        'seccion_inicial' => 'Sección Inicial',
        'type_ci_id' => 'Tipo de Cédula',
        'ci_estudiant' => 'Número de Cédula',
        'ci_estudiant_temp' => 'Cédula temporal',
        'name' => 'Nombre',
        'lastname' => 'Apellido',
        'fullname' => 'Nombre',
        'gender' => 'Genero',
        'date_birth' => 'Fecha de nacimiento',
        'city_birth' => 'Ciudad de nacimiento',
        'town_hall_birth' => 'Municipio de nacimiento',
        'state_birth' => 'Estado de nacimiento',
        'country_birth' => 'País de nacimiento',
        'dir_address' => 'Dirección de residencia',
        'phone' => 'Número de teléfono fijo',
        'cellphone' => 'Número de teléfono celular',
        'email' => 'Correo electrónico',
        'gsemail' => 'Correo electrónico Clases Virtuales',
        'representant_ci' => 'Observaciones',
        'representant' => 'Representante',
        'status_blacklist' => 'Lista negra',
        'status_active' => 'Estado del Estudiante',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function representant()
    {
        return $this->belongsTo(Representant::class, 'representant_id');
    }
    public function inscripcion()
    {
        return $this->hasOne(Inscripcion::class, 'estudiant_id');
    }
    public function administrativa()
    {
        return $this->hasOne(Administrativa::class, 'estudiant_id');
    }

    public function getShortNameAttribute()
    {
        $arr_name = explode(" ", $this->name);
        $arr_lastname = explode(" ", $this->lastname);
        $firstName = (array_key_exists(0, $arr_name)) ? $arr_name[0] : null;
        $lastName = (array_key_exists(0, $arr_lastname)) ? $arr_lastname[0] : Str::random(8);
        return "{$lastName} {$firstName}";
    }

    public function getFullNameAttribute()
    {
        return "{$this->lastname} {$this->name}";
    }

    public function getPestudioAttribute()
    {
        $inscripcion = ($this->inscripcion) ? $this->inscripcion : null;
        $seccion = ($inscripcion) ? $inscripcion->seccion : null;
        $grado = ($seccion) ? $seccion->grado : null;
        $pestudio = ($grado) ? $grado->pestudio : null;
        return $pestudio;
    }

    public function getAgeAttribute()
    {
        return ($this->date_birth <> '0000-00-00') ? Carbon::parse($this->date_birth)->age : '-';
    }

    public function getAgeDate($dateEnd)
    {
        $date_end = Carbon::parse($dateEnd);
        $date_birth = Carbon::parse($this->date_birth);
        $age = $date_end->DiffInYears($date_birth); //dd($age);
        return $age;
    }

    public function getNacionalidadAttribute()
    {
        $country_birth = (!empty($this->country_birth)) ? strpos($this->country_birth, 'VENEZUELA') : null;
        return ($country_birth === false) ? 'E' : 'V';
    }

    public function getDayBirthAttribute()
    {
        return ($this->date_birth <> '0000-00-00') ? Carbon::parse($this->date_birth)->format('d') : null;
    }
    public function getMonthBirthAttribute()
    {
        return ($this->date_birth <> '0000-00-00') ? Carbon::parse($this->date_birth)->format('m') : null;
    }
    public function getYearBirthAttribute()
    {
        return ($this->date_birth <> '0000-00-00') ? Carbon::parse($this->date_birth)->format('Y') : null;
    }

    public function getGenderSmAttribute()
    {
        return (isset($this->gender)) ? $this->gender[0] : null;
    }

    public function getSeccionAttribute()
    {
        $inscripcion = $this->inscripcion;
        if (!empty($inscripcion)) {
            $seccion = $this->seccion;
            if (!empty($seccion)) {
                if ($seccion->status_active == "true") {
                    return $seccion;
                }
            }
        }
    }

    public function getGradoAttribute()
    {
        $inscripcion = $this->inscripcion;
        if (!empty($inscripcion)) {
            $seccion = $this->seccion;
            if (!empty($seccion)) {
                if ($seccion->status_active == "true" && $seccion->status_inscription_affects == "true") {
                    $grado = $seccion->grado;
                    return ($grado->status_active) ? $grado : null;
                }
            }
        }
    }

    public function getFullInscripcionAttribute()
    {
        if (!empty($this->inscripcion)) {
            $inscripcion = $this->inscripcion;
            if (!empty($inscripcion->seccion)) {
                $seccion = $inscripcion->seccion;
                if ($seccion) {
                    if ($seccion->status_active == "true" && $seccion->status_inscription_affects == "true") {
                        if (!empty($seccion->grado)) {
                            $grado = $seccion->grado;
                            return "{$grado->name} {$seccion->name}";
                        }
                    }
                }
            }
        }
    }
}
