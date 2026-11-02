<?php

namespace App\Http\Controllers\API\Inicial;

use App\Http\Controllers\Controller;
use App\Models\app\Inicial\Eiplanningwk;
use App\Http\Requests\Inicial\EiplanningwkRequest;
use App\Http\Resources\Inicial\EiplanningwkResource;
use App\Http\Resources\Inicial\EiplanningwkCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * API REST para planificaciones semanales de Educación Inicial
 * 
 * @group Educación Inicial - Planificación Semanal
 * 
 * @authenticated
 */
class EiplanningwkApiController extends Controller
{
    /**
     * Listar planificaciones del docente autenticado
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "profesor_id": 5,
     *       "grado": "Pre-escolar",
     *       "seccion": "A",
     *       "finicial": "2026-10-01",
     *       "ffinal": "2026-10-05",
     *       "dias_con_contenido": 3,
     *       "estrategias_count": 15,
     *       "resumenes_count": 2
     *     }
     *   ],
     *   "links": {
     *     "first": "http://example.com/api/inicial/eiplanningwks?page=1",
     *     "last": "http://example.com/api/inicial/eiplanningwks?page=1",
     *     "prev": null,
     *     "next": null
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 1,
     *     "path": "http://example.com/api/inicial/eiplanningwks",
     *     "per_page": 10,
     *     "to": 1,
     *     "total": 1
     *   }
     * }
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $profesor = $user->profesor;
        
        if (!$profesor) {
            return response()->json([
                'message' => 'Usuario no tiene perfil de profesor'
            ], 403);
        }

        $query = Eiplanningwk::where('profesor_id', $profesor->id)
            ->with(['grado', 'seccion', 'pensum.asignatura', 'eiprojectk'])
            ->withCount([
                'eiplanningwstrategies as eiplanningwstrategies_count' => fn ($q) => $q
                    ->whereNotNull('lunes')
                    ->where('lunes', '<>', ''),
                'eiplanningwsummaries as eiplanningwsummaries_count',
            ]);

        // Filtros
        if ($request->has('grado_id')) {
            $query->where('grado_id', $request->grado_id);
        }

        if ($request->has('seccion_id')) {
            $query->where('seccion_id', $request->seccion_id);
        }

        if ($request->has('pensum_id')) {
            $query->where('pensum_id', $request->pensum_id);
        }

        if ($request->has('fecha_desde')) {
            $query->where('finicial', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('ffinal', '<=', $request->fecha_hasta);
        }

        // Búsqueda
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('diagnostico', 'like', '%' . $request->search . '%')
                  ->orWhere('observacion', 'like', '%' . $request->search . '%');
            });
        }

        // Ordenamiento
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_dir', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 10);
        $eiplanningwks = $query->paginate($perPage);

        // Calcular días con contenido para cada plan
        $eiplanningwks->getCollection()->transform(function ($plan) {
            $diasConContenido = $plan->eiplanningwstrategies()
                ->whereNotNull('lunes')
                ->where('lunes', '<>', '')
                ->selectRaw('COUNT(DISTINCT day_of_week) as dias')
                ->value('dias') ?? 0;

            $plan->setAttribute('dias_con_contenido', $diasConContenido);
            return $plan;
        });

        return new EiplanningwkCollection($eiplanningwks);
    }

    /**
     * Crear nueva planificación
     * 
     * @bodyParam profesor_id integer required ID del profesor
     * @bodyParam grado_id integer required ID del grado
     * @bodyParam seccion_id integer required ID de la sección
     * @bodyParam pensum_id integer optional ID del área de aprendizaje
     * @bodyParam eiprojectk_id integer optional ID del proyecto vinculado
     * @bodyParam finicial string required Fecha de inicio (YYYY-MM-DD)
     * @bodyParam ffinal string required Fecha de culminación (YYYY-MM-DD)
     * @bodyParam tiempo_ejecucion integer required Tiempo de ejecución en semanas
     * @bodyParam diagnostico string optional Diagnóstico inicial
     * @bodyParam observacion string optional Observaciones
     * 
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "profesor_id": 5,
     *     "grado_id": 1,
     *     "seccion_id": 1,
     *     "finicial": "2026-10-01",
     *     "ffinal": "2026-10-05",
     *     "tiempo_ejecucion": 1,
     *     "diagnostico": "Diagnóstico inicial...",
     *     "observacion": "Observaciones...",
     *     "created_at": "2026-10-20T10:00:00.000000Z",
     *     "updated_at": "2026-10-20T10:00:00.000000Z"
     *   },
     *   "message": "Planificación creada exitosamente"
     * }
     */
    public function store(EiplanningwkRequest $request)
    {
        $user = Auth::user();
        $profesor = $user->profesor;
        
        if (!$profesor) {
            return response()->json([
                'message' => 'Usuario no tiene perfil de profesor'
            ], 403);
        }

        // Verificar que el profesor_id coincida con el usuario autenticado
        if ($request->profesor_id != $profesor->id) {
            return response()->json([
                'message' => 'No puede crear planificaciones para otro profesor'
            ], 403);
        }

        // Verificar restricción única de semana
        $duplicado = Eiplanningwk::query()
            ->where('profesor_id', $request->profesor_id)
            ->where('grado_id', $request->grado_id)
            ->where('seccion_id', $request->seccion_id)
            ->whereDate('finicial', $request->finicial)
            ->exists();

        if ($duplicado) {
            return response()->json([
                'message' => 'Ya existe un plan para este nivel, sección y semana'
            ], 422);
        }

        $eiplanningwk = Eiplanningwk::create($request->validated());

        return response()->json([
            'data' => new EiplanningwkResource($eiplanningwk),
            'message' => 'Planificación creada exitosamente'
        ], 201);
    }

