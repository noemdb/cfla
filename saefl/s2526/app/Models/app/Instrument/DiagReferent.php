<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;

class DiagReferent extends Model
{
    protected $table = 'diag_referents';

    protected $fillable = [
        'pestudio_id',
        'name',
        'code',
        'version',
        'description',
        'active',
    ];

    const COLUMN_COMMENTS = [
        'name' => 'Nombre',
        'code' => 'Código/Resolución',
        'version' => 'Versión',
        'description' => 'Descripción',
        'active' => 'Activo',
    ];

    public function competencies()
    {
        return $this->hasMany(DiagCompetency::class, 'referent_id');
    }

    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
}
