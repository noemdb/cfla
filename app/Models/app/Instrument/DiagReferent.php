<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Pestudio;
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

    public function competencies()
    {
        return $this->hasMany(DiagCompetency::class, 'referent_id');
    }

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class);
    }

    const COLUMN_COMMENTS = [
        'name' => 'Nombre del referente',
        'code' => 'Código del referente',
        'version' => 'Versión',
        'description' => 'Descripción',
        'active' => 'Estado activo',
        'pestudio_id' => 'Plan de estudio',
    ];
}