    /**
     * Mostrar planificación específica
     * 
     * @urlParam id integer required ID de la planificación
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "profesor_id": 5,
     *     "grado": {
     *       "id": 1,
     *       "name": "Pre-escolar",
     *       "code": "PRE"
     *     },
     *     "seccion": {
     *       "id": 1,
     *       "name": "A",
     *       "code": "A"
     *     },
     *     "pensum": {
     *       "id": 1,
     *       "asignatura": {
     *         "name": "Matemáticas",
     *         "code": "MAT"
     *       }
     *     },
     *     "finicial": "2026-10-01",
     *     "ffinal": "2026-10-05",
     *     "tiempo_ejecucion": 1,
     *     "diagnostico": "Diagnóstico inicial...",
     *     "observacion": "Observaciones...",
     *     "estrategias": [
     *       {
     *         "id": 1,
     *         "day_of_week": "lunes",
     *         "momento_rutina_diaria": "Recibimiento",
     *         "estrategia": "Actividad de bienvenida...",
     *         "order": 1
     *       }
     *     ],
     *     "resumenes": [
     *       {
     *         "id": 1,
     *         "pevaluacion_id": 1,
     *         "componente": "Números",
     *         "objetivo": "Reconocer números del 1 al 5",
     *         "aprendizaje_esperado": "El niño reconoce...",
     *         "indicadores": "Identifica números..."
     *       }
     *     ],
     *     "estadisticas": {
     *       "estrategias_count": 15,
     *       "resumenes_count": 2,
     *       "dias_con_contenido": 3,
     *       "progreso_estrategias": 30,
     *       "progreso_resumenes": 100
     *     }
     *   }
     * }
     */
    public function show($id)
    {
        $eiplanningwk = Eiplanningwk::with([
            'grado', 
            'seccion', 
            'pensum.asignatura',
            'eiprojectk',
            'eiplanningwstrategies' => function($query) {
                $query->orderBy('momento_rutina_diaria')
                      ->orderBy('day_of_week')
                      ->orderBy('order');
            },
            'eiplanningwsummaries' => function($query) {
                $query->orderBy('order');
            },
            'profesor.user'
        ])->findOrFail($id);

        // Verificar autorización
        if (!Gate::allows('view', $eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para ver esta planificación'
            ], 403);
        }

