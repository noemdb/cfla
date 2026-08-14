<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method bool isCoordinacion()
 * @method bool isDirector()
 * @method bool isLeadership()
 * @method bool isProfesor()
 * @method bool isStudent()
 * @method bool isAdminOrDiagnostic()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'is_active',
        'is_admin',
        'is_planner',
        'is_diagnostic',
        'is_profesor',
        'is_coordinacion',
        'is_leadership',
        'is_director',
        'is_student',
        'number_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_planner' => 'boolean',
        'is_diagnostic' => 'boolean',
        'is_profesor' => 'boolean',
        'is_coordinacion' => 'boolean',
        'is_leadership' => 'boolean',
        'is_director' => 'boolean',
        'is_student' => 'boolean',
    ];

    public function profile()
    {
        return $this->hasOne(\App\Models\sys\Profile::class);
    }

    public function estudiant()
    {
        return $this->hasOne(\App\Models\app\Learner\Estudiant::class, 'user_id');
    }

    public function getFullNameAttribute()
    {
        if ($this->relationLoaded('profile') && $this->profile) {
            return trim($this->profile->firstname.' '.$this->profile->lastname);
        }

        $user = DB::table('users')
            ->selectRaw("CONCAT(profiles.firstname, ' ', profiles.lastname) as fullname")
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('users.id', $this->id)
            ->first();

        return ($user) ? $user->fullname : null;
    }

    public function isAdminOrDiagnostic()
    {
        return $this->is_admin || $this->is_diagnostic;
    }

    public function isProfesor()
    {
        return $this->is_profesor ?? false;
    }

    public function isStudent(): bool
    {
        return $this->is_student ?? false;
    }

    public function isLeadership(): bool
    {
        // Leer el raw attribute directamente para NO pasar por el accessor
        // getIsLeadershipAttribute(), que además incluye is_admin.
        return $this->attributes['is_leadership'] ?? false;
    }

    public function isCoordinacion(): bool
    {
        return $this->is_coordinacion ?? false;
    }

    public function isDirector(): bool
    {
        return $this->is_director ?? false;
    }

    public function getRolAttribute()
    {
        return $this->role_label;
    }

    public function getRoleLabelAttribute()
    {
        if ($this->is_admin) {
            return 'Administrador';
        }

        if ($this->is_director) {
            return 'Dirección';
        }

        if ($this->is_leadership) {
            return 'Jefe de Área';
        }

        if ($this->is_diagnostic) {
            return 'Personal de Diagnóstico';
        }

        if ($this->isCoordinacion()) {
            return 'Coordinación';
        }

        if ($this->is_planner) {
            return 'Planificación';
        }

        if ($this->isProfesor()) {
            return 'Profesor';
        }

        if ($this->is_student) {
            return 'Estudiante';
        }

        return 'Usuario Estándar';
    }

    public function getIsPlannerAttribute()
    {
        return $this->is_admin || ($this->attributes['is_planner'] ?? false);
    }

    public function getIsLeadershipAttribute()
    {
        return $this->is_admin || ($this->attributes['is_leadership'] ?? false);
    }

    public function getIsDirectorAttribute()
    {
        return $this->is_admin || ($this->attributes['is_director'] ?? false);
    }

    public function leadershipAreas()
    {
        return $this->hasMany(\App\Models\app\Academy\AreaConocimiento::class, 'leader_id');
    }

    public function lessonReads()
    {
        return $this->hasMany(UserLessonRead::class, 'user_id');
    }
}
