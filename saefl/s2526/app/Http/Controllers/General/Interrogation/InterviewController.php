<?php

namespace App\Http\Controllers\General\Interrogation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    //general.interrogation.interviews.index

    public function index(Request $request)
    {
        return view('general.interrogation.interviews.index');
    }
}
