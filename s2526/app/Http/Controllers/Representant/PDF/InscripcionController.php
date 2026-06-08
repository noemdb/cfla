<?php

namespace App\Http\Controllers\Representant\PDF;


use App\Http\Controllers\Administracion\PDF\InscripcionController as AdminInscripcionController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{
    protected $representant,$estudiants,$list_comment,$admin_boletin;

    public function __construct()
    {
        $this->middleware(['auth','is_representant', function ($request, $next) {
            $this->representant = Representant::where('user_id',Auth::user()->id)->first();
            $this->list_comment = Representant::COLUMN_COMMENTS;
            $this->estudiants = ($this->representant) ? $this->representant->estudiants : null;
            $this->admin_inscripcion = new AdminInscripcionController;
            return $next($request);
        }]);
    }

    public function constancia_inscripcions($id)
    {
        try {
            $representant = $this->representant;
            $inscripcion = $this->admin_inscripcion;
            
            // Buscar estudiante y validar permisos
            $estudiant = Estudiant::where('representant_id', $representant->id)
                                  ->where('id', $id)
                                  ->first();

            // -------------------------------
            // 🔍 VALIDACIÓN: ESTUDIANTE NO ENCONTRADO
            // -------------------------------
            if (!$estudiant) {
                return response()->view('errors.estudiant-not-found', [], 404);
            }

            // -------------------------------
            // 🔒 VALIDACIÓN DE SOLVENCIA
            // -------------------------------
            $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill, 2);

            if ($exchange_ammount_expire_bill > 0) {
                return response()->view('errors.estudiant-no-solvent', [], 403);
            }
            // -------------------------------

            return $inscripcion->constanciapdf($id);
            
        } catch (\Exception $e) {
            // Manejo de errores inesperados
            \Log::error('Error en constancia_inscripcions: ' . $e->getMessage());
            return response()->view('errors.generic-error', [
                'message' => 'Ocurrió un error inesperado al generar la constancia'
            ], 500);
        }
    }

    public function constancia_estudio($id)
    {
        try {
            $representant = $this->representant;
            $inscripcion = $this->admin_inscripcion;
            
            // Buscar estudiante y validar permisos
            $estudiant = Estudiant::where('representant_id', $representant->id)
                                  ->where('id', $id)
                                  ->first();

            // -------------------------------
            // 🔍 VALIDACIÓN: ESTUDIANTE NO ENCONTRADO
            // -------------------------------
            if (!$estudiant) {
                return response()->view('errors.estudiant-not-found', [], 404);
            }

            // -------------------------------
            // 🔒 VALIDACIÓN DE SOLVENCIA
            // -------------------------------
            $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill, 2);

            if ($exchange_ammount_expire_bill > 0) {
                return response()->view('errors.estudiant-no-solvent', [], 403);
            }
            // -------------------------------
            
            return $inscripcion->cestudiopdf($id);
            
        } catch (\Exception $e) {
            // Manejo de errores inesperados
            \Log::error('Error en constancia_estudio: ' . $e->getMessage());
            return response()->view('errors.generic-error', [
                'message' => 'Ocurrió un error inesperado al generar la constancia de estudio'
            ], 500);
        }
    }
}