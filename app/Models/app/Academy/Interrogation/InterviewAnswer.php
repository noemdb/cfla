<?php

namespace App\Models\app\Academy\Interrogation;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function testimonials($limit=3)
    {
        return InterviewAnswer::query()
        ->select('interview_answers.*')
        ->selectRaw('GROUP_CONCAT(interview_answers.text SEPARATOR ", ") AS "alltext"')
        ->join('interview_questions', 'interview_questions.id', '=', 'interview_answers.question_id')
        ->join('interviews', 'interviews.id', '=', 'interview_questions.interview_id')
        ->groupBy('interview_answers.user_id')
        ->where('interviews.id',1) //testimonials
        ->limit($limit)
        ->get();
    }

    public function getTestimonialFullTextAttribute()
    {

    }

    public function getFullNameAttribute()
    {
        $user = $this->user;
        return ($user) ? $user->fullname : null ;
    }

}
