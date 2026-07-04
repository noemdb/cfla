<?php

namespace App\Http\Livewire\Administracion\CalendarEvent;

trait CalendarEventTrait
{
    protected $rules = [
        'calendar_event.date' => 'required|date',
        'calendar_event.name' => 'nullable|string|max:255',
        'calendar_event.description' => 'nullable|string',
        'calendar_event.observations' => 'nullable|string',
        'calendar_event.icon' => 'nullable|string|max:50',
        'calendar_event.status_holidays' => 'required|boolean'
    ];

    protected function validationAttributes()
    {
        return [
            'calendar_event.date' => $this->list_comment['date'],
            'calendar_event.name' => $this->list_comment['name'],
            'calendar_event.description' => $this->list_comment['description'],
            'calendar_event.observations' => $this->list_comment['observations'],
            'calendar_event.icon' => $this->list_comment['icon'],
            'calendar_event.status_holidays' => $this->list_comment['status_holidays']
        ];
    }

    // ↓ NUEVO: MENSAJES DE VALIDACIÓN PERSONALIZADOS
    protected function messages()
    {
        return [
            'calendar_event.date.required' => 'La fecha del evento es obligatoria.',
            'calendar_event.date.date' => 'La fecha debe tener un formato válido.',
            'calendar_event.name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'calendar_event.status_holidays.required' => 'Debe especificar si es día feriado.',
        ];
    }
}