<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVotingPollRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cambiar a true para permitir todas las solicitudes por ahora
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
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'time_active.required' => 'La duración es obligatoria.',
            'time_active.integer' => 'La duración debe ser un número entero.',
            'time_active.min' => 'La duración mínima es 1 minuto.',
            'time_active.max' => 'La duración máxima es 10080 minutos (1 semana).',
            'options.required' => 'Debe agregar al menos 2 opciones.',
            'options.array' => 'Las opciones deben ser un array.',
            'options.min' => 'Debe agregar al menos 2 opciones.',
            'options.*.label.required' => 'Todas las opciones deben tener un texto.',
            'options.*.label.string' => 'El texto de la opción debe ser una cadena.',
            'options.*.label.max' => 'El texto de la opción no puede tener más de 255 caracteres.',
        ];
    }

    protected function prepareForValidation()
    {
        // Filtrar opciones vacías
        if ($this->has('options')) {
            $options = collect($this->options)
                ->filter(function ($option) {
                    return !empty($option['label']);
                })
                ->values()
                ->toArray();

            $this->merge(['options' => $options]);
        }
    }
}