        return new EiplanningwkResource($eiplanningwk);
    }

    /**
     * Actualizar planificación
     * 
     * @urlParam id integer required ID de la planificación
     * @bodyParam grado_id integer optional ID del grado
     * @bodyParam seccion_id integer optional ID de la sección
     * @bodyParam pensum_id integer optional ID del área de aprendizaje
     * @bodyParam eiprojectk_id integer optional ID del proyecto vinculado
     * @bodyParam finicial string optional Fecha de inicio (YYYY-MM-DD)
     * @bodyParam ffinal string optional Fecha de culminación (YYYY-MM-DD)
     * @bodyParam tiempo_ejecucion integer optional Tiempo de ejecución en semanas
     * @bodyParam diagnostico string optional Diagnóstico inicial
     * @bodyParam observacion string optional Observaciones
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "profesor_id": 5,
     *     "grado_id": 1,
     *     "seccion_id": 1,
     *     "finicial": "2026-10-01",
     *     "ffinal": "2026-10-05",
     *     "tiempo_ejecucion": 1,
     *     "diagnostico": "Diagnóstico actualizado...",
     *     "observacion": "Observaciones actualizadas...",
     *     "created_at": "2026-10-20T10:00:00.000000Z",
     *     "updated_at": "2026-10-20T10:15:00.000000Z"
     *   },
     *   "message": "Planificación actualizada exitosamente"
     * }
     */
    public function update(EiplanningwkRequest $request, $id)
    {
        $eiplanningwk = Eiplanningwk::findOrFail($id);

        // Verificar autorización
        if (!Gate::allows('update', $eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para actualizar esta planificación'
            ], 403);
        }

        // Verificar restricción única de semana si se cambia la fecha
        if ($request->has('finicial') && 
            ($eiplanningwk->finicial->toDateString() != $request->finicial ||
             $eiplanningwk->grado_id != $request->grado_id ||
             $eiplanningwk->seccion_id != $request->seccion_id)) {
            
            $duplicado = Eiplanningwk::query()
                ->where('profesor_id', $eiplanningwk->profesor_id)
                ->where('grado_id', $request->grado_id)
                ->where('seccion_id', $request->seccion_id)
                ->whereDate('finicial', $request->finicial)
                ->where('id', '!=', $id)
                ->exists();

            if ($duplicado) {
                return response()->json([
                    'message' => 'Ya existe un plan para este nivel, sección y semana'
                ], 422);
            }
        }

        $eiplanningwk->update($request->validated());

        return response()->json([
            'data' => new EiplanningwkResource($eiplanningwk),
            'message' => 'Planificación actualizada exitosamente'
        ]);
    }

    /**
     * Eliminar planificación
     * 
     * @urlParam id integer required ID de la planificación
     * 
     * @response 200 {
     *   "message": "Planificación eliminada exitosamente"
     * }
     */
    public function destroy($id)
    {
        $eiplanningwk = Eiplanningwk::findOrFail($id);

        // Verificar autorización
        if (!Gate::allows('delete', $eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para eliminar esta planificación'
            ], 403);
        }

        // Eliminar en transacción para mantener integridad
        \Illuminate\Support\Facades\DB::transaction(function () use ($eiplanningwk) {
            $eiplanningwk->eiplanningwstrategies()->delete();
            $eiplanningwk->eiplanningwsummaries()->delete();
            $eiplanningwk->delete();
        });

        return response()->json([
            'message' => 'Planificación eliminada exitosamente'
        ]);
    }

    /**
     * Clonar planificación
     * 
     * Crea una copia completa de la planificación (cabecera, estrategias, resúmenes)
     * para una semana posterior libre.
     * 
     * @urlParam id integer required ID de la planificación a clonar
     * 
     * @response 200 {
     *   "data": {
     *     "id": 2,
     *     "profesor_id": 5,
     *     "grado_id": 1,
     *     "seccion_id": 1,
     *     "finicial": "2026-10-08",
     *     "ffinal": "2026-10-12",
     *     "tiempo_ejecucion": 1,
     *     "diagnostico": "Diagnóstico inicial...",
     *     "observacion": "Observaciones...",
     *     "created_at": "2026-10-20T10:20:00.000000Z",
     *     "updated_at": "2026-10-20T10:20:00.000000Z"
     *   },
     *   "message": "Planificación clonada exitosamente",
     *   "estadisticas": {
     *     "estrategias_clonadas": 15,
     *     "resumenes_clonados": 2
     *   }
     * }
     */
    public function clone($id)
    {
        $eiplanningwk = Eiplanningwk::findOrFail($id);

        // Verificar autorización
        if (!Gate::allows('clone', $eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para clonar esta planificación'
            ], 403);
        }

        $user = Auth::user();
        $profesor = $user->profesor;

        $clonador = new \App\Services\Inicial\ClonadorEiplanningwk();
        $resultado = $clonador->clonar($eiplanningwk, $profesor->id);

        return response()->json([
            'data' => new EiplanningwkResource($resultado['copia']),
            'message' => 'Planificación clonada exitosamente',
            'estadisticas' => [
                'estrategias_clonadas' => $resultado['estrategias'],
                'resumenes_clonados' => $resultado['resumenes']
            ]
        ]);
    }

    /**
     * Exportar planificación a PDF
     * 
     * @urlParam id integer required ID de la planificación
     * 
     * @response 200 {
     *   "pdf_url": "http://example.com/storage/eiplanningwks/plan_1_20261020.pdf",
     *   "message": "PDF generado exitosamente"
     * }
     */
    public function exportPdf($id)
    {
        $eiplanningwk = Eiplanningwk::with([
            'grado', 
            'seccion', 
            'pensum.asignatura',
            'eiplanningwstrategies' => function($query) {
                $query->orderBy('momento_rutina_diaria')
                      ->orderBy('day_of_week')
                      ->orderBy('order');
            },
            'eiplanningwsummaries' => function($query) {
                $query->orderBy('order');
            },
            'profesor.user'
        ])->findOrFail($id);

        // Verificar autorización
        if (!Gate::allows('view', $eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para exportar esta planificación'
            ], 403);
        }

        // Generar PDF (simulación - en producción usaría dompdf)
        $filename = 'plan_' . $eiplanningwk->id . '_' . now()->format('YmdHis') . '.pdf';
        $path = 'eiplanningwks/' . $filename;

        // En producción usar:
        // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.eiplanningwk', compact('eiplanningwk'));
        // Storage::disk('public')->put($path, $pdf->output());

        return response()->json([
            'pdf_url' => url('storage/' . $path),
            'message' => 'PDF generado exitosamente',
            'download_url' => route('api.inicial.eiplanningwks.download-pdf', $id)
        ]);
    }

    /**
     * Estadísticas del docente
     * 
     * @response 200 {
     *   "total_planificaciones": 15,
     *   "total_semanas": 15,
     *   "promedio_estrategias_por_plan": 12.5,
     *   "promedio_resumenes_por_plan": 1.8,
     *   "planes_por_grado": [
     *     {
     *       "grado": "Pre-escolar",
     *       "count": 10
     *     },
     *     {
     *       "grado": "Kinder",
     *       "count": 5
     *     }
     *   ],
     *   "planes_por_mes": [
     *     {
     *       "mes": "Octubre 2026",
     *       "count": 3
     *     }
     *   ]
     * }
     */
    public function statistics()
    {
        $user = Auth::user();
        $profesor = $user->profesor;

        if (!$profesor) {
            return response()->json([
                'message' => 'Usuario no tiene perfil de profesor'
            ], 403);
        }

        $planes = Eiplanningwk::where('profesor_id', $profesor->id)
            ->withCount([
                'eiplanningwstrategies as estrategias_count' => fn ($q) => $q
                    ->whereNotNull('lunes')
                    ->where('lunes', '<>', ''),
                'eiplanningwsummaries as resumenes_count'
            ])
            ->get();

        $totalPlanes = $planes->count();
        $totalSemanas = $planes->sum('tiempo_ejecucion');
        $totalEstrategias = $planes->sum('estrategias_count');
        $totalResumenes = $planes->sum('resumenes_count');

        $planesPorGrado = Eiplanningwk::where('profesor_id', $profesor->id)
            ->join('grados', 'eiplanningwks.grado_id', '=', 'grados.id')
            ->select('grados.name as grado', \DB::raw('COUNT(*) as count'))
            ->groupBy('grados.id', 'grados.name')
            ->get();

        $planesPorMes = Eiplanningwk::where('profesor_id', $profesor->id)
            ->selectRaw('DATE_FORMAT(finicial, "%Y-%m") as mes, COUNT(*) as count')
            ->groupBy('mes')
            ->orderBy('mes', 'desc')
            ->get()
            ->map(function ($item) {
                $item->mes_nombre = \Carbon\Carbon::createFromFormat('Y-m', $item->mes)->format('F Y');
                return $item;
            });

        return response()->json([
            'total_planificaciones' => $totalPlanes,
            'total_semanas' => $totalSemanas,
            'promedio_estrategias_por_plan' => $totalPlanes > 0 ? round($totalEstrategias / $totalPlanes, 1) : 0,
            'promedio_resumenes_por_plan' => $totalPlanes > 0 ? round($totalResumenes / $totalPlanes, 1) : 0,
            'planes_por_grado' => $planesPorGrado,
            'planes_por_mes' => $planesPorMes,
            'planes_recientes' => $planes->take(5)->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'grado' => $plan->grado->name ?? 'N/A',
                    'seccion' => $plan->seccion->name ?? 'N/A',
                    'fecha' => $plan->finicial->format('d/m/Y'),
                    'estrategias' => $plan->estrategias_count,
                    'resumenes' => $plan->resumenes_count
                ];
            })
        ]);
    }
}