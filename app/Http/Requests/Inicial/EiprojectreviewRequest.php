<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación de la REVISIÓN del proyecto de aula (`eiprojectreviews`).
 *
 * ─────────────────────────────────────────────────────────────────
 * BLOQUE QUE NO EXISTE EN LOS OTROS DOCUMENTOS
 * ─────────────────────────────────────────────────────────────────
 * La revisión es la fase 2 del proyecto de aula (doc 04 §4): los docentes
 * documentan cómo llegaron al tema —qué interés detectaron, qué sabe el grupo,
 * qué desean aprender, qué falta, con quién cuenta—.
 *
 * El legacy exigía los SEIS campos. Aquí se mantiene así a conciencia: es el
 * bloque que le da sentido al proyecto frente a un plan de clase suelto, y a
 * diferencia de `diagnostico` (min:50 documentado pero min:10 en runtime) aquí
 * no hay discrepancia entre lo documentado y lo aplicado.
 *
 * `estrategias` es una columna que el legacy no validaba: aquí `nullable`, para
 * que el payload no invente un formato que el documento original no define.
 *
 * Claves prefijadas con `eiprojectreview.` por el mismo motivo que en
 * {@see EiplanningwkRequest}.
 */
class EiprojectreviewRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eiprojectreview';

    public function rules(): array
    {
        return [
            'eiprojectreview.eiprojectk_id' => ['required', 'integer', 'exists:eiprojectks,id'],

            'eiprojectreview.posibles_temas_interes' => ['required', 'string'],
            'eiprojectreview.eleccion_tema_nombre' => ['required', 'string'],
            'eiprojectreview.que_sabe' => ['required', 'string'],
            'eiprojectreview.que_desean_aprender' => ['required', 'string'],
            'eiprojectreview.que_necesitamos' => ['required', 'string'],
            'eiprojectreview.quienes_nos_pueden_apoyar' => ['required', 'string'],

            'eiprojectreview.estrategias' => ['nullable', 'string'],
            'eiprojectreview.order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eiprojectreview.posibles_temas_interes' => 'posibles temas de interés',
            'eiprojectreview.eleccion_tema_nombre' => 'elección del nombre del tema',
            'eiprojectreview.que_sabe' => 'qué sabe el grupo',
            'eiprojectreview.que_desean_aprender' => 'qué desean aprender',
            'eiprojectreview.que_necesitamos' => 'qué necesitamos',
            'eiprojectreview.quienes_nos_pueden_apoyar' => 'quiénes nos pueden apoyar',
            'eiprojectreview.estrategias' => 'estrategias',
            'eiprojectreview.order' => 'orden',
        ];
    }

    public function messages(): array
    {
        return [
            'eiprojectreview.posibles_temas_interes.required' => 'Registra los posibles temas de interés detectados.',
            'eiprojectreview.eleccion_tema_nombre.required' => 'Registra el nombre que tendrá el tema.',
            'eiprojectreview.que_sabe.required' => 'Registra qué sabe el grupo.',
            'eiprojectreview.que_desean_aprender.required' => 'Registra qué desean aprender.',
            'eiprojectreview.que_necesitamos.required' => 'Registra qué necesitamos.',
            'eiprojectreview.quienes_nos_pueden_apoyar.required' => 'Registra quiénes nos pueden apoyar.',
            'eiprojectreview.order.min' => 'El orden debe ser 1 o mayor.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function reviewData(): array
    {
        return [
            'eiprojectk_id' => (int) $this->field('eiprojectk_id'),
            'posibles_temas_interes' => trim((string) $this->field('posibles_temas_interes')),
            'eleccion_tema_nombre' => trim((string) $this->field('eleccion_tema_nombre')),
            'que_sabe' => trim((string) $this->field('que_sabe')),
            'que_desean_aprender' => trim((string) $this->field('que_desean_aprender')),
            'que_necesitamos' => trim((string) $this->field('que_necesitamos')),
            'quienes_nos_pueden_apoyar' => trim((string) $this->field('quienes_nos_pueden_apoyar')),
            'estrategias' => $this->field('estrategias') ?: null,
            'order' => $this->field('order') ?: null,
        ];
    }
}
