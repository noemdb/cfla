<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Profesor;
use App\Services\EducationStatsService;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InicialController extends Controller
{
    protected $educationStatsService,$peducativos;
    public $user, $autoridad, $list_comment_autoridad;

    public function __construct(EducationStatsService $educationStatsService)
    {
        $this->middleware(['auth', 'is_evaluacion']);
        $this->educationStatsService = $educationStatsService;

        $this->user = User::find(Auth::id());
        $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
        $this->autoridad = Autoridad::where('user_id',Auth::id())->first();
    }

    /**
     * Muestra la vista principal con indicadores y registros de planificación
     */
    public function index(Request $request)
    {
        try {
            $profesor_id = $request->profesor_id;
            $grado_id = $request->grado_id;
            $seccion_id = $request->seccion_id;

            // Generar clave de caché única para los filtros
            $cacheKey = "inicial_data_{$profesor_id}_{$grado_id}_{$seccion_id}";

            // Intentar obtener datos del caché (válido por 5 minutos)
            $cachedData = Cache::remember($cacheKey, 300, function () use ($profesor_id, $grado_id, $seccion_id) {
                return $this->getFilteredData($profesor_id, $grado_id, $seccion_id);
            });

            // Obtener estadísticas de educación
            $education_stats = $this->educationStatsService->getEducationStats($profesor_id, $grado_id);

            // Obtener listas para los filtros
            $list_grado = Grado::list_pestudio_grado(6); // Educ Inicial
            $list_profesors = Profesor::list_profesors_pestudio(6); // Educ Inicial

            // Preparar datos para la vista
            $viewData = array_merge($cachedData, [
                'profesor_id' => $profesor_id,
                'grado_id' => $grado_id,
                'seccion_id' => $seccion_id,
                'list_grado' => $list_grado,
                'list_profesors' => $list_profesors,
                'education_stats' => $education_stats,
                'stats_json' => json_encode($education_stats), // Para uso en JavaScript si es necesario
                'last_updated' => now()->format('d/m/Y H:i:s'),
            ]);

            return view('evaluacions.inicilas.index', $viewData);

        } catch (Exception $e) {
            Log::error('Error en InicialController@index: ' . $e->getMessage(), [
                'profesor_id' => $profesor_id ?? null,
                'grado_id' => $grado_id ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Ocurrió un error al cargar los datos. Por favor, intente nuevamente.');
        }
    }

    /**
     * Obtiene los datos filtrados para la vista principal
     */
    private function getFilteredData($profesor_id = null, $grado_id = null, $seccion_id = null)
    {
        // Planes semanales
        $eiplanningwks = $this->applyFilters(
            Eiplanningwk::with(['profesor', 'grado', 'seccion', 'eiprojectk']),
            $profesor_id,
            $grado_id,
            $seccion_id
        )->orderBy('created_at', 'desc')->get();

        // Planes quincenales
        $eiplanningbwks = $this->applyFilters(
            Eiplanningbwk::with(['profesor', 'grado', 'seccion', 'eiprojectk']),
            $profesor_id,
            $grado_id,
            $seccion_id
        )->orderBy('created_at', 'desc')->get();

        // Proyectos
        $eiprojectks = $this->applyFilters(
            Eiprojectk::with(['profesor', 'grado', 'seccion']),
            $profesor_id,
            $grado_id,
            $seccion_id
        )->orderBy('created_at', 'desc')->get();

        // Planes especiales
        $eispecialks = $this->applyFilters(
            Eispecialk::with(['profesor', 'grado', 'seccion']),
            $profesor_id,
            $grado_id,
            $seccion_id
        )->orderBy('created_at', 'desc')->get();

        // Evaluaciones
        $eievaluationks = $this->applyFilters(
            Eievaluationk::with(['profesor', 'grado', 'seccion', 'lapso']),
            $profesor_id,
            $grado_id,
            $seccion_id
        )->orderBy('created_at', 'desc')->get();

        return [
            'eiplanningwks' => $eiplanningwks,
            'eiplanningbwks' => $eiplanningbwks,
            'eiprojectks' => $eiprojectks,
            'eispecialks' => $eispecialks,
            'eievaluationks' => $eievaluationks,
        ];
    }

    /**
     * Aplica filtros comunes a las consultas
     */
    private function applyFilters($query, $profesor_id = null, $grado_id = null, $seccion_id = null)
    {
        if ($grado_id) {
            $query->where('grado_id', $grado_id);
        }

        if ($profesor_id) {
            $query->where('profesor_id', $profesor_id);
        }

        if ($seccion_id) {
            $query->where('seccion_id', $seccion_id);
        }

        return $query;
    }

    /**
     * API endpoint para obtener estadísticas actualizadas
     */
    public function getStats(Request $request)
    {
        try {
            $profesor_id = $request->profesor_id;
            $grado_id = $request->grado_id;

            $stats = $this->educationStatsService->getEducationStats($profesor_id, $grado_id);

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            Log::error('Error en InicialController@getStats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ], 500);
        }
    }

    /**
     * Limpia el caché de datos
     */
    public function clearCache(Request $request)
    {
        try {
            $profesor_id = $request->profesor_id;
            $grado_id = $request->grado_id;
            $seccion_id = $request->seccion_id;

            $cacheKey = "inicial_data_{$profesor_id}_{$grado_id}_{$seccion_id}";
            Cache::forget($cacheKey);

            // También limpiar caché de estadísticas si existe
            $statsCacheKey = "education_stats_{$profesor_id}_{$grado_id}";
            Cache::forget($statsCacheKey);

            return response()->json([
                'success' => true,
                'message' => 'Caché limpiado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en InicialController@clearCache: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar caché'
            ], 500);
        }
    }

    /**
     * Formato para plan semanal
     */
    public function format_eiplanningwk($id, Request $request)
    {
        try {
            $eiplanningwk = Eiplanningwk::with(['profesor', 'grado', 'seccion'])->findOrFail($id);
            $profesor = $eiplanningwk->profesor;
            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $fecha = Carbon::now()->format('d-m-Y h:i A');

            return view('livewire.inicial.formats.eiplanningwk.index', compact(
                'profesor',
                'eiplanningwk',
                'institucion',
                'fecha'
            ));

        } catch (\Exception $e) {
            Log::error('Error en format_eiplanningwk: ' . $e->getMessage(), ['id' => $id]);
            return redirect()->back()->with('error', 'No se pudo cargar el formato solicitado.');
        }
    }

    /**
     * Formato para plan quincenal
     */
    public function format_eiplanningbwk($id, Request $request)
    {
        try {
            $eiplanningbwk = Eiplanningbwk::with(['profesor', 'grado', 'seccion'])->findOrFail($id);
            $profesor = $eiplanningbwk->profesor;
            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $fecha = Carbon::now()->format('d-m-Y h:i A');

            return view('livewire.inicial.formats.eiplanningbwk.index', compact(
                'profesor',
                'eiplanningbwk',
                'institucion',
                'fecha'
            ));

        } catch (\Exception $e) {
            Log::error('Error en format_eiplanningbwk: ' . $e->getMessage(), ['id' => $id]);
            return redirect()->back()->with('error', 'No se pudo cargar el formato solicitado.');
        }
    }

    /**
     * Formato para proyectos
     */
    public function format_eiprojectks($id, Request $request)
    {
        try {
            $eiprojectk = Eiprojectk::with(['profesor', 'grado', 'seccion'])->findOrFail($id);
            $profesor = $eiprojectk->profesor;
            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $fecha = Carbon::now()->format('d-m-Y h:i A');

            return view('livewire.inicial.formats.eiprojectks.index', compact(
                'profesor',
                'eiprojectk',
                'institucion',
                'fecha'
            ));

        } catch (\Exception $e) {
            Log::error('Error en format_eiprojectks: ' . $e->getMessage(), ['id' => $id]);
            return redirect()->back()->with('error', 'No se pudo cargar el formato solicitado.');
        }
    }

    /**
     * Formato para planes especiales
     */
    public function format_eispecialks($id, Request $request)
    {
        try {
            $eispecialk = Eispecialk::with(['profesor', 'grado', 'seccion'])->findOrFail($id);
            $profesor = $eispecialk->profesor;
            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $fecha = Carbon::now()->format('d-m-Y h:i A');

            return view('livewire.inicial.formats.eispecialks.index', compact(
                'profesor',
                'eispecialk',
                'institucion',
                'fecha'
            ));

        } catch (\Exception $e) {
            Log::error('Error en format_eispecialks: ' . $e->getMessage(), ['id' => $id]);
            return redirect()->back()->with('error', 'No se pudo cargar el formato solicitado.');
        }
    }

    /**
     * Formato para evaluaciones
     */
    public function format_eievaluationks($id, Request $request)
    {
        try {
            $eievaluationk = Eievaluationk::with(['profesor', 'grado', 'seccion', 'lapso'])->findOrFail($id);
            $profesor = $eievaluationk->profesor;
            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $fecha = Carbon::now()->format('d-m-Y h:i A');

            return view('livewire.inicial.formats.eievaluationk.index', compact(
                'profesor',
                'eievaluationk',
                'institucion',
                'fecha'
            ));

        } catch (\Exception $e) {
            Log::error('Error en format_eievaluationks: ' . $e->getMessage(), ['id' => $id]);
            return redirect()->back()->with('error', 'No se pudo cargar el formato solicitado.');
        }
    }

    /**
     * Formato para informes finales
     */
    public function format_eifinalks($id, Request $request)
    {
        try {
            $eifinalk = Eifinalk::with([
                'pevaluacion.profesor',
                'pevaluacion.pensum.grado',
                'pevaluacion.seccion',
                'pevaluacion.lapso',
                'estudiant'
            ])->findOrFail($id);

            $profesor = $eifinalk->pevaluacion->profesor;
            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $fecha = Carbon::now()->format('d-m-Y h:i A');

            return view('livewire.inicial.formats.eifinalk.index', compact(
                'profesor',
                'eifinalk',
                'institucion',
                'fecha'
            ));

        } catch (\Exception $e) {
            Log::error('Error en format_eifinalks: ' . $e->getMessage(), ['id' => $id]);
            return redirect()->back()->with('error', 'No se pudo cargar el formato solicitado.');
        }
    }

    /**
     * Imprime todos los informes finales de un estudiante para un lapso específico
     */
    public function printAllforLapso(Estudiant $estudiant, Lapso $lapso)
    {
        try {
            // Cargar todos los informes finales del estudiante con sus relaciones
            $eifinalks = $estudiant->eifinalks()
                ->with([
                    'pevaluacion.pensum.grado',
                    'pevaluacion.seccion',
                    'pevaluacion.lapso',
                    'pevaluacion.profesor',
                    'expectations.area'
                ])
                ->whereHas('pevaluacion', function ($query) use ($lapso) {
                    $query->where('lapso_id', $lapso->id);
                })
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($eifinalks->isEmpty()) {
                return redirect()->back()
                    ->with('warning', 'No se encontraron informes finales para este estudiante en el lapso seleccionado.');
            }

            $eifinalks_oficial = $estudiant->eifinalks()
            ->with([
                'pevaluacion.pensum.grado',
                'pevaluacion.seccion',
                'pevaluacion.lapso',
                'pevaluacion.profesor',
                'expectations.area'
            ])
            ->whereHas('pevaluacion', function ($query) use ($lapso) {
                $query->where('lapso_id', $lapso->id)
                    ->where('status_official', true);
            })
            ->orderBy('order', 'asc')
            ->get();

        $eifinalks_component = $estudiant->eifinalks()
            ->with([
                'pevaluacion.pensum.grado',
                'pevaluacion.seccion',
                'pevaluacion.lapso',
                'pevaluacion.profesor',
                'expectations.area'
            ])
            ->whereHas('pevaluacion', function ($query) use ($lapso) {
                $query->where('lapso_id', $lapso->id)
                    ->where('status_official', false);
            })
            ->orderBy('order', 'asc')
            ->get(); //dd($eifinalks_oficial,$eifinalks_component);

            $profesor = Profesor::where('user_id', Auth::user()->id)->first();
            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $fecha = Carbon::now()->format('d-m-Y h:i A');

            return view('livewire.inicial.formats.eifinalks.index-all', compact(
                'estudiant',
                'lapso',
                'eifinalks',
                'profesor',
                'institucion',
                'fecha',
                'eifinalks_oficial',
                'eifinalks_component',
            ));

        } catch (\Exception $e) {
            Log::error('Error en printAllforLapso: ' . $e->getMessage(), [
                'estudiant_id' => $estudiant->id,
                'lapso_id' => $lapso->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'No se pudieron cargar los informes finales del estudiante.');
        }
    }

    /**
     * Exporta estadísticas a Excel (método adicional)
     */
    public function exportStats(Request $request)
    {
        try {
            $profesor_id = $request->profesor_id;
            $grado_id = $request->grado_id;

            $stats = $this->educationStatsService->getEducationStats($profesor_id, $grado_id);

            // Aquí puedes implementar la lógica de exportación a Excel
            // usando Laravel Excel o similar

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas exportadas correctamente',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error en exportStats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al exportar estadísticas'
            ], 500);
        }
    }

    /**
     * Obtiene un resumen rápido para el dashboard
     */
    public function getDashboardSummary(Request $request)
    {
        try {
            $profesor_id = $request->profesor_id;
            $grado_id = $request->grado_id;

            $summary = [
                'total_records' => 0,
                'recent_activity' => [],
                'pending_tasks' => 0,
                'completion_rate' => 0
            ];

            // Calcular estadísticas básicas
            $stats = $this->educationStatsService->getEducationStats($profesor_id, $grado_id);
            $summary['total_records'] = $stats['totalRecords'];

            // Actividad reciente (últimos 7 días)
            $recentDate = Carbon::now()->subDays(7);

            $recentActivity = collect();

            // Agregar actividad reciente de cada tipo
            $recentPlans = Eiplanningwk::where('created_at', '>=', $recentDate)
                ->when($profesor_id, fn($q) => $q->where('profesor_id', $profesor_id))
                ->when($grado_id, fn($q) => $q->where('grado_id', $grado_id))
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($item) => [
                    'type' => 'Plan Semanal',
                    'title' => 'Nuevo plan semanal creado',
                    'date' => $item->created_at->diffForHumans(),
                    'icon' => 'calendar-week'
                ]);

            $recentActivity = $recentActivity->merge($recentPlans);

            $summary['recent_activity'] = $recentActivity->take(10)->values();

            // Tareas pendientes (proyectos sin finalizar)
            $summary['pending_tasks'] = Eiprojectk::whereNull('ffinal')
                ->when($profesor_id, fn($q) => $q->where('profesor_id', $profesor_id))
                ->when($grado_id, fn($q) => $q->where('grado_id', $grado_id))
                ->count();

            // Tasa de completación
            $totalProjects = $stats['eiprojectks'];
            $completedProjects = $stats['activeProjects'];
            $summary['completion_rate'] = $totalProjects > 0
                ? round(($completedProjects / $totalProjects) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getDashboardSummary: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen del dashboard'
            ], 500);
        }
    }

    public function useCases()
    {
        $user = $this->user;
        $autoridad = $this->autoridad;
        $list_comment_autoridad = $this->list_comment_autoridad;

        // Datos para los casos de uso
        $useCases = $this->getUseCasesData();

        return view('inicials.use-cases', compact('user','autoridad','list_comment_autoridad','useCases'));
    }

    private function getUseCasesData()
    {
        return [
            [
                'id' => 'authentication',
                'title' => 'Autenticación de Usuarios',
                'description' => 'Los docentes inician sesión con credenciales seguras para acceder al sistema.',
                'icon' => 'fas fa-lock',
                'color' => 'primary'
            ],
            [
                'id' => 'weekly-planning',
                'title' => 'Gestión de Planificación Semanal',
                'description' => 'Crear, editar, eliminar y visualizar planes semanales de clase.',
                'icon' => 'fas fa-calendar-week',
                'color' => 'success'
            ],
            [
                'id' => 'classroom-projects',
                'title' => 'Gestión de Proyectos de Aula',
                'description' => 'Registrar y gestionar proyectos pedagógicos desarrollados durante el año escolar.',
                'icon' => 'fas fa-project-diagram',
                'color' => 'info'
            ],
            [
                'id' => 'evaluations',
                'title' => 'Gestión de Evaluaciones',
                'description' => 'Registrar actividades de evaluación y seguimiento del desempeño estudiantil.',
                'icon' => 'fas fa-clipboard-check',
                'color' => 'warning'
            ],
            [
                'id' => 'pedagogical-reports',
                'title' => 'Generación de Informes Pedagógicos',
                'description' => 'Generar informes finales sobre el progreso de los estudiantes.',
                'icon' => 'fas fa-file-alt',
                'color' => 'secondary'
            ],
            [
                'id' => 'special-reports',
                'title' => 'Administración de Informes Especiales',
                'description' => 'Gestionar eventos o situaciones particulares que requieren atención especial.',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'danger'
            ],
            [
                'id' => 'export-print',
                'title' => 'Exportar o Imprimir Formatos',
                'description' => 'Descargar o imprimir documentos en formato PDF listos para presentar oficialmente.',
                'icon' => 'fas fa-print',
                'color' => 'dark'
            ]
        ];
    }
}
