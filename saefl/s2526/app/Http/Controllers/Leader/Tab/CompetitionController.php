<?php

namespace App\Http\Controllers\Leader\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Leader;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetitionController extends Controller
{
    public $user, $autoridad, $pestudios;

    public function __construct()
    {
        $this->middleware(['auth', 'is_leader', function ($request, $next) {
            $this->user = User::find(Auth::id());
            $this->autoridad = Autoridad::where('user_id', Auth::id())->first();
            $this->pestudios = Leader::getPestudioForLeader(Auth::id());
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $autoridad = $this->autoridad;
        $pestudios = $this->pestudios;

        $competition = DebateCompetition::find(1);
        $indicators = $competition->getAccuracyForQuestionCategoryAll();

        return view('leaders.competitions.index', compact('autoridad', 'pestudios'));
    }

    public function indicators(Request $request)
    {
        $autoridad = $this->autoridad;
        $pestudios = $this->pestudios;
        return view('leaders.competitions.indicators', compact('autoridad', 'pestudios'));
    }
}
