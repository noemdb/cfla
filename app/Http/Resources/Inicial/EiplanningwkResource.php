<?php

namespace App\Http\Resources\Inicial;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Academy\GradoResource;
use App\Http\Resources\Academy\SeccionResource;
use App\Http\Resources\Academy\PensumResource;
use App\Http\Resources\Inicial\EiprojectkResource;
use App\Http\Resources\Academy\ProfesorResource;
use App\Http\Resources\Inicial\EiplanningwstrategyResource;
use App\Http\Resources\Inicial\EiplanningwsummaryResource;

class EiplanningwkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $estrategiasCount = $this->eiplanningwstrategies()
            ->whereNotNull('lunes')
            ->where('lunes', '<>', '')
            ->count();

        $resumenesCount = $this->eiplanningwsummaries()->count();

        $diasConContenido = $this->eiplanningwstrategies()
            ->whereNotNull('lunes')
            ->where('lunes', '<>', '')
            ->selectRaw('COUNT(DISTINCT day_of_week) as dias')
            ->value('dias') ?? 0;

        $pevaluacionesCount = $this->getPevaluacionsList()->count();
        $progresoResumenes = $pevaluacionesCount > 0 
            ? round(($resumenesCount / $pevaluacionesCount) * 100) 
            : ($resumenesCount > 0 ? 100 : 0);

        $estrategiasTotales = 50; // 5 días × 10 momentos
        $progresoEstrategias = $estrategiasTotales > 0 
            ? round(($estrategiasCount / $estrategiasTotales) * 100) 
            : 0;

        return [
            'id' => $this->id,
            'profesor_id' => $this->profesor_id,
            'grado_id' => $this->grado_id,
            'seccion_id' => $this->seccion_id,
            'pensum_id' => $this->pensum_id,
            'eiprojectk_id' => $this->eiprojectk_id,
            'finicial' => $this->finicial ? $this->finicial->format('Y-m-d') : null,
            'finicial_formatted' => $this->finicial ? $this->finicial->format('d/m/Y') : null,
            'ffinal' => $this->ffinal ? $this->ffinal->format('Y-m-d') : null,
            'ffinal_formatted' => $this->ffinal ? $this->ffinal->format('d/m/Y') : null,
            'tiempo_ejecucion' => $this->tiempo_ejecucion,
            'diagnostico' => $this->diagnostico,
            'observacion' => $this->observacion,
            
            // Relaciones cargadas
            'grado' => new GradoResource($this->whenLoaded('grado')),
            'seccion' => new SeccionResource($this->whenLoaded('seccion')),
            'pensum' => new PensumResource($this->whenLoaded('pensum')),
            'eiprojectk' => new EiprojectkResource($this->whenLoaded('eiprojectk')),
            'profesor' => new ProfesorResource($this->whenLoaded('profesor')),
            
            // Relaciones anidadas
            'estrategias' => EiplanningwstrategyResource::collection(
                $this->whenLoaded('eiplanningwstrategies', function() {
                    return $this->getOrderedStrategies();
                })
            ),
            'resumenes' => EiplanningwsummaryResource::collection(
                $this->whenLoaded('eiplanningwsummaries', function() {
                    return $this->getOrderedSummaries();
                })
            ),
            
            // Estadísticas
            'estadisticas' => $this->when($request->has('include_stats'), [
                'estrategias_count' => $estrategiasCount,
                'resumenes_count' => $resumenesCount,
                'dias_con_contenido' => $diasConContenido,
                'progreso_estrategias' => $progresoEstrategias,
                'progreso_resumenes' => $progresoResumenes,
                'pevaluaciones_esperadas' => $pevaluacionesCount,
                'estrategias_totales' => $estrategiasTotales,
            ]),
            
            // Fechas del sistema
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
            
            // URLs de acción
            'links' => [
                'self' => route('api.inicial.eiplanningwks.show', $this->id),
                'update' => route('api.inicial.eiplanningwks.update', $this->id),
                'delete' => route('api.inicial.eiplanningwks.destroy', $this->id),
                'clone' => route('api.inicial.eiplanningwks.clone', $this->id),
                'export_pdf' => route('api.inicial.eiplanningwks.export-pdf', $this->id),
                'format_view' => route('inicials.eiplanningwks.format.index', $this->id),
            ]
        ];
    }

    /**
     * Personalizar la respuesta del recurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->header('X-API-Version', '1.0')
                 ->header('X-API-Resource', 'Eiplanningwk')
                 ->header('Cache-Control', 'no-cache, private');
    }
}