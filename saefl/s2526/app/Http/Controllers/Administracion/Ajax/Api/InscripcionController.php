<?php

namespace App\Http\Controllers\Administracion\Ajax\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Retiro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    public function retiro($id, Request $request)
    {
        $estudiant =  Estudiant::findOrFail($id);

        $retiro = Retiro::where('estudiant_id',$id)->first();

        if (empty($retiro->id)) {
            $retiro = Retiro::create([
                    'estudiant_id' => $id,
                    'user_id' => Auth::user()->id,
                    'tipo' => 'control'
                ]);
        }

        $inscripcion =  Inscripcion::where('estudiant_id',$estudiant->id)->first();

        if ($inscripcion) {
            // $inscripcion->delete();
            $inscripcion->forceDelete();
        }

        if($request->ajax()){
            return response()->json([
                "messenge"=>'Retiro académico realizado exitosamente.',
                "text"=>'Retirado el: '.f_date($retiro->created_at),
                "operation"=>'operp_ok'
            ]);
        }
    }
}
