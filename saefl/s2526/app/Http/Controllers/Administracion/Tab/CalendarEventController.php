<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }
    public function index()
    {
        return view('administracion/configuraciones/calendar_events/index'); //administracion/configuraciones/calendar_events/index.blade.php
    }
}
