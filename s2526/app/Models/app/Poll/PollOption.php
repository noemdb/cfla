<?php

namespace App\Models\app\Poll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_question_id','text','description','observations','body','image'
    ];

    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'poll_question_id' => 'Pregunta',
        'text' => 'Opción',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'image' => 'Imagen',
        'body' => 'Explicaciones',
        'image' => 'Imagen',
        'poll_main_id' => 'Consulta',
    ];

    public function poll_question()
    {
        return $this->belongsTo('App\Models\app\Poll\PollQuestion','poll_question_id');
    }

    public function poll_answers()
    {
        return $this->hasMany('App\Models\app\Poll\PollAnswer','poll_option_id');
    }

    public function getPollMainAttribute()
    {
        $poll_main =
            PollMain::select('poll_mains.*')
                ->join('poll_questions', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
                ->join('poll_options', 'poll_questions.id', '=', 'poll_options.poll_question_id')
                ->where('poll_options.id',$this->id)
                ->first();
        return $poll_main;
    }

    // public function getPollTokensAttribute()
    // {
    //     $poll_tokens =
    //         PollToken::select('poll_tokens.*')
    //             ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
    //             ->join('poll_questions', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
    //             ->join('poll_options', 'poll_questions.id', '=', 'poll_options.poll_question_id')
    //             ->where('poll_options.id',$this->id)
    //             ->groupBy('poll_tokens.id')
    //             ->get();
    //     return $poll_tokens;
    // }

    public function getPollAnswerAttribute()
    {
        $poll_tokens =
            PollAnswer::select('poll_answers.*')
                ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->join('poll_questions', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
                ->join('poll_options', 'poll_questions.id', '=', 'poll_options.poll_question_id')
                ->where('poll_options.id',$this->id)
                ->groupBy('poll_answers.id')
                ->get();
        return $poll_tokens;
    }

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset('storage/'.$this->image) : null;
    }

    public function statusSelectedVote($token)
    {
        $poll_answer = PollAnswer::select('poll_answers.*')
        ->where('poll_answers.token',$token)
        ->where('poll_answers.poll_option_id',$this->id)
        ->first(); //dd($poll_answer,$token,$this->id);

        return ($poll_answer) ? true : false ;
    }
}

/*

Schema::create('poll_options', function (Blueprint $table) {
$table->smallIncrements('id');
$table->smallinteger('poll_question_id')->unsigned()->comment('Pregunta');
$table->string('text');
$table->string('description')->nullable()->comment('Descripción de la encuesta');
$table->string('observations')->nullable()->comment('Observaciones de la encuesta');
$table->text('body')->nullable();
$table->timestamps();
$table->foreign('poll_question_id')->references('id')->on('poll_questions')->onDelete('cascade')->onUpdate('cascade');
});

*/
