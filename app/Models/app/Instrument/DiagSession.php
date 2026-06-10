<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagSession extends Model
{
    use HasFactory;

    protected $table = 'diag_sessions';

    protected $fillable = [
        'estudiant_id',
        'pensum_id',
        'iniciado_at',
        'completado_at',
        'progreso',
        'total_preguntas',
        'activo',
        'diag_main_id',
        'lapso_id',
        'status',
    ];

    protected $dates = [
        'iniciado_at',
        'completado_at',
    ];

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function diagMain()
    {
        return $this->belongsTo(DiagMain::class, 'diag_main_id');
    }

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function answers()
    {
        return $this->hasMany(DiagAnswer::class, 'session_id');
    }
}
