<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación del resumen por área de aprendizaje de una planificación.
 *
 * Misma política que {@see EiplanningwkRequest}: reglas INLINE del legacy
 * (regla R5 del blueprint), no el trait `*ValidateTrait` que además exigía
 * `linea_investigacion` y `enfasis_curriculares`.
 *
 * Claves prefijadas con `eiplanningwsummary.` por el mismo motivo que en
 * {@see EiplanningwkRequest}: los `@error('eiplanningwsummary.componente')` de
 * la vista leen el ErrorBag por esa ruta.
 */
class EiplanningwsummaryRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eiplanningwsummary';

    public function rules(): array
    {
        return [
            'eiplanningwsummary.eiplanningwk_id' => ['required', 'integer', 'exists:eiplanningwks,id'],
            'eiplanningwsummary.pevaluacion_id' => ['required', 'integer', 'exists:pevaluacions,id'],

            // R5: los 4 campos de información general son obligatorios.
            'eiplanningwsummary.componente' => ['required', 'string'],
            'eiplanningwsummary.objetivo' => ['required', 'string'],
            'eiplanningwsummary.aprendizaje_esperado' => ['required', 'string'],
            'eiplanningwsummary.indicadores' => ['required', 'string'],

            // El trait del legacy los exigía; el runtime real no. Van como
            // ayuda, no como filtro.
            'eiplanningwsummary.linea_investigacion' => ['nullable', 'string'],
            'eiplanningwsummary.enfasis_curriculares' => ['nullable', 'string'],

            // `order` es entrada manual y admite NULL (NULLS-LAST al ordenar).
            'eiplanningwsummary.order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eiplanningwsummary.pevaluacion_id' => 'área de aprendizaje',
            'eiplanningwsummary.componente' => 'componente',
            'eiplanningwsummary.objetivo' => 'objetivo',
            'eiplanningwsummary.aprendizaje_esperado' => 'aprendizaje esperado',
            'eiplanningwsummary.indicadores' => 'indicadores',
            'eiplanningwsummary.linea_investigacion' => 'línea de investigación',
            'eiplanningwsummary.enfasis_curriculares' => 'énfasis curriculares',
            'eiplanningwsummary.order' => 'orden',
        ];
    }

    public function messages(): array
    {
        return [
            'eiplanningwsummary.pevaluacion_id.required' => 'Debes elegir el área de aprendizaje del resumen.',
            'eiplanningwsummary.componente.required' => 'El componente es obligatorio.',
            'eiplanningwsummary.objetivo.required' => 'El objetivo es obligatorio.',
            'eiplanningwsummary.aprendizaje_esperado.required' => 'El aprendizaje esperado es obligatorio.',
            'eiplanningwsummary.indicadores.required' => 'Los indicadores son obligatorios.',
            'eiplanningwsummary.order.min' => 'El orden debe ser 1 o mayor.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function summaryData(): array
    {
        return [
            'eiplanningwk_id' => (int) $this->field('eiplanningwk_id'),
            'pevaluacion_id' => (int) $this->field('pevaluacion_id'),
            'componente' => trim((string) $this->field('componente')),
            'objetivo' => trim((string) $this->field('objetivo')),
            'aprendizaje_esperado' => trim((string) $this->field('aprendizaje_esperado')),
            'indicadores' => trim((string) $this->field('indicadores')),
            'linea_investigacion' => $this->field('linea_investigacion') ?: null,
            'enfasis_curriculares' => $this->field('enfasis_curriculares') ?: null,
            // "" desde el formulario se normaliza a NULL.
            'order' => $this->field('order') ?: null,
        ];
    }
}
