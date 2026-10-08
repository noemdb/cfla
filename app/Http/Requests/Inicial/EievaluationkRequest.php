<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación de la cabecera del PLAN DE EVALUACIÓN.
 *
 * ⚠️ FUENTE DE VERDAD = reglas INLINE del legacy (`save()`), no su trait
 * `EievaluationkValidateTrait`. Blueprint · decisión R1–R5.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EL DOCUMENTO MÁS DISTINTO DEL MÓDULO
 * ─────────────────────────────────────────────────────────────────────────────
 * No se parece a la semanal ni a la quincenal en nada más que el esqueleto:
 *
 *  · tiene `lapso_id` OBLIGATORIO en la cabecera. Es el único documento que se
 *    ancla a un momento del año escolar, porque su contenido son las
 *    posiciones de evaluación de ese periodo;
 *  · NO tiene `tiempo_ejecucion` ni `diagnostico`. Se documenta con
 *    `observaciones` (en plural) y `asistencia`, no con un diagnóstico del
 *    grupo;
 *  · `recomendacion` es el único campo opcional de los tres, y es el que
 *    escribe la Coordinación en la perspectiva de evaluación;
 *  · no hay rejilla de estrategias: sus hijas son POSICIONES por niño
 *    (`eievaluationps`), no celdas día × momento.
 *
 * R2 se mantiene: `ffinal` no puede ser anterior a `finicial`.
 */
class EievaluationkRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eievaluationk';

    public function rules(): array
    {
        return [
            'eievaluationk.profesor_id' => ['required', 'integer', 'exists:profesors,id'],
            'eievaluationk.grado_id' => ['required', 'integer', 'exists:grados,id'],

            // Único documento del módulo con el lapso obligatorio en cabecera.
            'eievaluationk.lapso_id' => ['required', 'integer', 'exists:lapsos,id'],

            'eievaluationk.seccion_id' => ['required', 'integer', 'exists:seccions,id'],

            // R2.
            'eievaluationk.finicial' => ['required', 'date'],
            'eievaluationk.ffinal' => ['required', 'date', 'after_or_equal:eievaluationk.finicial'],

            // R1: el runtime legacy era min:10 (el "≥50" documentado no bloquea).
            'eievaluationk.observaciones' => ['required', 'string', 'min:10'],

            // Lo escribe la Coordinación en la perspectiva de evaluación.
            'eievaluationk.recomendacion' => ['nullable', 'string'],

            // El legacy lo exigía sin min, pero `required` ya rechaza "".
            'eievaluationk.asistencia' => ['required', 'string'],

            // Columna presente en el DDL que el legacy no mencionaba.
            'eievaluationk.observacion' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eievaluationk.profesor_id' => 'profesor',
            'eievaluationk.grado_id' => 'grado',
            'eievaluationk.lapso_id' => 'lapso',
            'eievaluationk.seccion_id' => 'sección',
            'eievaluationk.finicial' => 'fecha de inicio',
            'eievaluationk.ffinal' => 'fecha de culminación',
            'eievaluationk.observaciones' => 'observaciones',
            'eievaluationk.recomendacion' => 'recomendación',
            'eievaluationk.asistencia' => 'asistencia',
            'eievaluationk.observacion' => 'observación',
        ];
    }

    public function messages(): array
    {
        return [
            'eievaluationk.profesor_id.required' => 'No se pudo identificar al docente.',
            'eievaluationk.grado_id.required' => 'Debes elegir el grado.',
            'eievaluationk.lapso_id.required' => 'Debes elegir el lapso: este plan se ancla a un periodo.',
            'eievaluationk.seccion_id.required' => 'Debes elegir la sección.',
            'eievaluationk.finicial.required' => 'Debes indicar la fecha de inicio.',
            'eievaluationk.ffinal.required' => 'Debes indicar la fecha de culminación.',
            'eievaluationk.ffinal.after_or_equal' => 'La fecha de culminación no puede ser anterior a la de inicio.',
            'eievaluationk.observaciones.required' => 'Debes registrar las observaciones.',
            'eievaluationk.observaciones.min' => 'Las observaciones deben tener al menos 10 caracteres.',
            'eievaluationk.asistencia.required' => 'Debes registrar la asistencia del grupo.',
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
            'lapso_id' => (int) $this->field('lapso_id'),
            'seccion_id' => (int) $this->field('seccion_id'),
            'finicial' => $this->field('finicial'),
            'ffinal' => $this->field('ffinal'),
            'observaciones' => trim((string) $this->field('observaciones')),
            'recomendacion' => $this->field('recomendacion') ?: null,
            'asistencia' => trim((string) $this->field('asistencia')),
            'observacion' => $this->field('observacion') ?: null,
        ];
    }
}
