<?php

namespace App\Http\Controllers;

use App\Models\app\Academy\Enrollment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        https://livewire.laravel.com/docs/forms#extracting-a-form-object
        $enrollment = new Enrollment(); //dd($post);
        return view('home',compact('enrollment'));
    }
}
