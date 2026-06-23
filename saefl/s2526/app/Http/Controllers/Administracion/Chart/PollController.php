<?php

namespace App\Http\Controllers\Administracion\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

// Modelos adicionadas
use App\Models\app\Poll\PollMain;

// Modelos adicionadas
use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Poll\PollToken;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollQuestion;
use App\Models\app\Poll\PollOption;
use App\Models\sys\Rol;

class PollController extends Controller
{
        public function tracking(Request $request)
    {
        $poll_question_id = ($request->input('limit')!=null) ? $request->input('limit') : 3;

        $poll_question = PollQuestion::findOrFail($poll_question_id);
        $poll_main = $poll_question->poll_main;
        $poll_options = $poll_question->poll_options;


        $dates = PollAnswer::select('poll_answers.id',DB::raw('count(poll_answers.id) as value'),
            DB::raw('DAY(poll_answers.created_at) as day'),
            DB::raw("DATE_FORMAT(poll_answers.created_at, '%m-%d %H') as date"),
            DB::raw('count(poll_answers.id) as count'))
            ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->where('poll_mains.id',$poll_main->id)
            ->groupby('date')
            ->orderBy('poll_answers.created_at', 'asc')
            ->pluck('date');

        foreach ($dates as $date) {
            $dateFull = Carbon::createFromFormat('m-d H', $date); //dd($dateFull);
            $limitDown = $dateFull->format('Y-m-d H');
            $limitUp = $dateFull->addHours(1)->format('Y-m-d H');
            foreach ($poll_options as $poll_option) {
                $option = PollOption::select('poll_options.*',DB::raw('count(poll_answers.id) as count'),DB::raw("DATE_FORMAT(poll_answers.created_at, '%m-%d %H') as date"))
                ->join('poll_answers', 'poll_options.id', '=', 'poll_answers.poll_option_id')
                ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
                ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
                ->where('poll_mains.id',$poll_main->id)
                ->where('poll_options.id',$poll_option->id)

                ->where('poll_answers.created_at','>=',$limitDown)
                ->where('poll_answers.created_at','<=',$limitUp)

                ->groupby('poll_options.id')
                ->orderBy('poll_answers.created_at', 'asc')
                ->first(); //dd($option);

                $label[$poll_option->id] = $poll_option->text;
                $values[$poll_option->id][] = ($option) ? $option->count : 0;
            }
        }

        foreach ($label as $k => $v) {
            $color = rand(0,255).', '.rand(0,255).', '.rand(0,255);
            $datasets[] = [
                "label"=>$v,
                "backgroundColor"=>'rgba('.$color.', 0.6)',
                "borderColor"=>'rgba('.$color.', 1)',
                "borderWidth"=>1,
                "data"=>$values[$k]
            ];
        }

        $labels = $dates;
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>$datasets
        ];

