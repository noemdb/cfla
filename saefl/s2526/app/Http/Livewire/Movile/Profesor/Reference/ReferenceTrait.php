<?php

namespace App\Http\Livewire\Movile\Profesor\Reference;

trait ReferenceTrait
{
    public $list_comment;

    // evaluacion_id
    // order
    // title
    // status
    // comments
    // evidence
    // requireds
    
    protected $rules = [
        'evaluacion.pevaluacion_id'=>'required|integer',
        'evaluacion.fecha'=>'required|date',
        'evaluacion.description'=>'required|string',
    ];

    protected function validationAttributes()
    {
        return [
            'evaluacion.pevaluacion_id' => $this->list_comment['pevaluacion_id'],
            'evaluacion.fecha' => $this->list_comment['fecha'],
            'evaluacion.description' => $this->list_comment['description'],
        ];
    }
}

?>
