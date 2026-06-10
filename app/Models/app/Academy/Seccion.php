<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seccion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grado_id', 'name', 'description', 'amount_student', 'observation',
        'status_active', 'comment_final', 'status_inscription_affects'
    ];

    protected $table = 'seccions';

    const COLUMN_COMMENTS = [
        'grado_id' => 'Grado del Plan de Estudio',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'amount_student' => 'Cantidad de Estudiantes',
        'observation' => 'Observaciones',
        'status_active' => 'Estado',
        'comment_final' => 'Observaciones Resumen Final',
        'status_inscription_affects' => 'Contabiliza Inscripción'
    ];

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function inscripcions()
    {
        return $this->hasMany(Inscripcion::class, 'seccion_id');
    }

    public function profesor_guias()
    {
        return $this->hasMany(ProfesorGuia::class, 'seccion_id');
    }

    public function pevaluacions()
    {
        return $this->hasMany(Pevaluacion::class, 'seccion_id');
    }

    public function pestudio()
    {
        return $this->grado->pestudio();
    }

    // Scopes
    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('seccions.status_active', $flag);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        $gradoName = $this->grado ? $this->grado->name : '—';
        return "Secc. {$this->name} ({$gradoName})";
    }

    /**
     * Retorna las secciones activas de un grado, formateadas para select.
     */
    public static function list_seccion_grado($grado_id)
    {
        return self::where('grado_id', $grado_id)
            ->where('status_active', true)
            ->pluck('name', 'id');
    }
}