        return json_encode($ChartDataSQL);
    }


    public function userXarea(Request $request)
    {
        $poll_main_id = ($request->input('limit')!=null) ? $request->input('limit') : 1;

        $poll_main = PollMain::find($poll_main_id); //dd($request->input('limit'), $poll_main_id ,$poll_main);

        $attendees = $poll_main->attendees;

        $labels = $attendees->groupBy('area')->keys()->toArray();

        $labels = Rol::all()->groupBy('area')->keys()->toArray(); //dd($labels);

        $values = Array();        

        foreach ($labels as $key => $value) {
            $competitors = $poll_main->getCompetitorsForArea($value); //dd($value,$competitors);
            $count = 0;
            foreach ($competitors as $competitor) {
                $user = $competitor->user;
                $poll_answers = $user->getPollAnswers($poll_main->id);
                if ($poll_answers->isNotEmpty()) {
                    $count++;
                }
            }
            $values[] = $count;
        }
        //dd($labels,$values);

        for ($i=0; $i < count($labels) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Àreas",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function general(Request $request)
    {
        $poll_main_id = ($request->input('limit')!=null) ? $request->input('limit') : 1;

        $poll_main = PollMain::find($poll_main_id); //dd($request->input('limit'), $poll_main_id ,$poll_main);

        $competitors = $poll_main->competitors; //dd($competitors);

        $count_competitors = $competitors->count(); //dd($count_competitors);

        $poll_tokens = $poll_main->poll_tokens; //dd($poll_tokens);

        $count_tokens = $poll_tokens->count();

        $count_abstenciones = $count_tokens - $count_competitors;

        $porc_participantes = ($count_tokens  > 0) ? 100 * $count_competitors  / $count_tokens  : 0 ;
        $porc_participantes = round($porc_participantes,2);

        $porc_abstenciones = ($count_abstenciones  > 0) ? 100 * $count_abstenciones/ $count_tokens  : 0 ;
        $porc_abstenciones = round($porc_abstenciones,2);

        $labels = ['Abstención '.$porc_abstenciones.' %','Participación '.$porc_participantes.' %'];
        $values = [$count_abstenciones , $count_competitors];
        for ($i=0; $i < count($labels) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Participantes",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function questions(Request $request)
    {
        $id = ($request->input('limit')!=null) ? $request->input('limit') : 1;

        $poll_main = PollMain::find($id); //dd($poll_main);

        $poll_questions = $poll_main->poll_questions;

        $competitors = $poll_main->competitors; //dd($competitors);
        $count_competitors = $competitors->count(); //dd($count_competitors);

        $datasets = Array();

        /*$labels = $poll_questions->pluck('text'); dd($labels);*/
        $labels = Array();

        $q = 0;
        foreach ($poll_questions as $poll_question) {

            $q++;
            $name = 'Pregunta '.$poll_question->id;

            $labels[] = $name ;

            //$poll_options = $poll_question->poll_options; //dd($poll_options);
            $poll_options = $poll_question->getPollOptionsAnswer();

            $values = Array();
            $label = Array();

            $i = 0;
            foreach ($poll_options as $poll_option) {
                $poll_answers = $poll_option->poll_answers; //dd($poll_answers);
                $porcentage = ($count_competitors > 0) ? 100 * $poll_answers->count() / $count_competitors : null ;
                $porcentage = round($porcentage,2);
                $values[$i] = $porcentage;
                $i++;
                $label[] = 'Opción '.$i.' (%) ';
            }

            $name = 'Opción '.$q.' (%) ';
            $colors = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
            $datasets[] = [
                "label"=>$name,
                // "label"=>$label,
                "backgroundColor"=>$colors,
                "borderColor"=>$colors,
                "borderWidth"=>1,
                "data"=>$values
            ];
        }

        //dd($datasets,$competitors,$count_competitors,$values);

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>$datasets
        ]; //dd($ChartDataSQL);

        return json_encode($ChartDataSQL);
    }

    public function timeline(Request $request)
    {
        $poll_main_id = ($request->input('limit')!=null) ? $request->input('limit') : 1;
        $poll_main = PollMain::find($poll_main_id);

        $finicial = $poll_main->finicial ;
        $ffinal = $poll_main->ffinal ;

        //Date::setLocale('UTC');

        $data = PollAnswer::select('poll_answers.id',DB::raw('count(poll_answers.id) as value'),
            DB::raw('DAY(poll_answers.created_at) as day'),
            DB::raw("DATE_FORMAT(poll_answers.created_at, '%m-%d %H') as date"))
            ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->where('poll_mains.id',$poll_main->id)
            ->groupby('date')
            ->orderBy('poll_answers.created_at', 'asc')
            ->get();

        $label_month = array();
        $labels = $data->pluck('value','date');
        foreach ($labels as $date => $value) {
            $dateObj   = Date::createFromFormat('m-d H', $date);
            //$dateObj->addHours(4)->addMinutes(30);// dd($dateObj);
            // $dateObj->setTimezone(config('app.timezone')); dd(config('app.timezone')); //dd($dateObj);
            $str_date = $dateObj->format('j').' '.ucfirst($dateObj->format('M')). ' '.($dateObj->format('H')).':00';
            $label_month[] = $str_date;
        }

        $values = $data->pluck('value');

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }
        //FIN nombre de los meses en español

        // dd($labels, $label_month, $values,$finicial,$ffinal);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Cantidad de participaciones",
                    "backgroundColor"=>"rgba(100, 20, 150,0.2)",
                    "borderColor"=>"rgba(100, 20, 150,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function question(Request $request)
    {
        $id = ($request->input('limit')!=null) ? $request->input('limit') : 1;
        $labels = Array();

        $poll_question = PollQuestion::find($id); //dd($poll_main);

        $poll_options = $poll_question->getPollOptionsAnswer();

        $texts = $poll_options->pluck('text');
        $values = $poll_options->pluck('poll_answers_count');

        foreach ($texts as $k => $v) {
            $labels[] = $v.' ['.$values[$k].']';
        }

        for ($i=0; $i < count($texts) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Opciones",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}
