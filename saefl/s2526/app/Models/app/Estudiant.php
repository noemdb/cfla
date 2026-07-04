<?php

namespace App\Models\app;

use App\Models\app\Estudiante\Enrollment;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use App\Models\app\Pescolar\Grado;

//trait
use App\Models\app\Estudiante\Functions\Estudiant\Administrativo;
use App\Models\app\Estudiante\Functions\Estudiant\Admon;
use App\Models\app\Estudiante\Functions\Estudiant\Control;
use App\Models\app\Estudiante\Functions\Estudiant\Exchanges;

use App\Models\app\Estudiante\Functions\Estudiant\Relations;
use App\Models\app\Estudiante\Functions\Estudiant\Scope;

use App\User;
use App\Models\app\Estudiante\Functions\Estudiant\Boletins;
use App\Models\app\Estudiante\Functions\Estudiant\CommunityActions;
use App\Models\app\Estudiante\Functions\Estudiant\Enrollments;
use App\Models\app\Estudiante\Functions\Estudiant\Hnotas;
use App\Models\app\Estudiante\Functions\Estudiant\Incidents;
use App\Models\app\Estudiante\Functions\Estudiant\Inscripcions;
use App\Models\app\Estudiante\Functions\Estudiant\Lapsos;
use App\Models\app\Estudiante\Functions\Estudiant\Lists;
use App\Models\app\Estudiante\Functions\Estudiant\Pensums;

use App\Models\app\Estudiante\Functions\Estudiant\Planpago;
use App\Models\app\Estudiante\Functions\Estudiant\Prosecucions;
use App\Models\app\Estudiante\Functions\Estudiant\RegistroTitulo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;

class Estudiant extends Model
{
    protected $fillable = [
        'user_id',
        'planpago_id',
        'grado_inicial_id',
        'seccion_inicial',
        'type_ci_id',
        'ci_estudiant',
        'ci_estudiant_temp',
        'lastname',
        'name',
        'gender',
        'date_birth',
        'city_birth',
        'town_hall_birth',
        'state_birth',
        'country_birth',
        'dir_address',
        'phone',
        'cellphone',
        'email',
        'gsemail',
        'representant_ci',
        'representant_id',
        'status_active',
        'status_notice',
        'status_blacklist',
        'obs_resumen_final',
        'token',
        'status_prosecution',
        'date_prosecution',
        'count_passes',
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
        'status_notice' => 'Considerado para el envío de notificaciones',
        'obs_resumen_final' => 'Obs. Resumen Final',
        'token' => 'Token de autenticación',
        'status_prosecution' => 'Proseguir/continuar al siguiente periodo escolar',
        'date_prosecution' => 'Fecha de la confirmación de la prosecución al siguiente periodo escolar',
        'count_passes' => 'Cantidad de Pases',
    ];

    protected $appends = ['exchange_ammount_expire_bill'];

    use SoftDeletes;

    //Functions trait
    use Relations;
    use Scope;
    use Lists;
    use Admon;
    use Control;

    use Inscripcions;
    use Enrollments;
    use Pensums;

    use Boletins;
    use Lapsos;
    use RegistroTitulo;

    use Hnotas;

    use Administrativo;
    use Planpago;
    use Exchanges;

    use Incidents;

    use Prosecucions;

    use CommunityActions;

    // app/Models/Estudiant.php
    public function boletinPdfUrl()
    {
        return route('informe.notas.token', ['token' => $this->token]);
    }


    public static function getEstudiantsFormaly($peducativo_id = 3)
    {
        return Estudiant::select('estudiants.*', 'grados.id as grado_id', 'seccions.id as seccion_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('peducativos.id', $peducativo_id) // Inicial = 1, Primaria = 2, Media General = 3
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('representants.status_active', 'true')
            ->get();
    }

    public static function getEstudiantsFormalyAll($peducativo_id = NULL)
    {
        return Estudiant::select('estudiants.*', 'grados.id as grado_id', 'seccions.id as seccion_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->when($peducativo_id !== null, function ($query) use ($peducativo_id) {
                return $query->where('peducativos.id', $peducativo_id);
            })
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('representants.status_active', 'true')
            ->get();
    }

    public function getProfesorGuiaAttribute()
    {
        return Profesor::select('profesors.*')
            ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->where('inscripcions.estudiant_id', $this->id)
            ->first()
        ;
    }

    public function getTypeCiCodeAttribute()
    {
        return ($this->type_ci) ? $this->type_ci->code : null;
    }

    public function getProfileAttribute()
    {
        $user = $this->user;
        $profile = ($user) ? $user->profile : null;
        return $profile;
    }

    public function getUrlImgAttribute()
    {
        $url_img = $this->load_url_img;
        $user = $this->user; //dd($user);
        $profile = ($user) ? $user->profile : null;
        return ($profile) ? $profile->url_img : null;
        // return $url_img;
    }

    public function getLoadUrlImgAttribute()
    {
        $user = $this->user;
        $profile = ($user) ? $user->profile : null;
        $url_img = ($profile) ? $profile->url_img : null;
        if (!$url_img || $url_img = 'images/avatar/user_default.png') {
            $erollment = Enrollment::where('ci_estudiant', $this->ci_estudiant)->first();
            $photo = ($erollment) ? $erollment->photo : null;
            if ($photo) {
                $url_img = $erollment->photo_url;
                if ($url_img) {
                    $profile = $this->profile;
                    if ($profile) {
                        $profile->url_img = $url_img;
                        $profile->save();
                        return $url_img;
                    }
                }
            }
        }
    }

    public function getPhotoPdfAttribute()
    {
        $url_img = $this->url_img;
        $erollment = Enrollment::where('ci_estudiant', $this->ci_estudiant)->first();
        $photo_url = null;
        if ($erollment) {
            return ($erollment->photo_url) ? $erollment->photo_url : $this->logo;
        }
    }

