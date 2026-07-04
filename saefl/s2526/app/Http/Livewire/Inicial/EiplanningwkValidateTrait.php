<?php

namespace App\Http\Livewire\Inicial;

trait EiplanningwkValidateTrait
{    
    protected $rules = [
        'eiplanningwk.profesor_id'=>'required|integer',
        'eiplanningwk.grado_id'=>'required|integer',
        'eiplanningwk.seccion_id'=>'required|integer',
        'eiplanningwk.eiprojectk_id'=>'nullable|integer',
        'eiplanningwk.finicial'=>'required|date',
        'eiplanningwk.ffinal'=>'required|date',
        'eiplanningwk.tiempo_ejecucion'=>'required|integer',
        'eiplanningwk.diagnostico'=>'required|string',
        'eiplanningwk.observacion'=>'required|string',

        'eiplanningwsummary.eiplanningwk_id'=>'required|integer',
        'eiplanningwsummary.pevaluacion_id'=>'required|integer',
        'eiplanningwsummary.componente'=>'required|string',
        'eiplanningwsummary.objetivo'=>'required|string',
        'eiplanningwsummary.aprendizaje_esperado'=>'required|string',
        'eiplanningwsummary.indicadores'=>'required|string',
        'eiplanningwsummary.linea_investigacion'=>'required|string',
        'eiplanningwsummary.enfasis_curriculares'=>'required|string',
        'eiplanningwsummary.order'=>'nullable|integer',

        'eiplanningwstrategy.eiplanningwk_id'=>'required|integer',
        'eiplanningwstrategy.momento_rutina_diaria'=>'required|string',
        'eiplanningwstrategy.lunes'=>'required|string',
        'eiplanningwstrategy.martes'=>'required|string',
        'eiplanningwstrategy.miercoles'=>'required|string',
        'eiplanningwstrategy.jueves'=>'required|string',
        'eiplanningwstrategy.viernes'=>'required|string',
        'eiplanningwstrategy.order'=>'nullable|integer',
    ];

    protected function validationAttributes()
    {
        return [
            'eiplanningwk.profesor_id' => $this->list_comment['profesor_id'],
            'eiplanningwk.grado_id' => $this->list_comment['grado_id'],
            'eiplanningwk.seccion_id' => $this->list_comment['seccion_id'],
            'eiplanningwk.eiprojectk_id' => $this->list_comment['eiprojectk_id'],
            'eiplanningwk.finicial' => $this->list_comment['finicial'],
            'eiplanningwk.ffinal' => $this->list_comment['ffinal'],
            'eiplanningwk.tiempo_ejecucion' => $this->list_comment['tiempo_ejecucion'],
            'eiplanningwk.diagnostico' => $this->list_comment['diagnostico'],

            'eiplanningwsummary.eiplanningwk_id' => $this->list_comment_summary['eiplanningwk_id'],
            'eiplanningwsummary.pevaluacion_id' => $this->list_comment_summary['pevaluacion_id'],
            'eiplanningwsummary.componente' => $this->list_comment_summary['componente'],
            'eiplanningwsummary.objetivo' => $this->list_comment_summary['objetivo'],
            'eiplanningwsummary.aprendizaje_esperado' => $this->list_comment_summary['aprendizaje_esperado'],
            'eiplanningwsummary.indicadores' => $this->list_comment_summary['indicadores'],
            'eiplanningwsummary.linea_investigacion' => $this->list_comment_summary['linea_investigacion'],
            'eiplanningwsummary.enfasis_curriculares' => $this->list_comment_summary['enfasis_curriculares'],
            'eiplanningwsummary.order' => $this->list_comment_summary['order'],

            'eiplanningwstrategy.eiplanningwk_id' => $this->list_comment_strategy['eiplanningwk_id'],
            'eiplanningwstrategy.momento_rutina_diaria' => $this->list_comment_strategy['momento_rutina_diaria'],
            'eiplanningwstrategy.lunes' => $this->list_comment_strategy['lunes'],
            'eiplanningwstrategy.martes' => $this->list_comment_strategy['martes'],
            'eiplanningwstrategy.miercoles' => $this->list_comment_strategy['miercoles'],
            'eiplanningwstrategy.jueves' => $this->list_comment_strategy['jueves'],
            'eiplanningwstrategy.viernes' => $this->list_comment_strategy['viernes'],
            'eiplanningwstrategy.order' => $this->list_comment_strategy['order'],
        ];
    }
}