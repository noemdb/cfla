<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    use HasFactory;

    protected $fillable = [
        'pestudio_id', 'name', 'code', 'code_sm', 'description', 'status_active'
    ];

    const COLUMN_COMMENTS = [
        'pestudio_id' => 'Plan Estudio',
        'name' => 'Nombre',
        'code' => 'Código',
        'code_sm' => 'Código reducido',
        'description' => 'Descripción',
        'status_active' => 'Estado'
    ];

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }
    public function seccions()
    {
        return $this->hasMany(Seccion::class, 'seccion_id');
    }
}
