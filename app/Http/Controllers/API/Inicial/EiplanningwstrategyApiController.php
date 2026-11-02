<?php

namespace App\Http\Controllers\API\Inicial;

use App\Http\Controllers\Controller;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiplanningwk;
use App\Http\Requests\Inicial\EiplanningwstrategyRequest;
use App\Http\Resources\Inicial\EiplanningwstrategyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

/**
 * API REST para estrategias de planificación semanal
 * 
 * @group Educación Inicial - Estrategias de Planificación
 * 
 * @authenticated
 */
class EiplanningwstrategyApiController extends Controller
{
    /**
     * Listar estrategias de un plan
     * 
     * @queryParam plan_id integer optional Filtrar por ID de plan
     * @queryParam day_of_week string optional Filtrar por día de la semana
     * @queryParam momento_rutina_diaria string optional Filtrar por momento de rutina
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "eiplanningwk_id": 1,
     *       "day_of_week": "lunes",
     *       "momento_rutina_diaria": "Recibimiento",
     *       "estrategia": "Actividad de bienvenida...",
     *       "order": 1
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $query = Eiplanningwstrategy::query();

        // Filtro por plan
        if ($request->has('plan_id')) {
            $plan = Eiplanningwk::findOrFail($request->plan_id);
            
            if (!Gate::allows('view', $plan)) {
                return response()->json([
                    'message' => 'No autorizado para ver las estrategias de este plan'
                ], 403);
            }
            
            $query->where('eiplanningwk_id', $request->plan_id);
        }

        // Filtro por día de la semana
        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Filtro por momento de rutina
        if ($request->has('momento_rutina_diaria')) {
            $query->where('momento_rutina_diaria', $request->momento_rutina_diaria);
        }

        // Ordenamiento por defecto
        $query->orderBy('momento_rutina_diaria')
              ->orderBy('day_of_week')
              ->orderBy('order');

        $estrategias = $query->paginate($request->get('per_page', 50));

        return EiplanningwstrategyResource::collection($estrategias);
    }

    /**
     * Crear nueva estrategia
     * 
     * @bodyParam eiplanningwk_id integer required ID del plan
     * @bodyParam day_of_week string required Día de la semana (lunes|martes|miercoles|jueves|viernes)
     * @bodyParam momento_rutina_diaria string required Momento de rutina diaria
     * @bodyParam estrategia string required Contenido de la estrategia
     * @bodyParam order integer optional Orden de visualización
     * 
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "eiplanningwk_id": 1,
     *     "day_of_week": "lunes",
     *     "momento_rutina_diaria": "Recibimiento",
     *     "estrategia": "Actividad de bienvenida...",
     *     "order": 1,
     *     "created_at": "2026-10-20T10:00:00.000000Z",
     *     "updated_at": "2026-10-20T10:00:00.000000Z"
     *   },
     *   "message": "Estrategia creada exitosamente"
     * }
     */
    public function store(EiplanningwstrategyRequest $request)
    {
        $plan = Eiplanningwk::findOrFail($request->eiplanningwk_id);

        // Verificar autorización
        if (!Gate::allows('update', $plan)) {
            return response()->json([
                'message' => 'No autorizado para añadir estrategias a este plan'
            ], 403);
        }

        // Verificar que no exista ya una estrategia para esta celda
        $existente = Eiplanningwstrategy::where('eiplanningwk_id', $request->eiplanningwk_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('momento_rutina_diaria', $request->momento_rutina_diaria)
            ->first();

        if ($existente) {
            return response()->json([
                'message' => 'Ya existe una estrategia para esta celda. Use actualizar en su lugar.'
            ], 422);
        }

        $estrategia = Eiplanningwstrategy::create($request->validated());

        return response()->json([
            'data' => new EiplanningwstrategyResource($estrategia),
            'message' => 'Estrategia creada exitosamente'
        ], 201);
    }

    /**
     * Mostrar estrategia específica
     * 
     * @urlParam id integer required ID de la estrategia
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "eiplanningwk_id": 1,
     *     "day_of_week": "lunes",
     *     "momento_rutina_diaria": "Recibimiento",
     *     "estrategia": "Actividad de bienvenida...",
     *     "order": 1
     *   }
     * }
     */
    public function show($id)
    {
        $estrategia = Eiplanningwstrategy::with('eiplanningwk')->findOrFail($id);

        // Verificar autorización a través del plan
        if (!Gate::allows('view', $estrategia->eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para ver esta estrategia'
            ], 403);
        }

        return new EiplanningwstrategyResource($estrategia);
    }

    /**
     * Actualizar estrategia
     * 
     * @urlParam id integer required ID de la estrategia
     * @bodyParam estrategia string optional Contenido de la estrategia
     * @bodyParam order integer optional Orden de visualización
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "eiplanningwk_id": 1,
     *     "day_of_week": "lunes",
     *     "momento_rutina_diaria": "Recibimiento",
     *     "estrategia": "Actividad actualizada...",
     *     "order": 1,
     *     "created_at": "2026-10-20T10:00:00.000000Z",
     *     "updated_at": "2026-10-20T10:15:00.000000Z"
     *   },
     *   "message": "Estrategia actualizada exitosamente"
     * }
     */
    public function update(EiplanningwstrategyRequest $request, $id)
    {
        $estrategia = Eiplanningwstrategy::with('eiplanningwk')->findOrFail($id);

        // Verificar autorización a través del plan
        if (!Gate::allows('update', $estrategia->eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para actualizar esta estrategia'
            ], 403);
        }

