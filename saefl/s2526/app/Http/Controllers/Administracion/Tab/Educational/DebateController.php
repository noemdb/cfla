<?php

namespace App\Http\Controllers\Administracion\Tab\Educational;

use App\Http\Controllers\Controller;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use Illuminate\Http\Request;

class DebateController extends Controller
{
    public function index()
    {
        return view('administracion.educational.debate.index');
    }

    public function indicators()
    {
        $competition = DebateCompetition::find(1);
        $result = $competition->getAccuracyForGrado(11);
        $result2 = $competition->getWrongAnswerPercentageForGrado(11);
        $result3 = $competition->getCorrectAnsweredQuestionsByGrado(11);
        $result4 = $competition->getWrongAnsweredQuestionsByGrado(11);

        dd(
            $result,
            $result2,
            $result3,
            $result4,
        );
    }

    public function fix()
    {
        $datas = collect();
        $questions = DebateQuestion::where('debate_id',36)->where('category','like','%[31059] Educación física%')->get(); //dd($questions);
        foreach ($questions as $question) { //dd($question);
            // $sentinela = DebateQuestion::where('id','<>',$question->id)->where('text',$question->text)->first();
            // if (empty($sentinela)) { //dd('123');
                $newQuestion = new DebateQuestion();
                $newQuestion->category = $question->category;
                $newQuestion->time = $question->time;
                $newQuestion->text = $question->text;
                $newQuestion->weighting = $question->weighting;
                $newQuestion->observation = $question->observation;
                $newQuestion->option_max = $question->option_max;
                $newQuestion->status_active = $question->status_active;
                $newQuestion->status_answer = $question->status_answer;
                $newQuestion->status_under_review = $question->status_under_review;
                $newQuestion->attachment = $question->attachment;
                $newQuestion->debate_id = 43;
                $newQuestion->save();
                $datas->push($newQuestion);
                //dd($question->debateOptions);
                foreach ($question->options as $option) {
                    $newOption = new DebateOption();
                    $newOption->text = $option->text;
                    $newOption->observation = $option->observation;
                    $newOption->attachment = $option->attachment;
                    $newOption->status_option_correct = $option->status_option_correct;
                    $newOption->question_id = $newQuestion->id;
                    $newOption->save();
                    $datas->push($newOption);
                }
            // }
        }
        dd($datas);
    }

    public function fix2()
    {
        $datas = collect();
        $questions = DebateQuestion::where('debate_id',36)->where('category','like','%[31059] Educación física%')->get(); //dd($questions);
        foreach ($questions as $question) { //dd($question);
            // $sentinela = DebateQuestion::where('id','<>',$question->id)->where('text',$question->text)->first();
            // if (empty($sentinela)) { //dd('123');
                $newQuestion = new DebateQuestion();
                $newQuestion->category = $question->category;
                $newQuestion->time = $question->time;
                $newQuestion->text = $question->text;
                $newQuestion->weighting = $question->weighting;
                $newQuestion->observation = $question->observation;
                $newQuestion->option_max = $question->option_max;
                $newQuestion->status_active = $question->status_active;
                $newQuestion->status_answer = $question->status_answer;
                $newQuestion->status_under_review = $question->status_under_review;
                $newQuestion->attachment = $question->attachment;
                $newQuestion->debate_id = 50;
                $newQuestion->save();
                $datas->push($newQuestion);
                //dd($question->debateOptions);
                foreach ($question->options as $option) {
                    $newOption = new DebateOption();
                    $newOption->text = $option->text;
                    $newOption->observation = $option->observation;
                    $newOption->attachment = $option->attachment;
                    $newOption->status_option_correct = $option->status_option_correct;
                    $newOption->question_id = $newQuestion->id;
                    $newOption->save();
                    $datas->push($newOption);
                }
            // }
        }
        dd($datas);
    }

    public function fix3()
    {
        $datas = collect();
        $questions = DebateQuestion::where('debate_id',42)->where('category','like','%[31059] Informática%')->get(); //dd($questions);
        foreach ($questions as $question) {
            $newQuestion = new DebateQuestion();
            $newQuestion->category = $question->category;
            $newQuestion->time = $question->time;
            $newQuestion->text = $question->text;
            $newQuestion->weighting = $question->weighting;
            $newQuestion->observation = $question->observation;
            $newQuestion->option_max = $question->option_max;
            $newQuestion->status_active = $question->status_active;
            $newQuestion->status_answer = $question->status_answer;
            $newQuestion->status_under_review = $question->status_under_review;
            $newQuestion->attachment = $question->attachment;
            $newQuestion->debate_id = 49;
            $newQuestion->save();
            $datas->push($newQuestion);
            foreach ($question->options as $option) {
                $newOption = new DebateOption();
                $newOption->text = $option->text;
                $newOption->observation = $option->observation;
                $newOption->attachment = $option->attachment;
                $newOption->status_option_correct = $option->status_option_correct;
                $newOption->question_id = $newQuestion->id;
                $newOption->save();
                $datas->push($newOption);
            }
        }
        dd($datas);
    }
}
