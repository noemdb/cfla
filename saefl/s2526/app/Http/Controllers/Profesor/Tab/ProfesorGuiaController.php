<?php

namespace App\Http\Controllers\Profesor\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Facades\Auth;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\SectionDiagnosticReport;

class ProfesorGuiaController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user) {
                abort(401, 'Usuario no autenticado');
            }
            
            $this->profesor = Profesor::where('user_id', $user->id)->first();
            
            if (!$this->profesor) {
                abort(403, 'Profesor no registrado en el sistema');
            }
            
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $profesor = $this->profesor;

        $seccions = $profesor->seccion_guias;

        $lapsos = Lapso::all();

        $diagMains = DiagMain::active(true); //dd($diagMains );

        return view('profesors.profesor_guias.index',compact('seccions','diagMains','lapsos'));
    }

    public function showDiag(DiagMain $diagMain, Seccion $seccion)
    {
        // Verificar que el profesor sea guía de esta sección
        $profesor = $this->profesor;
        
        if (!$profesor) {
            abort(403, 'Profesor no encontrado');
        }

        // Verificar si el profesor guía esta sección
        $isGuia = $profesor->seccion_guias->where('seccions.id', $seccion->id)->empty();
        
        if (!$isGuia) {
            abort(403, 'No tiene permisos para ver diagnósticos de esta sección');
        }

        // Cargar relaciones de la sección
        $seccion->load(['grado.pestudio']);

        // Buscar el reporte de sección específico
        $sectionReport = SectionDiagnosticReport::with([
                'section.grado.pestudio',
                'globalResult',
                'areaResults.insights',
                'profile',
                'recommendations',
                'contrast'
            ])
            ->where('diagnostic_id', $diagMain->id)
            ->where('section_id', $seccion->id)
            ->first();

        // Obtener todos los diagnósticos activos
        $allDiagMains = DiagMain::active(true);

        return view('profesors.profesor_guias.modal.diag', compact(
            'diagMain', 
            'seccion',
            'sectionReport',
            'allDiagMains'
        ));
    }

    // Método para cargar reportes específicos via AJAX
    public function getSectionReport(DiagMain $diagMain, Request $request)
    {
        $reportId = $request->get('report_id');
        
        // Buscar el reporte
        $report = SectionDiagnosticReport::with([
            'section' => function($query) {
                $query->with(['grado']);
            },
            'globalResult',
            'areaResults.insights',
            'profile',
            'recommendations',
            'contrast'
        ])->findOrFail($reportId);

        // Verificar que el profesor tenga acceso
        $profesor = $this->profesor;
        $esGuiaDeSeccion = false;
        
        foreach ($profesor->seccion_guias as $seccionGuia) {
            if ($seccionGuia->id == $report->section_id) {
                $esGuiaDeSeccion = true;
                break;
            }
        }
        
        if (!$esGuiaDeSeccion) {
            abort(403, 'No autorizado');
        }

        return view('profesors.profesor_guias.modal.partials.section-report', [
            'report' => $report
        ]);
    }

}