        $estrategia->update($request->validated());

        return response()->json([
            'data' => new EiplanningwstrategyResource($estrategia),
            'message' => 'Estrategia actualizada exitosamente'
        ]);
    }

    /**
     * Eliminar estrategia
     * 
     * @urlParam id integer required ID de la estrategia
     * 
     * @response 200 {
     *   "message": "Estrategia eliminada exitosamente"
     * }
     */
    public function destroy($id)
    {
        $estrategia = Eiplanningwstrategy::with('eiplanningwk')->findOrFail($id);

        // Verificar autorización a través del plan
        if (!Gate::allows('update', $estrategia->eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para eliminar esta estrategia'
            ], 403);
        }

        $estrategia->delete();

        return response()->json([
            'message' => 'Estrategia eliminada exitosamente'
        ]);
    }

    /**
     * Crear múltiples estrategias en lote
     * 
     * @bodyParam estrategias array required Array de estrategias
     * @bodyParam estrategias[].eiplanningwk_id integer required ID del plan
     * @bodyParam estrategias[].day_of_week string required Día de la semana
     * @bodyParam estrategias[].momento_rutina_diaria string required Momento de rutina
     * @bodyParam estrategias[].estrategia string required Contenido de la estrategia
     * @bodyParam estrategias[].order integer optional Orden
     * 
     * @response 201 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "eiplanningwk_id": 1,
     *       "day_of_week": "lunes",
     *       "momento_rutina_diaria": "Recibimiento",
     *       "estrategia": "Actividad de bienvenida...",
     *       "order": 1
     *     }
     *   ],
     *   "message": "3 estrategias creadas exitosamente",
     *   "estadisticas": {
     *     "creadas": 3,
     *     "actualizadas": 0,
     *     "fallidas": 0
     *   }
     * }
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'estrategias' => 'required|array|min:1',
            'estrategias.*.eiplanningwk_id' => 'required|exists:eiplanningwks,id',
            'estrategias.*.day_of_week' => 'required|in:lunes,martes,miercoles,jueves,viernes',
            'estrategias.*.momento_rutina_diaria' => 'required|string|max:100',
            'estrategias.*.estrategia' => 'required|string',
            'estrategias.*.order' => 'nullable|integer',
        ]);

        $creadas = 0;
        $actualizadas = 0;
        $fallidas = 0;
        $resultados = [];

        foreach ($request->estrategias as $data) {
            try {
                $plan = Eiplanningwk::findOrFail($data['eiplanningwk_id']);

                // Verificar autorización para cada plan
                if (!Gate::allows('update', $plan)) {
                    $fallidas++;
                    continue;
                }

                // Buscar si ya existe
                $existente = Eiplanningwstrategy::where('eiplanningwk_id', $data['eiplanningwk_id'])
                    ->where('day_of_week', $data['day_of_week'])
                    ->where('momento_rutina_diaria', $data['momento_rutina_diaria'])
                    ->first();

                if ($existente) {
                    // Actualizar existente
                    $existente->update([
                        'estrategia' => $data['estrategia'],
                        'order' => $data['order'] ?? $existente->order,
                    ]);
                    $resultados[] = new EiplanningwstrategyResource($existente);
                    $actualizadas++;
                } else {
                    // Crear nueva
                    $estrategia = Eiplanningwstrategy::create($data);
                    $resultados[] = new EiplanningwstrategyResource($estrategia);
                    $creadas++;
                }
            } catch (\Exception $e) {
                $fallidas++;
                // Registrar error en logs
                \Log::error('Error al procesar estrategia en batch', [
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'data' => $resultados,
            'message' => "Procesamiento de lote completado",
            'estadisticas' => [
                'creadas' => $creadas,
                'actualizadas' => $actualizadas,
                'fallidas' => $fallidas,
                'total' => count($request->estrategias)
            ]
        ], 201);
    }

    /**
     * Actualizar orden de estrategia
     * 
     * @urlParam id integer required ID de la estrategia
     * @bodyParam order integer required Nuevo orden
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "order": 2,
     *     "updated_at": "2026-10-20T10:15:00.000000Z"
     *   },
     *   "message": "Orden actualizado exitosamente"
     * }
     */
    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'order' => 'required|integer|min:0'
        ]);

        $estrategia = Eiplanningwstrategy::with('eiplanningwk')->findOrFail($id);

        // Verificar autorización a través del plan
        if (!Gate::allows('update', $estrategia->eiplanningwk)) {
            return response()->json([
                'message' => 'No autorizado para actualizar esta estrategia'
            ], 403);
        }

        $estrategia->update(['order' => $request->order]);

        return response()->json([
            'data' => [
                'id' => $estrategia->id,
                'order' => $estrategia->order,
                'updated_at' => $estrategia->updated_at->toISOString()
            ],
            'message' => 'Orden actualizado exitosamente'
        ]);
    }
}