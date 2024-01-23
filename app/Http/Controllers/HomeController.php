<?php

namespace App\Http\Controllers;

use App\Models\app\Academy\Enrollment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        return view('home');
    }
}
