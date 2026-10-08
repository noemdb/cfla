<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación de la POSICIÓN de evaluación (`eievaluationps`).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * UNA POSICIÓN = UN NIÑO EN UNA ACTIVIDAD DE EVALUACIÓN
 * ─────────────────────────────────────────────────────────────────────────────
 * A diferencia de los resúmenes, actividades y estrategias de los otros
 * documentos, aquí la unidad no es un área ni una celda día × momento: es el
 * registro de lo que registró un grupo de niños en una actividad de un área.
 * eso el legacy solo exigía DOS campos —`pevaluacion_id` (qué área se evalúa) y
 * el vínculo al plan— y dejó el resto como `nullable`.
 *
 * Es coherente con el uso real: la docente puede ir abriendo una fila por
 * niño o por grupo a lo largo del lapso y completar los campos cuando tenga el
 * dato. Endurecerlo obligaría a crear filas completas de antemano.
 *
 * ÚNICA divergencia respecto al legacy: `fecha` se valida como `date` y no como
 * `string`. La columna es DATE, y con SQL en modo permisivo un texto tipo
 * "05/10/2026" se guardaba como NULL con un warning silencioso: el dato se
 * perdía sin avisar. Ahora se rechaza en el formulario.
 *
 * Claves prefijadas con `eievaluationp.` por el mismo motivo que en
 * {@see EiplanningwkRequest}.
 */
class EievaluationpRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eievaluationp';

    public function rules(): array
    {
        return [
            'eievaluationp.eievaluationk_id' => ['required', 'integer', 'exists:eievaluationks,id'],
            'eievaluationp.pevaluacion_id' => ['required', 'integer', 'exists:pevaluacions,id'],

            // `date`, no `string`: la columna es DATE (ver nota de cabecera).
            'eievaluationp.fecha' => ['nullable', 'date'],

            'eievaluationp.nombre_ninos' => ['nullable', 'string'],
            'eievaluationp.aprendizaje_alcanzado' => ['nullable', 'string'],
            'eievaluationp.componente' => ['nullable', 'string'],
            'eievaluationp.indicadores' => ['nullable', 'string'],
            'eievaluationp.instrumento' => ['nullable', 'string'],
            'eievaluationp.observacion' => ['nullable', 'string'],

            'eievaluationp.order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eievaluationp.pevaluacion_id' => 'área de aprendizaje',
            'eievaluationp.fecha' => 'fecha de la actividad',
            'eievaluationp.nombre_ninos' => 'niños participantes',
            'eievaluationp.aprendizaje_alcanzado' => 'aprendizaje alcanzado',
            'eievaluationp.componente' => 'componente',
            'eievaluationp.indicadores' => 'indicadores',
            'eievaluationp.instrumento' => 'instrumento',
            'eievaluationp.observacion' => 'observación',
            'eievaluationp.order' => 'orden',
        ];
    }

    public function messages(): array
    {
        return [
            'eievaluationp.pevaluacion_id.required' => 'Debes elegir el área de aprendizaje que se evaluó.',
            'eievaluationp.fecha.date' => 'La fecha debe ser válida (AAAA-MM-DD).',
            'eievaluationp.order.min' => 'El orden debe ser 1 o mayor.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function positionData(): array
    {
        return [
            'eievaluationk_id' => (int) $this->field('eievaluationk_id'),
            'pevaluacion_id' => (int) $this->field('pevaluacion_id'),
            'fecha' => $this->field('fecha') ?: null,
            'nombre_ninos' => $this->field('nombre_ninos') ?: null,
            'aprendizaje_alcanzado' => $this->field('aprendizaje_alcanzado') ?: null,
            'componente' => $this->field('componente') ?: null,
            'indicadores' => $this->field('indicadores') ?: null,
            'instrumento' => $this->field('instrumento') ?: null,
            'observacion' => $this->field('observacion') ?: null,
            // "" desde el formulario se normaliza a NULL.
            'order' => $this->field('order') ?: null,
        ];
    }
}
