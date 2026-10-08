<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación del RESUMEN POR ÁREA del proyecto de aula.
 *
 * Misma política R5 que {@see EiplanningwsummaryRequest}: los 4 campos de
 * información general obligatorios, línea de investigación y énfasis
 * curriculares opcionales.
 *
 * DIFERENCIA con los otros dos resúmenes: `eiprojectsummaries` tiene una
 * columna `estrategias` que el legacy no validaba. Se acepta como texto libre
 * (`nullable`) en vez de inventarle un formato: el documento original la
 * imprime como celda de tabla.
 *
 * Claves prefijadas con `eiprojectsummary.` por el mismo motivo que en
 * {@see EiplanningwkRequest}.
 */
class EiprojectsummaryRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eiprojectsummary';

    public function rules(): array
    {
        return [
            'eiprojectsummary.eiprojectk_id' => ['required', 'integer', 'exists:eiprojectks,id'],
            'eiprojectsummary.pevaluacion_id' => ['required', 'integer', 'exists:pevaluacions,id'],

            // R5: los 4 campos de información general son obligatorios.
            'eiprojectsummary.componente' => ['required', 'string'],
            'eiprojectsummary.objetivo' => ['required', 'string'],
            'eiprojectsummary.aprendizaje_esperado' => ['required', 'string'],
            'eiprojectsummary.indicadores' => ['required', 'string'],

            'eiprojectsummary.linea_investigacion' => ['nullable', 'string'],
            'eiprojectsummary.enfasis_curriculares' => ['nullable', 'string'],

            // Columna propia de este documento: el legacy no la validaba ni la
            // rellenaba, pero existe y se imprime.
            'eiprojectsummary.estrategias' => ['nullable', 'string'],

            'eiprojectsummary.order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eiprojectsummary.pevaluacion_id' => 'área de aprendizaje',
            'eiprojectsummary.componente' => 'componente',
            'eiprojectsummary.objetivo' => 'objetivo',
            'eiprojectsummary.aprendizaje_esperado' => 'aprendizaje esperado',
            'eiprojectsummary.indicadores' => 'indicadores',
            'eiprojectsummary.linea_investigacion' => 'línea de investigación',
            'eiprojectsummary.enfasis_curriculares' => 'énfasis curriculares',
            'eiprojectsummary.estrategias' => 'estrategias',
            'eiprojectsummary.order' => 'orden',
        ];
    }

    public function messages(): array
    {
        return [
            'eiprojectsummary.pevaluacion_id.required' => 'Debes elegir el área de aprendizaje del resumen.',
            'eiprojectsummary.componente.required' => 'El componente es obligatorio.',
            'eiprojectsummary.objetivo.required' => 'El objetivo es obligatorio.',
            'eiprojectsummary.aprendizaje_esperado.required' => 'El aprendizaje esperado es obligatorio.',
            'eiprojectsummary.indicadores.required' => 'Los indicadores son obligatorios.',
            'eiprojectsummary.order.min' => 'El orden debe ser 1 o mayor.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function summaryData(): array
    {
        return [
            'eiprojectk_id' => (int) $this->field('eiprojectk_id'),
            'pevaluacion_id' => (int) $this->field('pevaluacion_id'),
            'componente' => trim((string) $this->field('componente')),
            'objetivo' => trim((string) $this->field('objetivo')),
            'aprendizaje_esperado' => trim((string) $this->field('aprendizaje_esperado')),
            'indicadores' => trim((string) $this->field('indicadores')),
            'linea_investigacion' => $this->field('linea_investigacion') ?: null,
            'enfasis_curriculares' => $this->field('enfasis_curriculares') ?: null,
            'estrategias' => $this->field('estrategias') ?: null,
            // "" desde el formulario se normaliza a NULL.
            'order' => $this->field('order') ?: null,
        ];
    }
}
