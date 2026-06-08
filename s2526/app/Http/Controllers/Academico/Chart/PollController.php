<?php

namespace App\Http\Controllers\Academico\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Administracion\Chart\PollController as AdminPollController;

class PollController extends Controller
{
    protected $admin_pollmain;

    public function __construct()
    {
        $this->admin_pollmain = new AdminPollController;
    }

    public function question(Request $request)
    {
        return $this->admin_pollmain->question($request);
    }
    public function general(Request $request)
    {
        return $this->admin_pollmain->general($request);
    }
    public function timeline(Request $request)
    {
        return $this->admin_pollmain->timeline($request);
    }

}
