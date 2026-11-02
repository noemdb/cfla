<?php

namespace App\Http\Resources\Inicial;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Academy\PevaluacionResource;

class EiplanningwsummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'eiplanningwk_id' => $this->eiplanningwk_id,
            'pevaluacion_id' => $this->pevaluacion_id,
            'componente' => $this->componente,
            'objetivo' => $this->objetivo,
            'aprendizaje_esperado' => $this->aprendizaje_esperado,
            'indicadores' => $this->indicadores,
            'linea_investigacion' => $this->linea_investigacion,
            'enfasis_curriculares' => $this->enfasis_curriculares,
            'order' => $this->order,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
            
            // Relaciones
            'pevaluacion' => new PevaluacionResource($this->whenLoaded('pevaluacion')),
            
            // URLs de acción
            'links' => [
                'self' => route('api.inicial.eiplanningwsummaries.show', $this->id),
                'update' => route('api.inicial.eiplanningwsummaries.update', $this->id),
                'delete' => route('api.inicial.eiplanningwsummaries.destroy', $this->id),
                'plan' => route('api.inicial.eiplanningwks.show', $this->eiplanningwk_id),
            ]
        ];
    }
}