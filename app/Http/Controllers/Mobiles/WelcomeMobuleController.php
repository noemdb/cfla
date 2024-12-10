<?php

namespace App\Http\Controllers\Mobiles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WelcomeMobuleController extends Controller
{
    public function welcome()
    {
        // Obtiene los testimonios y preguntas frecuentes desde la base de datos.
        // $testimonials = Testimonial::latest()->take(6)->get();
        // $faqs = Faq::all();

        // Retorna la vista con los datos dinámicos.

        $testimonials = collect();
        $faqs = collect();

        return view('mobiles.welcome', compact('testimonials', 'faqs'));
    }
}
