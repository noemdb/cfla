<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVotingPollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'time_active' => 'required|integer|min:1|max:10080',
            'options' => 'required|array|min:2',
            'options.*.label' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'time_active.required' => 'La duración es obligatoria.',
            'time_active.min' => 'La duración mínima es 1 minuto.',
            'time_active.max' => 'La duración máxima es 10080 minutos (1 semana).',
            'options.required' => 'Debe agregar al menos 2 opciones.',
            'options.min' => 'Debe agregar al menos 2 opciones.',
            'options.*.label.required' => 'Todas las opciones deben tener un texto.',
        ];
    }
}
