<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    public function getFullNameAttribute()
    {
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

    public function getRoleLabelAttribute()
    {
        if ($this->is_admin) {
            return 'Administrador';
        }

        if ($this->is_diagnostic) {
            return 'Personal de Diagnóstico';
        }

        return 'Usuario Estándar';
    }
}
