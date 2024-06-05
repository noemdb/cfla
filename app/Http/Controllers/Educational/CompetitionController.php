<?php

namespace App\Http\Controllers\Educational;

use App\Http\Controllers\Controller;
use App\Models\app\Educational\DebateCompetition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function moderator($token)
    {
        $competition = DebateCompetition::where('token',$token)->first();
        if (empty($competition)) abort(404,'Competición no encontrada');
        return view('general.educational.competition.moderator.index',compact('token'));
    }

    public function board($token)
    {
        $competition = DebateCompetition::where('token',$token)->first();
        if (empty($competition)) abort(404,'Competición no encontrada');
        return view('general.educational.competition.board.index',compact('token'));
    }

    public function scoreboard($token)
    {
        $competition = DebateCompetition::where('token',$token)->first();
        if (empty($competition)) abort(404,'Competición no encontrada');
        return view('general.educational.competition.scoreboard.index',compact('token'));
    }
}
