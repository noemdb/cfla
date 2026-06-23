<?php

namespace App\Http\Livewire\Inicial;

trait EiprojectkValidateTrait
{
    protected $rules = [
        'eiprojectk.profesor_id'=>'required|integer',
        'eiprojectk.grado_id'=>'required|integer',
        'eiprojectk.seccion_id'=>'required|integer',
        'eiprojectk.finicial'=>'required|date',
        'eiprojectk.ffinal'=>'required|date',
        'eiprojectk.tiempo_ejecucion'=>'required|integer',
        'eiprojectk.diagnostico'=>'required|string',

        'eiprojectreview.eiprojectk_id'=>'required|integer',
        'eiprojectreview.posibles_temas_interes'=>'required|string',
        'eiprojectreview.eleccion_tema_nombre'=>'required|string',
        'eiprojectreview.que_sabe'=>'required|string',
        'eiprojectreview.que_desean_aprender'=>'required|string',
        'eiprojectreview.que_necesitamos'=>'required|string',
        'eiprojectreview.quienes_nos_pueden_apoyar'=>'required|string',
        'eiprojectreview.order'=>'required|string',
        'eiprojectreview.estrategias'=>'nullable|string',

        'eiprojectsummary.eiprojectk_id'=>'required|integer',
        'eiprojectsummary.pevaluacion_id'=>'required|integer',
        'eiprojectsummary.componente'=>'required|string',
        'eiprojectsummary.objetivo'=>'required|string',
        'eiprojectsummary.aprendizaje_esperado'=>'required|string',
        'eiprojectsummary.indicadores'=>'required|string',
        'eiprojectsummary.linea_investigacion'=>'required|string',
        'eiprojectsummary.enfasis_curriculares'=>'required|string',
        'eiprojectsummary.order'=>'nullable|integer',
        'eiprojectsummary.estrategias'=>'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'eiprojectk.profesor_id' => $this->list_comment['profesor_id'],
            'eiprojectk.grado_id' => $this->list_comment['grado_id'],
            'eiprojectk.seccion_id' => $this->list_comment['seccion_id'],
            'eiprojectk.finicial' => $this->list_comment['finicial'],
            'eiprojectk.ffinal' => $this->list_comment['ffinal'],
            'eiprojectk.tiempo_ejecucion' => $this->list_comment['tiempo_ejecucion'],
            'eiprojectk.diagnostico' => $this->list_comment['diagnostico'],

            'eiprojectreview.eiprojectk_id' => $this->list_comment_review['eiprojectk_id'],
            'eiprojectreview.posibles_temas_interes' => $this->list_comment_review['posibles_temas_interes'],
            'eiprojectreview.eleccion_tema_nombre' => $this->list_comment_review['eleccion_tema_nombre'],
            'eiprojectreview.que_sabe' => $this->list_comment_review['que_sabe'],
            'eiprojectreview.que_desean_aprender' => $this->list_comment_review['que_desean_aprender'],
            'eiprojectreview.que_necesitamos' => $this->list_comment_review['que_necesitamos'],
            'eiprojectreview.quienes_nos_pueden_apoyar' => $this->list_comment_review['quienes_nos_pueden_apoyar'],
            'eiprojectreview.order' => $this->list_comment_review['order'],
            'eiprojectreview.estrategias' => $this->list_comment_review['estrategias'],

            'eiprojectsummary.eiprojectk_id' => $this->list_comment_summary['eiprojectk_id'],
            'eiprojectsummary.pevaluacion_id' => $this->list_comment_summary['pevaluacion_id'],
            'eiprojectsummary.componente' => $this->list_comment_summary['componente'],
            'eiprojectsummary.objetivo' => $this->list_comment_summary['objetivo'],
            'eiprojectsummary.aprendizaje_esperado' => $this->list_comment_summary['aprendizaje_esperado'],
            'eiprojectsummary.indicadores' => $this->list_comment_summary['indicadores'],
            'eiprojectsummary.linea_investigacion' => $this->list_comment_summary['linea_investigacion'],
            'eiprojectsummary.enfasis_curriculares' => $this->list_comment_summary['enfasis_curriculares'],
            'eiprojectsummary.order' => $this->list_comment_summary['order'],
            'eiprojectsummary.estrategias' => $this->list_comment_summary['estrategias'],
        ];
    }
}
