<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\Achievement;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

trait ValidateTrait
{
    protected $rules = [
        'activity.pevaluacion_id' => 'required|integer',
        'activity.finicial' => 'required|date',
        'activity.ffinal' => 'required|date',
        'activity.topic' => 'required|string',
        'activity.thematic' => 'required|string',
        'activity.references' => 'required|string',
        'activity.teaching' => 'nullable|string',
        'activity.learning' => 'nullable|string',
        'activity.observations' => 'required|string',
        'activity.description' => 'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'activity.pevaluacion_id' => $this->list_comment['pevaluacion_id'] ?? 'Plan de Evaluación',
            'activity.finicial' => $this->list_comment['finicial'] ?? 'Fecha Inicial',
            'activity.ffinal' => $this->list_comment['ffinal'] ?? 'Fecha Final',
            'activity.topic' => $this->list_comment['topic'] ?? 'Tema',
            'activity.thematic' => $this->list_comment['thematic'] ?? 'Tejido Temático',
            'activity.references' => $this->list_comment['references'] ?? 'Referentes',
            'activity.teaching' => $this->list_comment['teaching'] ?? 'Enseñanza',
            'activity.learning' => $this->list_comment['learning'] ?? 'Aprendizaje',
            'activity.description' => $this->list_comment['description'] ?? 'Descripción',
            'activity.observations' => $this->list_comment['observations'] ?? 'Observaciones',
        ];
    }
}
