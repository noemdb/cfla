<?php

namespace App\Models\app\Academy;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lapso extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'code_sm', 'name',
        'finicial', 'ffinal', 'academic_start_date', 'date_cutnote',
        'date_start_census', 'time_start_census',
        'date_end_census', 'time_end_census',
        'date_preclosing', 'time_preclosing',
        'status_last',
    ];

    protected $casts = [
        'finicial' => 'date:Y-m-d',
        'ffinal' => 'date:Y-m-d',
        'academic_start_date' => 'date:Y-m-d',
        'date_cutnote' => 'date:Y-m-d',
        'date_start_census' => 'date:Y-m-d',
        'date_end_census' => 'date:Y-m-d',
        'date_preclosing' => 'date:Y-m-d',
    ];

    protected $table = 'lapsos';

    public function profesor_guias()
    {
        return $this->hasMany(ProfesorGuia::class, 'lapso_id');
    }

    public function pevaluacions()
    {
        return $this->hasMany(Pevaluacion::class, 'lapso_id');
    }

    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('lapsos.status_last', $flag);
    }

    public function getFullNameAttribute()
    {
        return '[' . $this->code . '] ' . $this->name;
    }

    public function getIsCurrentAttribute()
    {
        $today = Carbon::now()->format('Y-m-d');
        if (! $this->finicial || ! $this->ffinal) {
            return false;
        }
        $finicial = $this->finicial instanceof Carbon ? $this->finicial->format('Y-m-d') : $this->finicial;
        $ffinal = $this->ffinal instanceof Carbon ? $this->ffinal->format('Y-m-d') : $this->ffinal;
        return $finicial <= $today && $ffinal >= $today;
    }

    /**
     * Retorna el lapso cuya fecha actual cae dentro de su rango (finicial - ffinal).
     * Si ningún lapso coincide, retorna el primero registrado como fallback.
     */
    public static function current($fecha = null)
    {
        $fecha = $fecha ?: Carbon::now()->format('Y-m-d');
        $lapso = self::whereDate('finicial', '<=', $fecha)
            ->whereDate('ffinal', '>=', $fecha)
            ->orderBy('id')
            ->first();

        return $lapso ?: self::orderBy('id')->first();
    }
}
