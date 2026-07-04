<?php

namespace App\Http\Controllers\General\Catchment;

use App\Http\Controllers\Controller;
use App\Models\app\Enrollment\Catchment;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $request)
    {
        return view('general.catchments.index');
    }

    public function register($token)
    {
        $catchment =Catchment::where('token',$token)->first(); //dd($token,$catchment);
        $StrToken = $token;
        $token = ($catchment) ? $catchment->token : null ; //dd($token,$StrToken,$catchment);
        return view('general.catchments.register',compact('token','StrToken','catchment'));
    }
    
}
