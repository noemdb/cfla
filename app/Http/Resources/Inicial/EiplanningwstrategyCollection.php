<?php

namespace App\Http\Resources\Inicial;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EiplanningwstrategyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'links' => [
                    'first' => $this->url(1),
                    'last' => $this->url($this->lastPage()),
                    'prev' => $this->previousPageUrl(),
                    'next' => $this->nextPageUrl(),
                ],
            ],
            'estadisticas' => $this->when($request->has('plan_id'), function() use ($request) {
                $planId = $request->get('plan_id');
                return [
                    'total_celdas' => 50, // 5 días × 10 momentos
                    'celdas_llenas' => $this->collection->count(),
                    'porcentaje_completado' => round(($this->collection->count() / 50) * 100)
                ];
            })
        ];
    }

    /**
     * Personalizar la respuesta de la colección.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->header('X-API-Version', '1.0')
                 ->header('X-API-Resource', 'EiplanningwstrategyCollection')
                 ->header('X-Total-Count', $this->total())
                 ->header('X-Per-Page', $this->perPage())
                 ->header('Cache-Control', 'no-cache, private');
    }
}