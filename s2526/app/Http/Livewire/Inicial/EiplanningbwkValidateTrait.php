<?php

namespace App\Http\Livewire\Inicial;

trait EiplanningbwkValidateTrait
{    
    protected $rules = [
        'eiplanningbwk.profesor_id'=>'required|integer',
        'eiplanningbwk.grado_id'=>'required|integer',
        'eiplanningbwk.seccion_id'=>'required|integer',
        'eiplanningbwk.eiprojectk_id'=>'nullable|integer',
        'eiplanningbwk.finicial'=>'required|date',
        'eiplanningbwk.ffinal'=>'required|date',
        'eiplanningbwk.tiempo_ejecucion'=>'required|integer',
        'eiplanningbwk.diagnostico'=>'required|string',
        'eiplanningbwk.observacion'=>'nullable|string',

        'eiplanningbwsummary.eiplanningbwk_id'=>'required|integer',
        'eiplanningbwsummary.pevaluacion_id'=>'required|integer',
        'eiplanningbwsummary.componente'=>'required|string',
        'eiplanningbwsummary.objetivo'=>'required|string',
        'eiplanningbwsummary.aprendizaje_esperado'=>'required|string',
        'eiplanningbwsummary.indicadores'=>'required|string',
        'eiplanningbwsummary.linea_investigacion'=>'required|string',
        'eiplanningbwsummary.enfasis_curriculares'=>'required|string',
        'eiplanningbwsummary.order'=>'nullable|integer',
    ];

    protected function validationAttributes()
    {
        return [
            'eiplanningbwk.profesor_id' => $this->list_comment['profesor_id'],
            'eiplanningbwk.grado_id' => $this->list_comment['grado_id'],
            'eiplanningbwk.seccion_id' => $this->list_comment['seccion_id'],
            'eiplanningbwk.eiprojectk_id' => $this->list_comment['eiprojectk_id'],
            'eiplanningbwk.finicial' => $this->list_comment['finicial'],
            'eiplanningbwk.ffinal' => $this->list_comment['ffinal'],
            'eiplanningbwk.tiempo_ejecucion' => $this->list_comment['tiempo_ejecucion'],
            'eiplanningbwk.diagnostico' => $this->list_comment['diagnostico'],

            'eiplanningbwsummary.eiplanningbwk_id' => $this->list_comment_summary['eiplanningbwk_id'],
            'eiplanningbwsummary.pevaluacion_id' => $this->list_comment_summary['pevaluacion_id'],
            'eiplanningbwsummary.componente' => $this->list_comment_summary['componente'],
            'eiplanningbwsummary.objetivo' => $this->list_comment_summary['objetivo'],
            'eiplanningbwsummary.aprendizaje_esperado' => $this->list_comment_summary['aprendizaje_esperado'],
            'eiplanningbwsummary.indicadores' => $this->list_comment_summary['indicadores'],
            'eiplanningbwsummary.linea_investigacion' => $this->list_comment_summary['linea_investigacion'],
            'eiplanningbwsummary.enfasis_curriculares' => $this->list_comment_summary['enfasis_curriculares'],
            'eiplanningbwsummary.order' => $this->list_comment_summary['order'],
        ];
    }
}