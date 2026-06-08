<?php

namespace App\Http\Controllers\Administracion\Tab\Interrogation;

use App\Http\Controllers\Controller;
use App\Models\app\interrogation\InterviewAnswer;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_control','is_admon']);
    }
    public function index()
    {
        $interview_answers = InterviewAnswer::all();
        return view('administracion.iterrogations.interviews.index',compact('interview_answers'));
    }
}
