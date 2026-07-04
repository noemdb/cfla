<?php

namespace App\Http\Controllers\Administracion\Ajax\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Retiro;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdministrativaController extends Controller
{
    public function retiro($id, Request $request)
    {
        $estudiant =  Estudiant::findOrFail($id);
        $user = Auth::user();
        $tipo = ( $user->isControl() ) ? 'control' : null ;
        $tipo = ( $user->isAdmon() ) ? 'admon' : null ;

        $retiro = Retiro::where('estudiant_id',$estudiant->id)->first();

        if (empty($retiro->id)) {
            $retiro = Retiro::create([
                'estudiant_id' => $id,
                'user_id' => $user->id,
                'tipo' => $tipo
            ]);
        }

        $ammount_expire_bill = $estudiant->ammount_expire_bill;
        $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill; //dd($exchange_ammount_expire_bill);

        if ( $ammount_expire_bill>0 || $exchange_ammount_expire_bill>0) {
            $expire_bill = Cuentaxpagar::create([
                'planpago_id' => $estudiant->administrativa->planpago_id,
                'name' => 'DEUDA INDIVIDUAL PENDIENTE',
                'type' => 'INDIVIDUAL',
                'estudiant_id' => $estudiant->id,
                'date_expiration' => Carbon::now(),
                'description' => 'DEUDA INDIVIDUAL PENDIENTE GENERADA POR: '.Auth::user()->username.', DEL RETIRO DEL ESTUDIANTE. ',
                'status_active' => 'true',
                'status_inscription' => 'true'
            ]);
            $expire_concepto = ConceptoPago::create([
                'cuentaxpagar_id' => $expire_bill->id,
                'nom_concepto_pago_id' => 3,
                'concepto_description' => 'DEUDA INDIVIDUAL PENDIENTE',
                'concepto_ammount' => $ammount_expire_bill,
                'exchange_ammount' => $exchange_ammount_expire_bill
            ]);
        }

        if (!empty($estudiant->inscripcion->id) && $tipo=='control') {
            $inscripcion = Inscripcion::findOrFail($estudiant->inscripcion->id);
            $inscripcion->delete();
        }

        if ( $tipo=='admon') {
            $retiro->update(['status_admon' => 'true']);
            $estudiant->update(['status_active' => 'false']);
            if (!empty($estudiant->administrativa->id) ) {
                $administrativa = Administrativa::findOrFail($estudiant->administrativa->id);
                $administrativa->delete();
            }

        }

        DB::commit();

        if($request->ajax()){
            return response()->json([
                "messenge"=>'Retiro administrativo realizado exitosamente.',
                "text"=>'Deuda individual generada por '.f_float($ammount_expire_bill).' Bs'
            ]);
        }
    }
}
