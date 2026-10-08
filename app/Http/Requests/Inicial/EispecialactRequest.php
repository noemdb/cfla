<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación de la ACTIVIDAD por área del plan especial (`eispecialacts`).
 *
 * Misma política R5 que {@see EiplanningwsummaryRequest}: los 4 campos de
 * información general obligatorios; línea de investigación y énfasis curriculares
 * opcionales, porque el trait del legacy los exigía pero el runtime no.
 *
 * Lo que NO lleva, a diferencia de `EiprojectsummaryRequest`: la columna
 * `estrategias`. Esa existe solo en `eiprojectsummaries`; en el plan especial las
 * estrategias viven en la rejilla día × momento (`eispecialstrategies`), así que
 * aquí no hay ningún campo de ese nombre que validar.
 *
 * Claves prefijadas con `eispecialact.` por el mismo motivo que en
 * {@see EiplanningwkRequest}.
 */
class EispecialactRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eispecialact';

    public function rules(): array
    {
        return [
            'eispecialact.eispecialk_id' => ['required', 'integer', 'exists:eispecialks,id'],
            'eispecialact.pevaluacion_id' => ['required', 'integer', 'exists:pevaluacions,id'],

            // R5: los 4 campos de información general son obligatorios.
            'eispecialact.componente' => ['required', 'string'],
            'eispecialact.objetivo' => ['required', 'string'],
            'eispecialact.aprendizaje_esperado' => ['required', 'string'],
            'eispecialact.indicadores' => ['required', 'string'],

            // El trait del legacy los exigía; el runtime real no.
            'eispecialact.linea_investigacion' => ['nullable', 'string'],
            'eispecialact.enfasis_curriculares' => ['nullable', 'string'],

            'eispecialact.order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eispecialact.pevaluacion_id' => 'área de aprendizaje',
            'eispecialact.componente' => 'componente',
            'eispecialact.objetivo' => 'objetivo',
            'eispecialact.aprendizaje_esperado' => 'aprendizaje esperado',
            'eispecialact.indicadores' => 'indicadores',
            'eispecialact.linea_investigacion' => 'línea de investigación',
            'eispecialact.enfasis_curriculares' => 'énfasis curriculares',
            'eispecialact.order' => 'orden',
        ];
    }

    public function messages(): array
    {
        return [
            'eispecialact.pevaluacion_id.required' => 'Debes elegir el área de aprendizaje de la actividad.',
            'eispecialact.componente.required' => 'El componente es obligatorio.',
            'eispecialact.objetivo.required' => 'El objetivo es obligatorio.',
            'eispecialact.aprendizaje_esperado.required' => 'El aprendizaje esperado es obligatorio.',
            'eispecialact.indicadores.required' => 'Los indicadores son obligatorios.',
            'eispecialact.order.min' => 'El orden debe ser 1 o mayor.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function activityData(): array
    {
        return [
            'eispecialk_id' => (int) $this->field('eispecialk_id'),
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
