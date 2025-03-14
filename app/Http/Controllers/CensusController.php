<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CensusController extends Controller
{
    public function index(Request $request)
    {
        return view('census');
    }
}
