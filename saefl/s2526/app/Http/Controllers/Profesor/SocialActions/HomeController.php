<?php

namespace App\Http\Controllers\Profesor\SocialActions;

use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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
        return view('profesors.social_actions.index',compact('profesor'));
    }
}
