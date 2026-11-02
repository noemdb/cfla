<?php

namespace App\Http\Controllers\API\Inicial;

use App\Http\Controllers\Controller;
use App\Models\app\Inicial\Eiplanningwsummary;
use App\Models\app\Inicial\Eiplanningwk;
use App\Http\Requests\Inicial\EiplanningwsummaryRequest;
use App\Http\Resources\Inicial\EiplanningwsummaryResource;
use App\Http\Resources\Inicial\EiplanningwsummaryCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * API REST para resúmenes de planificación semanal
 * 
 * @group Educación Inicial - Resúmenes de Planificación
 * 
 * @authenticated
 */
class EiplanningwsummaryApiController extends Controller
{
    /**
     * Listar resúmenes de un plan
     * 
     * @queryParam plan_id integer optional Filtrar por ID de plan
     * @queryParam pevaluacion_id integer optional Filtrar por ID de pevaluación
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "eiplanningwk_id": 1,
     *       "pevaluacion_id": 1,
     *       "componente": "Números",
     *       "objetivo": "Reconocer números del 1 al 5",
     *       "aprendizaje_esperado": "El niño reconoce...",
     *       "indicadores": "Identifica números...",
     *       "order": 1
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $query = Eiplanningwsummary::query()->with('pevaluacion');

        // Filtro por plan
        if ($request->has('plan_id')) {
            $plan = Eiplanningwk::findOrFail($request->plan_id);
            
            if (!Gate::allows('view', $plan)) {
                return response()->json([
                    'message' => 'No autorizado para ver los resúmenes de este plan'
                ], 403);
            }
            
            $query->where('eiplanningwk_id', $request->plan_id);
        }

        // Filtro por pevaluación
        if ($request->has('pevaluacion_id')) {
            $query->where('pevaluacion_id', $request->pevaluacion_id);
        }

        // Ordenamiento por defecto
        $query->orderBy('order')->orderBy('created_at');

        $resumenes = $query->paginate($request->get('per_page', 20));

        return new EiplanningwsummaryCollection($resumenes);
    }

    /**
     * Crear nuevo resumen
     * 
     * @bodyParam eiplanningwk_id integer required ID del plan
     * @bodyParam pevaluacion_id integer required ID de la pevaluación
     * @bodyParam componente string required Componente del área
     * @bodyParam objetivo string required Objetivo de aprendizaje
     * @bodyParam aprendizaje_esperado string required Aprendizaje esperado
     * @bodyParam indicadores string required Indicadores de logro
     * @bodyParam linea_investigacion string optional Línea de investigación
     * @bodyParam enfasis_curriculares string optional Énfasis curriculares
     * @bodyParam order integer optional Orden de visualización
     * 
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "eiplanningwk_id": 1,
     *     "pevaluacion_id": 1,
     *     "componente": "Números",
     *     "objetivo": "Reconocer números del 1 al 5",
     *     "aprendizaje_esperado": "El niño reconoce...",
     *     "indicadores": "Identifica números...",
     *     "order": 1,
     *     "created_at": "2026-10-20T10:00:00.000000Z",
     *     "updated_at": "2026-10-20T10:00:00.000000Z"
     *   },
     *   "message": "Resumen creado exitosamente"
     * }
     */
    public function store(EiplanningwsummaryRequest $request)
    {
        $plan = Eiplanningwk::findOrFail($request->eiplanningwk_id);

        // Verificar autorización
        if (!Gate::allows('update', $plan)) {
            return response()->json([
                'message' => 'No autorizado para añadir resúmenes a este plan'
            ], 403);
        }

        // Verificar que la pevaluación pertenece al plan
        $pevaluaciones = $plan->getPevaluacionsList();
        if (!$pevaluaciones->has($request->pevaluacion_id)) {
            return response()->json([
                'message' => 'La pevaluación seleccionada no pertenece a este plan'
            ], 422);
        }

        // Verificar que no exista ya un resumen para esta pevaluación en este plan
        $existente = Eiplanningwsummary::where('eiplanningwk_id', $request->eiplanningwk_id)
            ->where('pevaluacion_id', $request->pevaluacion_id)
            ->first();

        if ($existente) {
            return response()->json([
                'message' => 'Ya existe un resumen para esta área de aprendizaje en este plan. Use actualizar en su lugar.'
            ], 422);
        }

        $resumen = Eiplanningwsummary::create($request->validated());

        return response()->json([
            'data' => new EiplanningwsummaryResource($resumen),
            'message' => 'Resumen creado exitosamente'
        ], 201);
    }

