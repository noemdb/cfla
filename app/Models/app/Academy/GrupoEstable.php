<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoEstable extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'code_sm', 'name', 'description',
        'hour_t_week', 'hour_p_week', 'size_max',
        'observations', 'status_belongs_ins', 'status_active',
    ];

    protected $table = 'grupo_estables';

    const COLUMN_COMMENTS = [
        'code' => 'Código',
        'code_sm' => 'Código (SM)',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'hour_t_week' => 'Horas Teóricas',
        'hour_p_week' => 'Horas Prácticas',
        'size_max' => 'Cupo Máximo',
        'observations' => 'Observaciones',
        'status_belongs_ins' => 'Pertenece a la Institución',
        'status_active' => 'Activo',
    ];

    public function scopeActive($query, $flag = true)
    {
        return $query->where('status_active', $flag);
    }
}
