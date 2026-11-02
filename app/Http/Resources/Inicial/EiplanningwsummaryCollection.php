<?php

namespace App\Http\Resources\Inicial;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EiplanningwsummaryCollection extends ResourceCollection
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
            ]
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
                 ->header('X-API-Resource', 'EiplanningwsummaryCollection')
                 ->header('X-Total-Count', $this->total())
                 ->header('X-Per-Page', $this->perPage())
                 ->header('Cache-Control', 'no-cache, private');
    }
}