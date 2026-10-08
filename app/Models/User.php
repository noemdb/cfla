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
 * @method bool isInicial()
 */
class User extends Authenticatable implements \App\Contracts\Auditable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * NO existe role_id: los roles son flags booleanos.
     * Excluidos a propósito: password, remember_token, api_token, number_id.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'username', 'email', 'is_active',
            'is_admin', 'is_planner', 'is_diagnostic', 'is_profesor', 'is_inicial',
            'is_coordinacion', 'is_leadership', 'is_director', 'is_student',
        ];
    }

    public function maskedAuditFields(): array
    {
        return ['email'];
    }

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
        'is_inicial',
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
        'last_seen_at' => 'datetime',
        'password' => 'hashed',
        'is_planner' => 'boolean',
        'is_diagnostic' => 'boolean',
        'is_profesor' => 'boolean',
        'is_inicial' => 'boolean',
        'is_coordinacion' => 'boolean',
        'is_leadership' => 'boolean',
        'is_director' => 'boolean',
        'is_student' => 'boolean',
    ];

    public function profile()
    {
        return $this->hasOne(\App\Models\sys\Profile::class);
    }

    /**
     * Ficha de profesor asociada al usuario.
     *
     * Relación inversa de la que usa el módulo de Educación Inicial: el
     * docente del módulo se identifica por su `users.is_inicial`, y desde ahí
     * se llega a su `profesors.id`, que es la FK de cabecera de los planes,
     * proyectos, evaluaciones e informes finales.
     */
    public function profesor()
    {
        return $this->hasOne(\App\Models\app\Academy\Profesor::class, 'user_id');
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

    /**
     * Acceso al módulo de Educación Inicial (pestudio 6).
     *
     * Lee el atributo crudo para NO HEREDAR el fallback de otros flags: si se
     * usara `$this->is_inicial`, un administrador caería en el grupo "Inicial"
     * de la etiqueta de rol y se mostraría como docente de Inicial en la
     * navbar, que es exactamente el tipo de solapamiento que seam el módulo.
     *
     * Devuelve `false` (no null) mientras la migración
     * `add_is_inicial_to_users_table` no se haya corrido.
     */
    public function isInicial(): bool
    {
        return $this->attributes['is_inicial'] ?? false;
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

    public function getRoleLabelAttribute(): string
    {
        // Prioridad cuando el usuario tiene varios roles (requerimiento navbar):
        // 'is_admin', 'is_planner','is_coordinacion', 'is_leadership', 'is_profesor', 'is_director', 'is_student', 'is_diagnostic'
        // Se lee el atributo crudo para no heredar el fallback de is_planner/is_leadership/is_director que incluyen is_admin.
        $a = $this->attributes;

        if (! empty($a['is_admin'])) {
            return 'Administrador';
        }
        if (! empty($a['is_planner'])) {
            return 'Planificación';
        }
        if (! empty($a['is_coordinacion'])) {
            return 'Coordinación';
        }
        if (! empty($a['is_leadership'])) {
            return 'Jefe de Área';
        }
        if (! empty($a['is_profesor'])) {
            return 'Profesor';
        }
        if (! empty($a['is_inicial'])) {
            return 'Educación Inicial';
        }
        if (! empty($a['is_director'])) {
            return 'Dirección';
        }
        if (! empty($a['is_student'])) {
            return 'Estudiante';
        }
        if (! empty($a['is_diagnostic'])) {
            return 'Personal de Diagnóstico';
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
