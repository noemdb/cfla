<?php

namespace App\Http\Controllers\Academico\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Learning\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_academico']);
    }
    public function index()
    {
        $lessons = Lesson::all();
        return view('academicos.lessons.index',compact('lessons'));
    }
}
