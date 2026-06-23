<?php

namespace App\Http\Controllers\Profesor\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function index(Request $request)
	{
		$profesor = $this->profesor;
		return view('profesors.incidents.index',compact('profesor'));
	}
}
