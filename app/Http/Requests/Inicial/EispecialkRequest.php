<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación de la cabecera del PLAN ESPECIAL.
 *
 * ⚠️ FUENTE DE VERDAD = reglas INLINE del legacy (`save()`), no su trait
 * `EispecialkValidateTrait`, que además de exigir `linea_investigacion` y
 * `enfasis_curriculares` en las actividades, declaraba `observacion` como
 * obligatoria cuando el runtime la aceptaba vacía. Blueprint · decisión R1–R5:
 * el runtime manda en F2/F3 (MVP sin romper producción).
 *
 * Reglas idénticas a {@see EiplanningbwkRequest} salvo en dos puntos, que son
 * deliberados y no herencias:
 *  · `justificacion` sustituye a `diagnostico`. El plan especial no arranca de
 *    un diagnóstico del grupo, sino de una razón pedagógica que hay que
 *    argumentar; por eso el mensaje de ayuda pide el porqué y no la situación
 *    de partida.
 *  · NO hay campo de proyecto vinculado: `eispecialks` no tiene columna
 *    `eiprojectk_id` (a diferencia de la semanal y la quincenal, que sí lo
 *    tienen). El plan especial no se subordina a ningún plan.
 *
 * Claves prefijadas con `eispecialk.` para que los `@error(...)` de la vista
 * las encuentren (ver {@see EiplanningwkRequest}).
 */
class EispecialkRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eispecialk';

    public function rules(): array
    {
        return [
            'eispecialk.profesor_id' => ['required', 'integer', 'exists:profesors,id'],
            'eispecialk.grado_id' => ['required', 'integer', 'exists:grados,id'],
            'eispecialk.seccion_id' => ['required', 'integer', 'exists:seccions,id'],

            // R2: coherencia de fechas.
            'eispecialk.finicial' => ['required', 'date'],
            'eispecialk.ffinal' => ['required', 'date', 'after_or_equal:eispecialk.finicial'],

            // R3: manual (el cálculo automático quedó en backlog).
            'eispecialk.tiempo_ejecucion' => ['required', 'integer', 'min:1'],

            // R1: el runtime legacy era min:10, no min:50.
            'eispecialk.justificacion' => ['required', 'string', 'min:10'],
            'eispecialk.observacion' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eispecialk.profesor_id' => 'profesor',
            'eispecialk.grado_id' => 'grado',
            'eispecialk.seccion_id' => 'sección',
            'eispecialk.finicial' => 'fecha de inicio',
            'eispecialk.ffinal' => 'fecha de culminación',
            'eispecialk.tiempo_ejecucion' => 'cantidad de semanas',
            'eispecialk.justificacion' => 'justificación del plan',
            'eispecialk.observacion' => 'observación',
        ];
    }

    public function messages(): array
    {
        return [
            'eispecialk.profesor_id.required' => 'No se pudo identificar al docente.',
            'eispecialk.grado_id.required' => 'Debes elegir el grado.',
            'eispecialk.seccion_id.required' => 'Debes elegir la sección.',
            'eispecialk.finicial.required' => 'Debes indicar la fecha de inicio del plan.',
            'eispecialk.ffinal.required' => 'Debes indicar la fecha de culminación.',
            'eispecialk.ffinal.after_or_equal' => 'La fecha de culminación no puede ser anterior a la de inicio.',
            'eispecialk.tiempo_ejecucion.min' => 'El plan especial dura al menos 1 semana.',
            'eispecialk.justificacion.min' => 'La justificación debe tener al menos 10 caracteres.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function planData(): array
    {
        return [
            'profesor_id' => (int) $this->field('profesor_id'),
            'grado_id' => (int) $this->field('grado_id'),
            'seccion_id' => (int) $this->field('seccion_id'),
            'finicial' => $this->field('finicial'),
            'ffinal' => $this->field('ffinal'),
            'tiempo_ejecucion' => (int) $this->field('tiempo_ejecucion'),
            'justificacion' => trim((string) $this->field('justificacion')),
            'observacion' => $this->field('observacion') ?: null,
        ];
    }
}
