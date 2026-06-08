<?php

namespace App\Http\Controllers\Admin\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChartController extends Controller
{
    /*
        Constructor, verifica login del usuario - Profile
    */
    public function __construct(){

        $this->middleware('auth');

    }
    

    //
    public function users()
    {
        
        // dd('estoy en ChartController@users');
        return view('admin.users.charts');
    }

}
