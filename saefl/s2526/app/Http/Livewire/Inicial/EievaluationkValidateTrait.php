<?php

namespace App\Http\Livewire\Inicial;

trait EievaluationkValidateTrait
{    
    protected $rules = [
        'eievaluationk.profesor_id' => 'required|integer',
        'eievaluationk.grado_id' => 'required|integer',
        'eievaluationk.lapso_id' => 'required|integer',
        'eievaluationk.seccion_id' => 'required|integer',
        'eievaluationk.finicial' => 'required|date',
        'eievaluationk.ffinal' => 'required|date',
        'eievaluationk.observaciones' => 'required|string',
        'eievaluationk.recomendacion' => 'nullable|string',
        'eievaluationk.asistencia' => 'required|string',

        'eievaluationp.eievaluationk_id' => 'required|integer',
        'eievaluationp.pevaluacion_id' => 'required|integer',
        'eievaluationp.fecha' => 'nullable|string',
        'eievaluationp.nombre_ninos' => 'nullable|string',
        'eievaluationp.aprendizaje_alcanzado' => 'nullable|string',
        'eievaluationp.componente' => 'nullable|string',
        'eievaluationp.indicadores' => 'nullable|string',
        'eievaluationp.instrumento' => 'nullable|string',
        'eievaluationp.observacion' => 'nullable|string',
        'eievaluationp.order' => 'nullable|integer',
    ];

    protected function validationAttributes()
    {
        return [
            'eievaluationk.profesor_id' => $this->list_comment['profesor_id'] ?? 'Profesor',
            'eievaluationk.grado_id' => $this->list_comment['grado_id'] ?? 'Grado',
            'eievaluationk.lapso_id' => $this->list_comment['lapso_id'] ?? 'Momento',
            'eievaluationk.seccion_id' => $this->list_comment['seccion_id'] ?? 'Sección',
            'eievaluationk.finicial' => $this->list_comment['finicial'] ?? 'Fecha inicial',
            'eievaluationk.ffinal' => $this->list_comment['ffinal'] ?? 'Fecha final',
            'eievaluationk.observaciones' => $this->list_comment['observaciones'] ?? 'Observaciones',
            'eievaluationk.recomendacion' => $this->list_comment['recomendacion'] ?? 'Recomendación',
            'eievaluationk.asistencia' => $this->list_comment['asistencia'] ?? 'Asistencia',

            'eievaluationp.eievaluationk_id' => $this->list_comment_position['eievaluationk_id'] ?? 'Plan de evaluación',
            'eievaluationp.pevaluacion_id' => $this->list_comment_position['pevaluacion_id'] ?? 'Área de aprendizaje',
            'eievaluationp.fecha' => $this->list_comment_position['fecha'] ?? 'Fecha',
            'eievaluationp.nombre_ninos' => $this->list_comment_position['nombre_ninos'] ?? 'Nombres de niños',
            'eievaluationp.aprendizaje_alcanzado' => $this->list_comment_position['aprendizaje_alcanzado'] ?? 'Aprendizaje alcanzado',
            'eievaluationp.componente' => $this->list_comment_position['componente'] ?? 'Componente',
            'eievaluationp.indicadores' => $this->list_comment_position['indicadores'] ?? 'Indicadores',
            'eievaluationp.instrumento' => $this->list_comment_position['instrumento'] ?? 'Instrumento',
            'eievaluationp.observacion' => $this->list_comment_position['observacion'] ?? 'Observación',
            'eievaluationp.order' => $this->list_comment_position['order'] ?? 'Orden',
        ];
    }
}
