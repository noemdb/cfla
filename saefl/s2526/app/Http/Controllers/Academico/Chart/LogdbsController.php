<?php

namespace App\Http\Controllers\Academico\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\Chart\LogdbsController as AdminLogdbsController;

class LogdbsController extends Controller
{
    public function __construct()
    {
        $this->admin_logdbs = new AdminLogdbsController;
    }

    public function logdbs_users_extend(Request $request)
    {
        return $this->admin_logdbs->LogdbsUsers($request);
    }
    public function logdbs_months_extend(Request $request)
    {
        return $this->admin_logdbs->LogdbsMonth($request);
    }
    public function logdbs_rols_extend(Request $request)
    {
        return $this->admin_logdbs->LoginoutsRols($request);
    }
}
