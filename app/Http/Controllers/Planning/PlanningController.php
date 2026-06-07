<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    /**
     * Display the planning dashboard.
     */
    public function index()
    {
        return view('planning.index');
    }
}
