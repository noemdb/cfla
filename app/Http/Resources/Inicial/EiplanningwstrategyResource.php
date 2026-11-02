<?php

namespace App\Http\Resources\Inicial;

use Illuminate\Http\Resources\Json\JsonResource;

class EiplanningwstrategyResource extends JsonResource
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
            'day_of_week' => $this->day_of_week,
            'day_of_week_label' => $this->getDayLabelAttribute(),
            'momento_rutina_diaria' => $this->momento_rutina_diaria,
            'momento_rutina_diaria_label' => $this->getMomentLabelAttribute(),
            'estrategia' => $this->estrategia,
            'estrategia_plain' => strip_tags($this->estrategia),
            'order' => $this->order,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
            
            // Campos específicos por día (legacy)
            'lunes' => $this->lunes,
            'martes' => $this->martes,
            'miercoles' => $this->miercoles,
            'jueves' => $this->jueves,
            'viernes' => $this->viernes,
            
            // URLs de acción
            'links' => [
                'self' => route('api.inicial.eiplanningwstrategies.show', $this->id),
                'update' => route('api.inicial.eiplanningwstrategies.update', $this->id),
                'delete' => route('api.inicial.eiplanningwstrategies.destroy', $this->id),
                'plan' => route('api.inicial.eiplanningwks.show', $this->eiplanningwk_id),
            ]
        ];
    }
}