<?php

namespace App\Http\Controllers\Profesor\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\app\Pescolar\Profesor;

class CensuController extends Controller
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
		return view('profesors.census.index');
	}
}
