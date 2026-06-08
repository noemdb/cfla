<?php

namespace App\Http\Controllers\Administracion\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Planpago\NomConceptoPago;

class ModalCreateController extends Controller
{
    public function create_nom_concepto(Request $request)
    {        
        $data = NomConceptoPago::create($request->all());
        $messenge = 'El nombre fue registrado exitosamente';
        $operation= 'create';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }        
        
    }
}
