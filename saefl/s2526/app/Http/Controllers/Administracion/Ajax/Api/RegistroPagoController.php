<?php //apis para registro de pago [return json_encode]

namespace App\Http\Controllers\Administracion\Ajax\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\app\Planpago\RegistroPagoCombinado;

class RegistroPagoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function list_registro_pagos_irregulars(Request $request)
    {
        $start = (!empty($request->start)) ? $request->start : null;
        $size = (!empty($request->size)) ? $request->size : null ;

        if ($start && $size) {

            $datas = RegistroPagoCombinado::irregular_pay($start,$size);

            return json_encode($datas);

        }

    }

    public function all_registro_pagos_irregulars()
    {
        $size = RegistroPagoCombinado::count();
        $datas = RegistroPagoCombinado::irregular_pay(1,$size);

        return json_encode($datas);
    }
}