    public function getPhotoUrlAttribute()
    {
        $erollment = Enrollment::where('ci_estudiant', $this->ci_estudiant)->first();
        $photo_url = null;
        if ($erollment) {
            return ($erollment->photo_url) ? $erollment->photo_url : $this->logo;
        }
    }

    public function getLogoAttribute()
    {
        $url_img = $this->load_url_img;
        $user = $this->user; //dd($user);
        $profile = ($user) ? $user->profile : null; //dd($profile);
        $logo = ($profile) ? $profile->url_img : 'images/avatar/estudiant/neutral.png';
        if ($logo == 'images/avatar/user_default.png') {
            $gender = $this->gender;
            $logo = ($gender == 'Masculino') ? 'images/avatar/estudiant/male.png' : 'images/avatar/estudiant/female.png';
        }
        return $logo;
    }

    public function setCreateUserGetId($area = "ESTUDIANTIL", $rol = "ESTUDIANTE")
    {
        if (empty($this->user_id)) {
            $arr_name = explode(" ", $this->name);
            $arr_lastname = explode(" ", $this->lastname);

            $firstName = (array_key_exists(0, $arr_name)) ? $arr_name[0] : null;
            $firstChar = ($firstName) ? $firstName[0] : Str::random(1);
            $lastName = (array_key_exists(0, $arr_lastname)) ? $arr_lastname[0] : Str::random(8);

            $user = replace_tilde(strtolower($firstChar . $lastName));
            do {
                $ident = rand(100, 1000);
                $username = $user . $ident;
                $q = User::where('username', $username)->first();
            } while ($q);

            $sentinela = Carbon::now()->timestamp . '@saefl.test';
            $email = ($this->email) ? $this->email : $sentinela;
            $user_email = User::where('email', $email)->first();
            if ($user_email) {
                $email = $username . '@saefl.test';
                $user_email = User::where('email', $email)->first();
                if ($user_email) {
                    $email = $sentinela;
                }
            }
            $password = bcrypt($username); //dd( $user, $email, $password);

            $id = DB::table('users')->insertGetId([
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'is_active' => 'enable',
                'created_at' => Carbon::now(),
                'remember_token' => Str::random(10),
            ]);
            DB::table('profiles')->insert([
                'firstname' => $lastName,
                'lastname' => $firstName,
                'url_img' => "images/avatar/user_default.png",
                'user_id' => $id,
                'created_at' => Carbon::now(),
            ]);
            DB::table('rols')->insert([
                'area' => $area,
                'rol' => $rol,
                'descripcion' => "Estudiante de la institución",
                'finicial' => Carbon::now()->year . "0101",
                'ffinal' => (Carbon::now()->year + 1) . "1231",
                'user_id' => $id,
                'created_at' => Carbon::now(),
            ]);
            $update = Estudiant::where('id', $this->id)->update(['user_id' => $id]); //dd(Estudiant::find($this->id));
            return $id;
        }
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

    public function getGenderSmAttribute()
    {
        return (isset($this->gender)) ? $this->gender[0] : null;
    }

    public function getFullName2Attribute()
    {
        return $this->name . ' ' . $this->lastname;
    }

    public static function getGrado($grado_id)
    {
        $grado = Grado::Where('id', $grado_id)->first();
        if (isset($grado)) {
            return $grado->name;
        } else {
            return '...';
        }
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

    public function desactive()
    {
        $this->fill(['status_active' => 'false']);
        $this->save();
    }

    public function getStatusDeleteAttribute()
    {
        $inscripcion = $this->getInscripcion();
        $administrativa = $this->getAdministrativa();
        return ($inscripcion || $administrativa) ? false : true;
    }

    public function getPestudioAttribute()
    {
        $inscripcion = ($this->inscripcion) ? $this->inscripcion : null;
        $seccion = ($inscripcion) ? $inscripcion->seccion : null;
        $grado = ($seccion) ? $seccion->grado : null;
        $pestudio = ($grado) ? $grado->pestudio : null;
        return $pestudio;
    }

    public function getStatusBaremoAttribute()
    {
        $pestudio = $this->pestudio;
        return ($pestudio) ? $pestudio->status_baremo : false;
    }

    public static function getPestudioNext($pestudio_id)
    {
        $pestudio = Pestudio::where('id', '>', $pestudio_id)->where('status_active', 'true')->orderBy('id', 'asc')->first();
        return $pestudio;
    }

    public static function getGradoNext($grado_id)
    {
        $pestudio = Grado::where('id', '>', $grado_id)->where('status_active', 'true')->orderBy('id', 'asc')->first();
        return $pestudio;
    }

    public function getGrupoEstableAttribute()
    {
        return $this->inscripcion->grupo_estable;
    }

    public function getBrothersFormalyAttribute()
    {
        $brothers_formaly =
            Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->where('estudiants.status_active', 'true')
            ->where('estudiants.representant_id', $this->representant->id)
            ->where('estudiants.id', '<>', $this->id)
            ->whereNull('estudiants.deleted_at')
            ->get();
        return $brothers_formaly;
    }

    public function getHasEifinalkAttribute()
    {
        return $this->eifinalks()->exists();
    }

    public function getEifinalkIdAttribute()
    {
        $eifinalk = $this->eifinalks()->first();
        return $eifinalk ? $eifinalk->id : null;
    }

    // En App\Models\Estudiant.php
    public function hasEifinalkForLapso($lapsoId)
    {
        return $this->eifinalks()
            ->whereHas('pevaluacion', function ($query) use ($lapsoId) {
                $query->where('lapso_id', $lapsoId);
            })->exists();
    }

}
