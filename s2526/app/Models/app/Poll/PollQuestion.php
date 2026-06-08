<?php

namespace App\Models\app\Poll;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PollQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_main_id','text','description','observations','body','image','status_grid'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'poll_main_id' => 'Nombre de la Consulta',
        'text' => 'Interrogante',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'body' => 'Explicaciones',
        'status_grid' => 'Opciones en modo grip',
        'image' => 'Imagen',
    ];

    public function poll_main()
    {
        return $this->belongsTo('App\Models\app\Poll\PollMain','poll_main_id');
    }

    public function poll_options()
    {
        return $this->hasMany('App\Models\app\Poll\PollOption','poll_question_id');
    }

    public function poll_answers()
    {
        return $this->hasMany('App\Models\app\Poll\PollAnswer','poll_question_id');
    }

    public function getListOptionsAttribute()
    {
        $list = PollOption::select('poll_options.*')
        ->selectRaw("CONCAT(poll_options.text,' - ',poll_options.description) as full_description")
        ->where('poll_question_id',$this->id)
        ->pluck('full_description','id');
        // $list = $this->poll_options->pluck('text','id');
        return $list;
    }

    public function getPollTokensAttribute()
    {
        $poll_tokens =
            PollToken::select('poll_tokens.*')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->join('poll_questions', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
                ->where('poll_questions.id',$this->id)
                ->get();
        return $poll_tokens;
    }

    public function getPollAnswerAttendee($user_id)
    {
        $poll_answer =
            PollAnswer::select('poll_answers.*','poll_options.text')
                ->join('poll_options', 'poll_options.id', '=', 'poll_answers.poll_option_id')
                ->join('poll_questions', 'poll_questions.id', '=', 'poll_options.poll_question_id')
                ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')

                ->where('poll_tokens.user_id',$user_id)
                ->where('poll_questions.id',$this->id)
                ->first(); //dd($poll_answer);

        return $poll_answer;
    }

    public function getStatusDeleteAttribute()
    {
        $now = Carbon::now()->format('Y-m-d');
        $poll_main = $this->poll_main;
        return ($poll_main->date_start > $now) ? false : true;
    }

    public static function list_poll_question_enable() /* usada para llenar los objetos de formularios select*/
    {
        $now = Carbon::now()->format('Y-m-d'); //dd($now);
        return
            PollQuestion::select('poll_questions.*')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
                ->where('poll_mains.date_start','<=',$now)
                ->pluck('text','id');
    }

    public static function list_poll_question_enable_user($user_id) /* usada para llenar los objetos de formularios select*/
    {
        $user = User::find($user_id);
        if ($user) {
            $now = Carbon::now()->format('Y-m-d');
            $poll_questions = PollQuestion::select('poll_questions.*')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
                // ->where('poll_mains.date_start','<',$now)
            ;

            $is_admin = $user->IsAdmin(); //dd($is_admin);

            $poll_questions = ($is_admin) ? $poll_questions : $poll_questions->where('poll_mains.user_id',$user->id) ;

            return $poll_questions->pluck('text','id');
        }
    }

    public static function list_question_enable_token($token) /* usada para llenar los objetos de formularios select*/
    {
        $now = Carbon::now()->format('Y-m-d'); //dd($now,$token);
        $data = collect();
        $datas = collect();
        $list = collect();

        $poll_token = PollToken::where('token',$token)->first();

        if ($poll_token) {
            $poll_main = $poll_token->poll_main;
            if ($poll_main) {
                $poll_questions = PollQuestion::select('poll_questions.*')
                    ->join('poll_mains', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
                    ->whereDate('poll_mains.date_start','<=',$now)
                    ->where('poll_mains.id',$poll_main->id)
                    ->get(); //dd($poll_questions);
                foreach ($poll_questions as $poll_question) {
                    $poll_answer = PollAnswer::where('poll_question_id',$poll_question->id)->where('token',$token)->first();
                    if (empty($poll_answer)) {
                        $list->put($poll_question->id,$poll_question->text);
                    }
                }
            }
        }
        return $list;
    }

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset('storage/'.$this->image) : null;
    }


    public function getPollOptionsAnswer()
    {
        $poll_options =
            PollOption::select('poll_options.*')
                ->selectRaw('count(poll_answers.id) as poll_answers_count')
                ->join('poll_questions', 'poll_questions.id', '=', 'poll_options.poll_question_id')
                ->join('poll_answers', 'poll_options.id', '=', 'poll_answers.poll_option_id')
                // ->join('poll_mains', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
                // ->join('poll_tokens', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->where('poll_questions.id',$this->id)
                ->orderBy('poll_answers_count','desc')
                ->groupBy('poll_options.id')
                ->get(); //dd($poll_options);

        return $poll_options;
    }

}


/*

Schema::create('poll_questions', function (Blueprint $table) {
$table->smallIncrements('id');
$table->smallinteger('poll_main_id')->unsigned()->comment('Encuesta');
$table->string('text');
$table->string('description')->nullable()->comment('Descripción de la encuesta');
$table->string('observations')->nullable()->comment('Observaciones de la encuesta');
$table->text('body')->nullable();
$table->timestamps();
$table->foreign('poll_main_id')->references('id')->on('poll_mains')->onDelete('cascade')->onUpdate('cascade');
});

*/
