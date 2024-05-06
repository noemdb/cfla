<?php

namespace App\Models\app\Academy\Interrogation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',
        'text',
        'observations',
    ];

    const COLUMN_COMMENTS = [
        'interview_id' => 'Identificador de la entrevista',
        'text' => 'Pregunta',
        'observations' => 'Observaciones',
    ];
    
    public function interview() { return $this->belongsTo(Interview::class,'interview_id'); }
    public function interview_answers() { return $this->hasMany(InterviewAnswer::class,'question_id'); }

}
