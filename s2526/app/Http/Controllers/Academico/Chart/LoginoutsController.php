<?php

namespace App\Http\Controllers\Academico\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\Chart\LoginoutsController as AdminLoginoutsController;

class LoginoutsController extends Controller
{
    public function __construct()
    {
        $this->admin_logdbs = new AdminLoginoutsController;
    }

    public function loginouts_users_extend(Request $request)
    {
        return $this->admin_logdbs->LoginoutsUsers($request);
    }
    public function loginouts_months_extend(Request $request)
    {
        return $this->admin_logdbs->LoginoutsMonth($request);
    }
    public function loginouts_rols_extend(Request $request)
    {
        return $this->admin_logdbs->LoginoutsRols($request);
    }

}
