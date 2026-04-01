<?php

namespace App\Livewire;

trait CatchmentValidate
{
    protected function rules()  // Validaciones por paso
    {
        $reglas = [
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'date_birth' => 'required|date|before:today',
            'representant_ci' => 'required', // ajustado a genérico numérico o string
            'representant_name' => 'required|string|max:100',
            'representant_phone' => 'required|numeric|digits:12',
            'representant_cellphone' => 'required|numeric|digits:12',
            'grade' => 'required|integer',
            'day_appointment' => 'required|after_or_equal:'.$this->day_appointment_start.'|before_or_equal:'.$this->day_appointment_end.'',
        ];

        if ($this->wizardFlow === 'A') {
            $reglas['email'] = 'required|email';
            $reglas['input_code'] = 'required|size:6';
        }

        return $reglas;
    }

    protected $validationAttributes = [
        'email' => 'Correo electrónico',
        'input_code' => 'Código de validación',
        'firstname' => 'Nombre',
        'lastname' => 'Apellido',
        'date_birth' => 'Fecha de nacimiento',
        'representant_ci' => 'Cédula del representante',
        'representant_name' => 'Nombre del representante',
        'representant_phone' => 'Teléfono del representante',
        'representant_cellphone' => 'Celular del representante',
        'grade' => 'Grado',
        'day_appointment' => 'Fecha en la que acudirá a la institucción',
    ];

    protected $messages = [
        // 'email.required' => 'El :attribute es obligatorio.',
    ];
}