    /**
     * Mostrar resumen específico
     * 
     * @urlParam id integer required ID del resumen
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "eiplanningwk_id": 1,
     *     "pevaluacion_id": 1,
     *     "componente": "Números",
     *     "objetivo": "Reconocer números del 1 al 5",
     *     "aprendizaje_esperado": "El niño reconoce...",
     *     "indicadores": "Identifica números...",
     *     "order": 1
     *   }
     * }
     */
    public function show($id)
    {
        $resumen = Eiplanningwsummary::with(['eiplanningwk', 'pevaluacion'])->findOrFail($id);

        // Verificar autorización a través del plan
        if (!Gate::allows('view', $resumen->eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para ver este resumen'
            ], 403);
        }

        return new EiplanningwsummaryResource($resumen);
    }

    /**
     * Actualizar resumen
     * 
     * @urlParam id integer required ID del resumen
     * @bodyParam componente string optional Componente del área
     * @bodyParam objetivo string optional Objetivo de aprendizaje
     * @bodyParam aprendizaje_esperado string optional Aprendizaje esperado
     * @bodyParam indicadores string optional Indicadores de logro
     * @bodyParam linea_investigacion string optional Línea de investigación
     * @bodyParam enfasis_curriculares string optional Énfasis curriculares
     * @bodyParam order integer optional Orden de visualización
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "eiplanningwk_id": 1,
     *     "pevaluacion_id": 1,
     *     "componente": "Números actualizado",
     *     "objetivo": "Reconocer números del 1 al 10",
     *     "aprendizaje_esperado": "El niño reconoce y cuenta...",
     *     "indicadores": "Identifica y relaciona números...",
     *     "order": 1,
     *     "created_at": "2026-10-20T10:00:00.000000Z",
     *     "updated_at": "2026-10-20T10:15:00.000000Z"
     *   },
     *   "message": "Resumen actualizado exitosamente"
     * }
     */
    public function update(EiplanningwsummaryRequest $request, $id)
    {
        $resumen = Eiplanningwsummary::with('eiplanningwk')->findOrFail($id);

        // Verificar autorización a través del plan
        if (!Gate::allows('update', $resumen->eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para actualizar este resumen'
            ], 403);
        }

        // Si se cambia la pevaluación, verificar que pertenece al plan
        if ($request->has('pevaluacion_id') && $request->pevaluacion_id != $resumen->pevaluacion_id) {
            $pevaluaciones = $resumen->eiplanningwk->getPevaluacionsList();
            if (!$pevaluaciones->has($request->pevaluacion_id)) {
                return response()->json([
                    'message' => 'La nueva pevaluación no pertenece a este plan'
                ], 422);
            }

            // Verificar que no exista ya otro resumen para la nueva pevaluación
            $existente = Eiplanningwsummary::where('eiplanningwk_id', $resumen->eiplanningwk_id)
                ->where('pevaluacion_id', $request->pevaluacion_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existente) {
                return response()->json([
                    'message' => 'Ya existe un resumen para esta área de aprendizaje en este plan'
                ], 422);
            }
        }

        $resumen->update($request->validated());

        return response()->json([
            'data' => new EiplanningwsummaryResource($resumen),
            'message' => 'Resumen actualizado exitosamente'
        ]);
    }

    /**
     * Eliminar resumen
     * 
     * @urlParam id integer required ID del resumen
     * 
     * @response 200 {
     *   "message": "Resumen eliminado exitosamente"
     * }
     */
    public function destroy($id)
    {
        $resumen = Eiplanningwsummary::with('eiplanningwk')->findOrFail($id);

        // Verificar autorización a través del plan
        if (!Gate::allows('update', $resumen->eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para eliminar este resumen'
            ], 403);
        }

        $resumen->delete();

        return response()->json([
            'message' => 'Resumen eliminado exitosamente'
        ]);
    }

    /**
     * Listar resúmenes por plan
     * 
     * @urlParam planId integer required ID del plan
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "eiplanningwk_id": 1,
     *       "pevaluacion_id": 1,
     *       "componente": "Números",
     *       "objetivo": "Reconocer números del 1 al 5",
     *       "aprendizaje_esperado": "El niño reconoce...",
     *       "indicadores": "Identifica números...",
     *       "order": 1
     *     }
     *   ],
     *   "plan": {
     *     "id": 1,
     *     "grado": "Pre-escolar",
     *     "seccion": "A",
     *     "fecha": "01/10/2026"
     *   },
     *   "cobertura": {
     *     "total_areas": 5,
     *     "cubiertas": 2,
     *     "porcentaje": 40
     *   }
     * }
     */
    public function byPlan($planId)
    {
        $plan = Eiplanningwk::findOrFail($planId);

        // Verificar autorización
        if (!Gate::allows('view', $plan)) {
            return response()->json([
                'message' => 'No autorizado para ver los resúmenes de este plan'
            ], 403);
        }

        $resumenes = Eiplanningwsummary::where('eiplanningwk_id', $planId)
            ->with('pevaluacion')
            ->orderBy('order')
            ->get();

        // Calcular cobertura
        $areasTotales = $plan->getPevaluacionsList()->count();
        $areasCubiertas = $resumenes->count();
        $porcentajeCobertura = $areasTotales > 0 ? round(($areasCubiertas / $areasTotales) * 100) : 0;

        return response()->json([
            'data' => EiplanningwsummaryResource::collection($resumenes),
            'plan' => [
                'id' => $plan->id,
                'grado' => $plan->grado->name ?? 'N/A',
                'seccion' => $plan->seccion->name ?? 'N/A',
                'fecha' => $plan->finicial->format('d/m/Y'),
                'areas_totales' => $areasTotales
            ],
            'cobertura' => [
                'total_areas' => $areasTotales,
                'cubiertas' => $areasCubiertas,
                'pendientes' => $areasTotales - $areasCubiertas,
                'porcentaje' => $porcentajeCobertura
            ]
        ]);
    }

    /**
     * Validar cobertura de áreas en un plan
     * 
     * @bodyParam plan_id integer required ID del plan
     * 
     * @response 200 {
     *   "plan_id": 1,
     *   "areas_totales": 5,
     *   "areas_cubiertas": 2,
     *   "areas_pendientes": 3,
     *   "porcentaje_cobertura": 40,
     *   "areas_pendientes_list": [
     *     {
     *       "id": 3,
     *       "name": "Matemáticas [MAT] PRE A I",
     *       "asignatura": "Matemáticas"
     *     }
     *   ],
     *   "cumple_minimo": false,
     *   "recomendacion": "Faltan 3 áreas por cubrir. Se recomienda completar al menos 4 áreas (80%)."
     * }
     */
    public function validateCoverage(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:eiplanningwks,id'
        ]);

        $plan = Eiplanningwk::findOrFail($request->plan_id);

        // Verificar autorización
        if (!Gate::allows('view', $plan)) {
            return response()->json([
                'message' => 'No autorizado para validar la cobertura de este plan'
            ], 403);
        }

        $areasTotales = $plan->getPevaluacionsList();
        $resumenes = Eiplanningwsummary::where('eiplanningwk_id', $plan->id)
            ->pluck('pevaluacion_id');

        $areasCubiertas = $resumenes->count();
        $areasPendientesIds = $areasTotales->keys()->diff($resumenes);
        
        // Obtener detalles de áreas pendientes
        $areasPendientes = collect();
        if (!$areasPendientesIds->isEmpty()) {
            $areasPendientes = \App\Models\app\Academy\Pevaluacion::whereIn('id', $areasPendientesIds)
                ->with(['pensum.asignatura', 'grado', 'seccion'])
                ->get()
                ->map(function ($pevaluacion) {
                    return [
                        'id' => $pevaluacion->id,
                        'name' => $pevaluacion->fullname_lg ?? 'Sin nombre',
                        'asignatura' => $pevaluacion->pensum->asignatura->name ?? 'N/A',
                        'grado' => $pevaluacion->grado->code ?? 'N/A',
                        'seccion' => $pevaluacion->seccion->name ?? 'N/A'
                    ];
                });
        }

        $porcentajeCobertura = $areasTotales->count() > 0 
            ? round(($areasCubiertas / $areasTotales->count()) * 100) 
            : 0;

        // Verificar si cumple con mínimo (80% recomendado)
        $cumpleMinimo = $porcentajeCobertura >= 80;
        
        $recomendacion = match (true) {
            $areasTotales->count() === 0 => 'No hay áreas de aprendizaje definidas para esta sección.',
            $areasCubiertas === 0 => 'No se ha cubierto ninguna área. Se recomienda añadir al menos un resumen.',
            $porcentajeCobertura < 50 => 'Cobertura baja. Se recomienda cubrir al menos el 80% de las áreas.',
            $porcentajeCobertura >= 50 && $porcentajeCobertura < 80 => 'Cobertura moderada. Considere añadir más resúmenes.',
            default => 'Cobertura adecuada. ' . ($areasCubiertas < $areasTotales->count() 
                ? 'Aún puede completar las áreas restantes.' 
                : 'Todas las áreas están cubiertas.')
        };

        return response()->json([
            'plan_id' => $plan->id,
            'areas_totales' => $areasTotales->count(),
            'areas_cubiertas' => $areasCubiertas,
            'areas_pendientes' => $areasTotales->count() - $areasCubiertas,
            'porcentaje_cobertura' => $porcentajeCobertura,
            'areas_pendientes_list' => $areasPendientes,
            'cumple_minimo' => $cumpleMinimo,
            'recomendacion' => $recomendacion,
            'fecha_validacion' => now()->toISOString()
        ]);
    }
}