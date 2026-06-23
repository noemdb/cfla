<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;

class CheckSolvency
{
    public function handle($request, Closure $next, $type = 'estudiant')
    {
        // ------------------------------------------------------
        // 🔎 DETERMINAR SI ES ESTUDIANTE O REPRESENTANTE
        // ------------------------------------------------------
        if ($type === 'estudiant') {

            $estudiant_id = $request->route('estudiant_id');

            if (!$estudiant_id) {
                abort(400, 'ID de estudiante no proporcionado');
            }

            $estudiant = Estudiant::findOrFail($estudiant_id);

            $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill, 2);

            if ($exchange_ammount_expire_bill > 0) {
                return response()->view('errors.estudiant-no-solvent', [], 403);
            }

        } elseif ($type === 'representant') {

            $representant_id = $request->route('representant_id');

            if (!$representant_id) {
                abort(400, 'ID de representante no proporcionado');
            }

            $representant = Representant::findOrFail($representant_id);

            $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);

            if ($exchange_ammount_expire_bill > 0) {
                return response()->view('errors.estudiant-no-solvent', [], 403);
            }

        } else {
            abort(500, 'Tipo de validación no definido en CheckSolvency');
        }

        return $next($request);
    }
}
