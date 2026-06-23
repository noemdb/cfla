<?php

namespace App\Models\app\Poll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_question_id','poll_option_id','token'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'poll_question_id' => 'Nombre de la Consulta',
        'poll_option_id' => 'Opción seleccionada',
        'token' => 'Token',
    ];

    public function poll_question() { return $this->belongsTo('App\Models\app\Poll\PollQuestion','poll_question_id'); }

    public function poll_option() { return $this->belongsTo('App\Models\app\Poll\PollOption','poll_option_id'); }
}

/*

Schema::create('poll_answers', function (Blueprint $table) {
$table->bigIncrements('id');
$table->smallinteger('poll_question_id')->unsigned()->comment('Pregunta');
$table->smallinteger('poll_option_id')->unsigned()->comment('Opción');
$table->string('token');
$table->timestamps();
$table->foreign('poll_question_id')->references('id')->on('poll_questions')->onDelete('cascade')->onUpdate('cascade');
$table->foreign('poll_option_id')->references('id')->on('poll_options')->onDelete('cascade')->onUpdate('cascade');
});

*/
