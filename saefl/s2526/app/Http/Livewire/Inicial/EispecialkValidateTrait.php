<?php

namespace App\Http\Livewire\Inicial;

trait EispecialkValidateTrait
{    
    protected $rules = [
        'eispecialk.profesor_id'=>'required|integer',
        'eispecialk.grado_id'=>'required|integer',
        'eispecialk.seccion_id'=>'required|integer',
        'eispecialk.finicial'=>'required|date',
        'eispecialk.ffinal'=>'required|date',
        'eispecialk.tiempo_ejecucion'=>'required|integer',
        'eispecialk.justificacion'=>'required|string',
        'eispecialk.observacion'=>'required|string',

        'eispecialact.eispecialk_id'=>'required|integer',
        'eispecialact.pevaluacion_id'=>'required|integer',
        'eispecialact.componente'=>'required|string',
        'eispecialact.objetivo'=>'required|string',
        'eispecialact.aprendizaje_esperado'=>'required|string',
        'eispecialact.indicadores'=>'required|string',
        'eispecialact.linea_investigacion'=>'required|string',
        'eispecialact.enfasis_curriculares'=>'required|string',
        'eispecialact.order'=>'nullable|integer',
    ];

    protected function validationAttributes()
    {
        return [
            'eispecialk.profesor_id' => $this->list_comment['profesor_id'],
            'eispecialk.grado_id' => $this->list_comment['grado_id'],
            'eispecialk.seccion_id' => $this->list_comment['seccion_id'],
            'eispecialk.finicial' => $this->list_comment['finicial'],
            'eispecialk.ffinal' => $this->list_comment['ffinal'],
            'eispecialk.tiempo_ejecucion' => $this->list_comment['tiempo_ejecucion'],
            'eispecialk.justificacion' => $this->list_comment['justificacion'],
            'eispecialk.observacion' => $this->list_comment['observacion'],

            'eispecialact.eispecialk_id' => $this->list_comment_activities['eispecialk_id'],
            'eispecialact.pevaluacion_id' => $this->list_comment_activities['pevaluacion_id'],
            'eispecialact.componente' => $this->list_comment_activities['componente'],
            'eispecialact.objetivo' => $this->list_comment_activities['objetivo'],
            'eispecialact.aprendizaje_esperado' => $this->list_comment_activities['aprendizaje_esperado'],
            'eispecialact.indicadores' => $this->list_comment_activities['indicadores'],
            'eispecialact.linea_investigacion' => $this->list_comment_activities['linea_investigacion'],
            'eispecialact.enfasis_curriculares' => $this->list_comment_activities['enfasis_curriculares'],
        ];
    }
}