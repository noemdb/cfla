<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Profesor;
use Illuminate\Support\Facades\Auth;

class DebateController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id', Auth::id())->first();
            return $next($request);
        });
    }

    /**
     * Página principal de competencias (debates educativos).
     */
    public function competitions()
    {
        $profesor = $this->profesor;

        return view('profesors.competitions.index', compact('profesor'));
    }
}
