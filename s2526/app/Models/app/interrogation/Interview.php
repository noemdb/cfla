<?php

namespace App\Models\app\interrogation;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'status',
    ];

    const COLUMN_COMMENTS = [
        'user_id' => 'Identificador del usuario',
        'name' => 'Nombre de la entrevista',
        'description' => 'Descripción',
        'status' => 'Estado',
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id');}

    public function interview_questions() { return $this->hasMany(InterviewQuestion::class,'interview_id'); }

    public function getAnsweredQuestions($user_id)
    {
        return InterviewQuestion::query()
            ->whereHas('interview_answers', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderBy('interview_questions.text')
            ->get();
    }

    public function getUnansweredQuestions($user_id)
    {
        return InterviewQuestion::query()
            ->where('interview_questions.interview_id',$this->id)
            ->whereDoesntHave('interview_answers', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderBy('interview_questions.text')
            ->get();
    }
}
