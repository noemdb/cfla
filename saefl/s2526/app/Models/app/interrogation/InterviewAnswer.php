<?php

namespace App\Models\app\interrogation;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'text',
    ];

    const COLUMN_COMMENTS = [
        'user_id' => 'Identificador del usuario',
        'question_id' => 'Identificador de la pregunta',
        'text' => 'Respuesta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function interview_question()
    {
        return $this->belongsTo(InterviewQuestion::class, 'question_id');
    }

    public function getInterviewAttribute()
    {
        return ($this->interview_question) ? $this->interview_question->interview : null; // Accesses interview through "interview" relationship
    }

    public function  getInterviewAttendeeAttribute()
    {
        return $this->hasOne(InterviewAttendee::class, 'user_id', 'user_id')
            ->where('interview_id', $this->interview_question->interview_id)->first(); // Ensure attendee belongs to the same interview
    }
}
