<?php

namespace App\Http\Controllers\Administracion\AssistControl;

use App\Http\Controllers\Controller;
use App\Models\app\Assistcontrol\AssitSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssitScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('administracion.asisst_controls.assit_schedules.index');
    }

}
