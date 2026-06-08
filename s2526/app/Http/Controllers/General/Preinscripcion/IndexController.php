<?php

namespace App\Http\Controllers\General\Preinscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index($token)
    {
        return view('general.preinscripcions.index',compact('token'));
    }
}
