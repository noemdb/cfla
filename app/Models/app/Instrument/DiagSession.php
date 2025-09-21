<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Pensum;
use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagSession extends Model
{
    protected $table = 'diag_sessions';

    protected $fillable = [
        'estudiant_id',
        'pensum_id',
        'iniciado_at',
        'completado_at',
        'progreso',
        'total_preguntas',
        'activo',
    ];

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function answers()
    {
        return $this->hasManyThrough(
            DiagAnswer::class,
            Estudiant::class,
            'id', // clave foránea en estudiant
            'estudiant_id', // clave foránea en diag_answers
            'estudiant_id', // clave local en diag_sessions
            'id' // clave local en estudiant
        );
    }
}
