<?php

namespace App\Http\Controllers;

use App\Models\app\Academy\Enrollment;
use App\Models\app\Academy\Profesor;
use App\Models\app\Blog\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function studia(Request $request)
    {
        $testimonials = collect();
        $faqs = collect();

        return view('studia', compact('testimonials', 'faqs'));
    }

    public function home(Request $request)
    {
        return view('home');
    }

    public function payment(Request $request)
    {
        return view('payment');
    }

    public function enrollment(Request $request)
    {
        return view('enrollment');
    }

    public function credicard(Request $request)
    {
        return view('credicard');
    }

    public function post($id)
    {
        $post = Post::findOrFail($id);
        return view('post',compact('post'));
    }

    // public function env(Request $request)
    // {
    //     /* Getting All Env Variables */
    //     $allEnvVar = $_ENV;
    //     dd($allEnvVar);
    // }
}
