<?php

namespace App\Models\app\Instrument;

use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagAnswer extends Model
{
    protected $table = 'diag_answers';

    protected $fillable = [
        'estudiant_id',
        'question_id',
        'session_id',
        'respuesta',
        'valor_numerico',
        'completado_at',
    ];

    public function question()
    {
        return $this->belongsTo(DiagQuestion::class, 'question_id');
    }

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(DiagOption::class, 'option_id');
    }
}
