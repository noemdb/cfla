<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grado extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pestudio_id', 'name', 'code', 'code_sm', 'description',
        'status_active', 'hour_social', 'total_hour_social', 'order',
    ];

    protected $table = 'grados';

    const COLUMN_COMMENTS = [
        'pestudio_id' => 'Plan Estudio',
        'name' => 'Nombre',
        'code' => 'Código',
        'code_sm' => 'Código reducido',
        'description' => 'Descripción',
        'status_active' => 'Estado',
        'hour_social' => 'Horas sociales requeridas',
        'total_hour_social' => 'Horas sociales totales',
        'order' => 'Orden',
    ];

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function seccions()
    {
        return $this->hasMany(Seccion::class, 'grado_id');
    }

    public function pensums()
    {
        return $this->hasMany(Pensum::class, 'grado_id');
    }

    public function activeSeccions()
    {
        return $this->seccions()->where('status_active', true)->get();
    }

    // Scopes
    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('grados.status_active', $flag);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return '[' . $this->code . '] ' . $this->name;
    }
}
