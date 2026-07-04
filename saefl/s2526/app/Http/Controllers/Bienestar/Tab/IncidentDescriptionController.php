<?php

namespace App\Http\Controllers\Bienestar\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncidentDescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_bienestar']);
    }
    public function index()
    {
        return view('bienestars.incident_descriptions.index');
    }
}
