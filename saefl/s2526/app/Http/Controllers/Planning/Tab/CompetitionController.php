<?php

namespace App\Http\Controllers\Planning\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Educational\DebateCompetition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    use UserDataInitializer;

    public function __construct()
    {
        $this->middleware(['auth', 'is_planning', function ($request, $next) {
            $this->initializeUserData();
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $autoridad = $this->autoridad;
        $pestudios = $this->pestudios;

        $competition = DebateCompetition::find(1);
        // $indicators = $competition->getAccuracyForQuestionCategory("[31059] Castellano");
        $indicators = $competition->getAccuracyForQuestionCategoryAll(); //dd($indicators);

        return view('plannings.competitions.index', compact('autoridad', 'pestudios'));
    }

    public function indicators(Request $request)
    {
        $autoridad = $this->autoridad;
        $pestudios = $this->pestudios;
        return view('plannings.competitions.indicators', compact('autoridad', 'pestudios'));
    }
}
