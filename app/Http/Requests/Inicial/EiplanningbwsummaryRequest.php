<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación del resumen por área de aprendizaje de la planificación quincenal.
 *
 * Misma política que {@see EiplanningwsummaryRequest} (regla R5 del blueprint):
 * los 4 campos de información general son obligatorios; la línea de
 * investigación y los énfasis curriculares NO lo son, porque el legacy exigía
 * solo en su trait y no en el runtime.
 *
 * Claves prefijadas con `eiplanningbwsummary.` por el mismo motivo que en
 * {@see EiplanningwkRequest}.
 */
class EiplanningbwsummaryRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eiplanningbwsummary';

    public function rules(): array
    {
        return [
            'eiplanningbwsummary.eiplanningbwk_id' => ['required', 'integer', 'exists:eiplanningbwks,id'],
            'eiplanningbwsummary.pevaluacion_id' => ['required', 'integer', 'exists:pevaluacions,id'],

            // R5: los 4 campos de información general son obligatorios.
            'eiplanningbwsummary.componente' => ['required', 'string'],
            'eiplanningbwsummary.objetivo' => ['required', 'string'],
            'eiplanningbwsummary.aprendizaje_esperado' => ['required', 'string'],
            'eiplanningbwsummary.indicadores' => ['required', 'string'],

            // El trait del legacy los exigía; el runtime real no. Van como
            // ayuda, no como filtro.
            'eiplanningbwsummary.linea_investigacion' => ['nullable', 'string'],
            'eiplanningbwsummary.enfasis_curriculares' => ['nullable', 'string'],

            'eiplanningbwsummary.order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eiplanningbwsummary.pevaluacion_id' => 'área de aprendizaje',
            'eiplanningbwsummary.componente' => 'componente',
            'eiplanningbwsummary.objetivo' => 'objetivo',
            'eiplanningbwsummary.aprendizaje_esperado' => 'aprendizaje esperado',
            'eiplanningbwsummary.indicadores' => 'indicadores',
            'eiplanningbwsummary.linea_investigacion' => 'línea de investigación',
            'eiplanningbwsummary.enfasis_curriculares' => 'énfasis curriculares',
            'eiplanningbwsummary.order' => 'orden',
        ];
    }

    public function messages(): array
    {
        return [
            'eiplanningbwsummary.pevaluacion_id.required' => 'Debes elegir el área de aprendizaje del resumen.',
            'eiplanningbwsummary.componente.required' => 'El componente es obligatorio.',
            'eiplanningbwsummary.objetivo.required' => 'El objetivo es obligatorio.',
            'eiplanningbwsummary.aprendizaje_esperado.required' => 'El aprendizaje esperado es obligatorio.',
            'eiplanningbwsummary.indicadores.required' => 'Los indicadores son obligatorios.',
            'eiplanningbwsummary.order.min' => 'El orden debe ser 1 o mayor.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function summaryData(): array
    {
        return [
            'eiplanningbwk_id' => (int) $this->field('eiplanningbwk_id'),
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
