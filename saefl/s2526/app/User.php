<?php

namespace App;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollMain;
use App\Models\app\Poll\PollToken;
use App\Models\blog\Post;
use App\Models\sys\Cargo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;

// use App\Traits\UserSettingsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

//models
use App\Models\sys\Rol;
use App\Notifications\MyResetPassword;
use App\Models\sys\Functions\User\AssitAttendances;
use App\Models\sys\Functions\User\EvaluacionFunctions;

// use App\Models\sys\Functions\User\AssitAttendance\AssitAttendance;

class User extends Authenticatable
{
    use Notifiable, LogsActivity;
    use AssitAttendances;
    use EvaluacionFunctions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'email',
        'is_active',
        'status_update',
        'work_id',
        'card_id',
        'ident',
        'number_id',
        'worker_order',
        'mail_username',
        'mail_password',
        'mail_cc_address',
        'is_diagnostic'
    ];

    protected static $logAttributes = ['username', 'email'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    const COLUMN_COMMENTS = [
        'username' => 'Nombre de Usuario',
        'password' => 'Contraseña',
        'email' => 'Correo Electrónico',
        'is_active' => 'Activo',
        'status_update' => 'Actualizado',
        'work_id' => 'Ident. Trabajador',
        'card_id' => 'Número de Tarjeta',
        'ident' => 'Ident. Trabajador BIO',
        'number_id' => 'Cédula de Identidad',
        'fullname' => 'Nombre',
        'assit_schedule' => 'Horario del Trabajador',
        'assit_schedule_id' => 'Horario del Trabajador',
        'worker_order' => 'Orden del trabajador',
        'cargo' => 'Cargo',
        'firstname' => 'Nombres',
        'lastname' => 'Apellidos',
        'cargo_id' => 'Cargo',
        'assit_schedule_id' => 'Horario',
        'pestudios' => 'Plan de Estudio',
        'mail_username' => 'Dirección de correo ECA',
        'mail_password' => 'Contraseña de correo ECA',
        'mail_cc_address' => 'Dirección de Correo CC ECA',
        'is_diagnostic' => 'Diagnostico',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function generateToken()
    {
        $this->api_token = Str::random(60);
        $this->save();

        return $this->api_token;
    }

    // nuemro de registros en User getCountAttribute
    public function getCountAttribute()
    {
        return $this->count();
    }
    // nuemro de perfiles registrados
    public function getCountTasksAttribute()
    {
        return $this->tasks->count();
    }

    /*INI relaciones entre modelos*/
    public function profile()
    {
        return $this->hasOne('App\Models\sys\Profile');
    }

    public function rols()
    {
        return $this->hasMany('App\Models\sys\Rol');
    }
    public function administrativas()
    {
        return $this->hasMany('App\Models\app\Estudiante\Administrativa');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\sys\Task');
    }

    public function messeges()
    {
        return $this->hasMany('App\Models\sys\Messege');
    }

    public function alerts()
    {
        return $this->hasMany('App\Models\sys\Alert');
    }

    public function loginouts()
    {
        return $this->hasMany('App\Models\sys\Loginout');
    }

    public function logdbs()
    {
        return $this->hasMany('App\Models\sys\Logdb');
    }

    public function settings()
    {
        return $this->hasMany('App\Models\sys\Setting');
    }
    public function registropagos()
    {
        return $this->hasMany('App\Models\app\Planpago\RegistroPago');
    }
    public function profesor()
    {
        return $this->hasOne('App\Models\app\Pescolar\Profesor');
    }
    public function autoridad()
    {
        return $this->hasOne('App\Models\app\Institucion\Autoridad');
    }
    public function estudiant()
    {
        return $this->hasOne('App\Models\app\Estudiant');
    }
    public function representant()
    {
        return $this->hasOne('App\Models\app\Estudiante\Representant');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    /////////////////////////////////////////////////////////////////////

    public function poll_mains()
    {
        return $this->hasMany(PollMain::class, 'user_id');
    }

    public function poll_tokens()
    {
        return $this->hasMany(PollToken::class, 'user_id');
    }
    /*FIN relaciones entre modelos*/

    public function setPasswordAttribute($value)
    {
        if (! empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function getCiAttribute()
    {
        $ci = null;
        if ($this->IsRepresentant()) {
            $ci = ($this->representant) ? $this->representant->ci_representant : null;
        }
        if ($this->IsEstudiant()) {
            $ci = ($this->estudiant) ? $this->estudiant->ci_estudiant : null;
        }
        if ($this->IsProfesor()) {
            $ci = ($this->profesor) ? $this->profesor->ci_profesor : null;
        }
        if (empty($ci)) {
            $this->number_id;
        }
        return $ci;
    }

    public function getFullNameAttribute()
    {
        return ($this->profile) ? $this->profile->fullname : null;
    }
    public function getFirstNameAttribute()
    {
        return ($this->profile) ? $this->profile->firstname : null;
    }
    public function getLastNameAttribute()
    {
        return ($this->profile) ? $this->profile->lastname : null;
    }

    //is admin
    public function isAdmin()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $is_admin = $this->rols->whereIn('area', ['SISTEMA'])->Where('finicial', '<=', $fecha)->Where('ffinal', '>=', $fecha)->count();
        if ($is_admin > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmon()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $IsExpediente = $this->rols
            ->whereIn('area', ['SISTEMA', 'ADMINISTRACION'])
            ->whereIn('rol', ['ADMINISTRADOR', 'DIRECTOR', 'COORDINADOR', 'ASISTENTE'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($IsExpediente > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isControl()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $control = $this->rols
            ->whereIn('area', ['SISTEMA', 'CONTROL ESTUDIO'])
            // ->whereIn('area', ['SISTEMA','AUTORIDAD','CONTROL ESTUDIO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'DIRECTOR', 'COORDINADOR', 'ASISTENTE'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($control > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isControlDir()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $control = $this->rols
            ->whereIn('area', ['SISTEMA', 'CONTROL ESTUDIO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'DIRECTOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($control > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsCommon()
    {
        if ($this->is_active == 'disable') return false;
        $control = false;
        if ($this->isAdmin() || $this->isAdmon() || $this->isControl()) {
            $control = true;
        }
        return $control;
    }
    public function isProfesor()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'PROFESORADO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'PROFESOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsDirector()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'AUTORIDAD'])
            ->whereIn('rol', ['ADMINISTRADOR', 'DIRECTOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsControls()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'ACADEMICO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'DIRECTOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsAcademico()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'ACADEMICO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'DIRECTOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsAdmons()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'ADMINISTRATIVO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'DIRECTOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsRepresentant()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'REPRESENTANTE'])
            ->whereIn('rol', ['ADMINISTRADOR', 'REPRESENTANTE'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function IsEstudiant()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'ESTUDIANTIL'])
            ->whereIn('rol', ['ADMINISTRADOR', 'ESTUDIANTE'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsBienestar()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'BIENESTAR'])
            ->whereIn('rol', ['ADMINISTRADOR', 'COORDINADOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsEvaluacion()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'EVALUACION'])
            ->whereIn('rol', ['ADMINISTRADOR', 'COORDINADOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsProyecto()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'PROYECTO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'COORDINADOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsLeader()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'CONOCIMIENTO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'COORDINADOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsInicial()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'PROFESORADO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'INICIAL'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsPlanning()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $data = $this->rols
            ->whereIn('area', ['SISTEMA', 'ACADEMICO'])
            ->whereIn('rol', ['ADMINISTRADOR', 'COORDINADOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function IsAudit()
    {
        if ($this->is_active == 'disable') return false;
        $fecha = Carbon::now();
        $IsExpediente = $this->rols
            ->whereIn('area', ['SISTEMA', 'AUTORIDAD'])
            ->whereIn('rol', ['ADMINISTRADOR', 'SUPERVISOR'])
            ->Where('finicial', '<=', $fecha)
            ->Where('ffinal', '>=', $fecha)
            ->count();
        if ($IsExpediente > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getRolAttribute()
    {
        $fecha = Carbon::now();
        $rol = Rol::Where('user_id', $this->id)->Where('ffinal', '>=', $fecha)->Where('finicial', '<=', $fecha)->first();
        if (isset($rol))
            return $rol->rol;
    }

    public function getRolNameAttribute()
    {
        return ($this->fullrol) ? $this->fullrol->area . ' - ' . $this->fullrol->rol : null;
    }

    public function getFullRolAttribute()
    {
        $fecha = Carbon::now();
        return Rol::Where('user_id', $this->id)->Where('ffinal', '>=', $fecha)->Where('finicial', '<=', $fecha)->first();
    }

    public function getAreaAttribute()
    {
        $fecha = Carbon::now();
        $rol = Rol::Where('user_id', $this->id)->Where('ffinal', '>=', $fecha)->Where('finicial', '<=', $fecha)->first();
        if (isset($rol))
            return $rol->area;
    }

    public function getCargoAttribute()
    {
        $fecha = Carbon::now();
        $cargo = Cargo::select('cargos.*')
            ->join('rols', 'cargos.id', '=', 'rols.cargo_id')
            ->where('rols.user_id', $this->id)
            ->Where('rols.finicial', '<=', $fecha)
            ->Where('rols.ffinal', '>=', $fecha)
            ->orderBy('rols.created_at', 'desc')
            ->first();
        return $cargo;
    }

    public function getPositionAttribute()
    {
        $fecha = Carbon::now();
        return Rol::select('rols.*', 'cargos.name')
            ->join('cargos', 'cargos.id', '=', 'rols.cargo_id')
            ->join('users', 'users.id', '=', 'rols.user_id')
            ->where('rols.user_id', $this->id)
            ->Where('rols.finicial', '<=', $fecha)
            ->Where('rols.ffinal', '>=', $fecha)
            ->Where('rols.status_schedule', true)
            ->Where('users.is_active', 'enable')
            ->orderBy('rols.created_at', 'desc')
            ->first();
    }

    public function getCargoNameAttribute()
    {
        return ($this->cargo) ? $this->cargo->name : null;
    }

    public function getCargoIdAttribute()
    {
        return ($this->cargo) ? $this->cargo->id : null;
    }

    public function getAssitScheduleAttribute()
    {
        $fecha = Carbon::now();
        $assit_schedule = AssitSchedule::select('assit_schedules.*')
            ->join('rols', 'assit_schedules.id', '=', 'rols.assit_schedule_id')
            ->where('rols.user_id', $this->id)
            ->Where('rols.finicial', '<=', $fecha)
            ->Where('rols.ffinal', '>=', $fecha)
            ->orderBy('rols.created_at', 'desc')
            ->first();
        return $assit_schedule;
    }

    public function getAssitScheduleIdAttribute()
    {
        return ($this->assit_schedule) ? $this->assit_schedule->id : null;
    }


    public function hasRole()
    {
        return $this->area . ':' . $this->rol;
    }

    public static function getUsernameId($id)
    {
        $user = User::Where('id', $id)->first();
        if (isset($user)) {
            return $user->username;
        } else {
            return '...';
        }
    }

    public static function getUserId($id)
    {
        $user = User::Where('id', $id)->first();
        if (isset($user)) {
            return $user;
        } else {
            return '...';
        }
    }

    public function getUserRol()
    {
        $fecha = Carbon::now();
        $rol = Rol::Where('user_id', $this->id)
            ->Where('ffinal', '>=', $fecha)
            ->Where('finicial', '<=', $fecha)
            ->first();
        if (isset($rol)) {
            return $rol->area . ':' . $rol->rol;
        } else {
            return '...';
        }
    }

    public function getClassAttribute()
    {
        switch ($this->is_active) {
            case 'enable':
                $class = 'primary';
                break;
            case 'disable':
                $class = 'danger';
                break;
            default:
                $class = 'secondary';
                break;
        }
        return $class;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPassword($token));
    }

    public static function list_users_worker() /* usada para llenar los objetos de formularios select*/
    {
        $list = User::select('users.id')
            ->selectRaw('CONCAT(profiles.lastname, " ",profiles.firstname ) as profile_fullname')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('users.is_active', 'enable')
            ->whereNotNull('users.work_id')
            ->orderBy('profiles.lastname', 'asc')
            ->pluck('profile_fullname', 'id');
        return $list;
    }

    public static function list_users() /* usada para llenar los objetos de formularios select*/
    {
        $list = User::select('id', 'username')
            ->orderby('username', 'asc')
            ->pluck('username', 'id');
        return $list;
    }

    public static function list_users_employ() /* usada para llenar los objetos de formularios select*/
    {
        $fecha = Carbon::now();
        $datas = collect();
        $users = DB::table('users')
            ->select('users.id', 'users.username', 'rols.area', 'rols.rol', DB::raw('concat(profiles.firstname, " ",profiles.lastname ) as proFullname'))
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->Where('rols.ffinal', '>=', $fecha)
            ->Where('rols.finicial', '<=', $fecha)
            ->whereIn('rols.area', ['ADMINISTRACION', 'AUTORIDAD', 'DIRECTOR', 'ADMINISTRACION', 'CONTROL ESTUDIO', 'PROFESORADO'])
            ->whereIn('rols.rol', ['DIRECTOR', 'COORDINADOR', 'PROFESOR', 'ASISTENTE'])
            ->get();
        $areas = $users->pluck('area');
        foreach ($areas as $area) {
            $pluck = $users->where('area', $area)->pluck('proFullname', 'id');
            $datas->put($area, $pluck);
        }
        return $datas;
    }

    public function getPestudiosAttribute()
    {
        $fecha = Carbon::now();
        $pestudios = Pestudio::select('pestudios.*')
            ->join('pensums', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('users', 'users.id', '=', 'profesors.user_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->where('rols.user_id', $this->id)
            ->Where('rols.finicial', '<=', $fecha)
            ->Where('rols.ffinal', '>=', $fecha)
            ->orderBy('rols.created_at', 'desc')
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('pestudios.id')
            ->get();
        return $pestudios;
    }

    public function getPeducativosAttribute()
    {
        $peducativos = Peducativo::select('peducativos.*')
            ->where('peducativos.manager_id', $this->id)
            ->groupBy('peducativos.id')
            ->get();
        return $peducativos;
    }

    // public function getAssitScheduleAttribute()
    // {
    //     $fecha = Carbon::now();
    //     $assit_schedule = AssitSchedule::select('assit_schedules.*')
    //     ->join('rols', 'assit_schedules.id', '=', 'rols.assit_schedule_id')
    //     ->where('rols.user_id',$this->id)
    //     ->Where('rols.finicial', '<=', $fecha)
    //     ->Where('rols.ffinal', '>=', $fecha)
    //     ->first();
    //     return $assit_schedule;
    // }

    public static function user_admin()
    {
        $fecha = Carbon::now();
        $user = User::select('users.*')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->whereIn('rols.area', ['SISTEMA'])
            ->Where('rols.finicial', '<=', $fecha)
            ->Where('rols.ffinal', '>=', $fecha)
            ->first();
        return $user;
    }

    public function getPollAnswers($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $poll_answers = PollAnswer::select('poll_answers.*')
            ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->join('users', 'users.id', '=', 'poll_tokens.user_id')
            ->where('users.id', $this->id)
            ->where('poll_mains.id', $poll_main->id)
            ->get();
        return $poll_answers;
    }

    public function statusNotifiledPollToken($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $poll_token = PollToken::select('poll_tokens.*')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->join('users', 'users.id', '=', 'poll_tokens.user_id')
            ->where('poll_tokens.status_notifiled', 'true')
            ->where('users.id', $this->id)
            ->first();
        return ($poll_token) ? true : false;
    }

    public function getPollTokenId($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $poll_token = PollToken::select('poll_tokens.*')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->join('users', 'users.id', '=', 'poll_tokens.user_id')
            ->where('users.id', $this->id)
            ->where('poll_mains.id', $poll_main->id)
            ->first();
        return $poll_token;
    }

    public function activityLogs()
    {
        return $this->hasMany(\Spatie\Activitylog\Models\Activity::class, 'causer_id');
    }
}

// ->whereIn('area', ['SISTEMA','ADMINISTRACION'])
// ->whereIn('rol', ['ADMINISTRADOR','DIRECTOR','COORDINADOR','ASISTENTE'])

// ->whereIn('area', ['SISTEMA','CONTROL ESTUDIO'])
// ->whereIn('rol', ['ADMINISTRADOR','DIRECTOR','COORDINADOR','ASISTENTE'])

// ->whereIn('area', ['SISTEMA','PROFESORADO'])
// ->whereIn('rol', ['ADMINISTRADOR','PROFESOR'])

// ->whereIn('area', ['SISTEMA','AUTORIDAD'])
// ->whereIn('rol', ['ADMINISTRADOR','DIRECTOR'])
