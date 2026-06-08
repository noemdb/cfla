<?php

namespace App\Http\Controllers\Director\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Administracion\Chart\ExchangeRateController as AdministracionExchangeRateController;

class ExchangeRateController extends Controller
{
    public function __construct()
    {
        $this->administracion_exchange_rate = new AdministracionExchangeRateController;
    }
    public function fluctuations_extend(Request $request)
    {
        return $this->administracion_exchange_rate->movimientocambiario($request);
    }
}
