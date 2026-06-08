<?php

namespace App\Http\Controllers\Director\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Administracion\Chart\PaymentController as AdministracionPaymentController;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->administracion_payment = new AdministracionPaymentController;
    }
    public function countxday_extend(Request $request)
    {
        return $this->administracion_payment->countxday($request);
    }

}
