<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiagnosticController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id', Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $profesor = $this->profesor;
        return view('profesors.diagnostics.index', compact('profesor'));
    }
}
