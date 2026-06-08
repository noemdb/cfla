<?php

namespace App\Http\Controllers\Bienestar\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_bienestar']);
    }
    public function index()
    {
        return view('bienestars.incidents.index');
    }
    public function summaries()
    {
        return view('bienestars.incidents.summaries');
    }
    public function overviews()
    {
        return view('bienestars.incidents.overviews');
    }
}
