<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación de la cabecera del PROYECTO DE AULA.
 *
 * ⚠️ FUENTE DE VERDAD = reglas INLINE del legacy (`save()`), no su trait
 * `EiprojectkValidateTrait`. Blueprint · decisión R1–R5.
 *
 * ┌──────┬──────────────────────────────┬───────────────┬───────────┐
 * │ Regla│ Documentada en use-cases     │ Runtime legacy│ Decisión   │
 * ├──────┼──────────────────────────────┼───────────────┼───────────┤
 * │ R1   │ diagnostico min:50           │ min:10        │ ADOPT      │
 * │ R2   │ ffinal >= finicial           │ sí            │ ADOPT      │
 * │ R3   │ tiempo_ejecucion calculado   │ required|min:1│ ADOPT      │
 * │ R4   │ >=3 días con contenido       │ >=1 celda     │ ADOPT      │
 * │ R5   │ resumen 4 campos required    │ idem          │ ADOPT      │
 * └──────┴──────────────────────────────┴───────────────┴───────────┘
 *
 * DIFERENCIAS con las cabeceras de la semanal y la quincenal (no heredadas a
 * propósito):
 *  · NO hay campo de proyecto vinculado: `eiprojectks` no tiene columna
 *    `eiprojectk_id`. La relación va al revés —los planes apuntan al proyecto—
 *    y la gestiona el componente de planificación, no este formulario.
 *  · `observacion` no estaba en las reglas del legacy pero sí es columna: se
 *    valida como `nullable` en lugar de ignorarse.
 *  · `finicial` / `ffinal` son NULLABLE en el DDL (a diferencia de los planes),
 *    pero el legacy los exigía: se mantienen `required`.
 *
 * Claves prefijadas con `eiprojectk.` para que los `@error(...)` de la vista
 * las encuentren (ver {@see EiplanningwkRequest}).
 */
class EiprojectkRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eiprojectk';

    public function rules(): array
    {
        return [
            'eiprojectk.profesor_id' => ['required', 'integer', 'exists:profesors,id'],
            'eiprojectk.grado_id' => ['required', 'integer', 'exists:grados,id'],
            'eiprojectk.seccion_id' => ['required', 'integer', 'exists:seccions,id'],
            // Área de aprendizaje opcional. La pertenencia al docente se valida en el componente.
            'eiprojectk.pensum_id' => ['nullable', 'integer', 'exists:pensums,id'],

            // R2: coherencia de fechas.
            'eiprojectk.finicial' => ['required', 'date'],
            'eiprojectk.ffinal' => ['required', 'date', 'after_or_equal:eiprojectk.finicial'],

            // R3: manual (el cálculo automático quedó en backlog).
            'eiprojectk.tiempo_ejecucion' => ['required', 'integer', 'min:1'],

            // R1: el runtime legacy era min:10, no min:50.
            'eiprojectk.diagnostico' => ['required', 'string', 'min:10'],
            'eiprojectk.observacion' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eiprojectk.profesor_id' => 'profesor',
            'eiprojectk.grado_id' => 'grado',
            'eiprojectk.seccion_id' => 'sección',
            'eiprojectk.pensum_id' => 'área de aprendizaje',
            'eiprojectk.finicial' => 'fecha de inicio',
            'eiprojectk.ffinal' => 'fecha de culminación',
            'eiprojectk.tiempo_ejecucion' => 'cantidad de semanas',
            'eiprojectk.diagnostico' => 'diagnóstico inicial',
            'eiprojectk.observacion' => 'observación',
        ];
    }

    public function messages(): array
    {
        return [
            'eiprojectk.profesor_id.required' => 'No se pudo identificar al docente.',
            'eiprojectk.grado_id.required' => 'Debes elegir el grado.',
            'eiprojectk.seccion_id.required' => 'Debes elegir la sección.',
            'eiprojectk.finicial.required' => 'Debes indicar la fecha de inicio del proyecto.',
            'eiprojectk.ffinal.required' => 'Debes indicar la fecha de culminación.',
            'eiprojectk.ffinal.after_or_equal' => 'La fecha de culminación no puede ser anterior a la de inicio.',
            'eiprojectk.tiempo_ejecucion.min' => 'El proyecto dura al menos 1 semana.',
            'eiprojectk.diagnostico.min' => 'El diagnóstico inicial debe tener al menos 10 caracteres.',
            'eiprojectk.pensum_id.exists' => 'El área de aprendizaje elegida no existe.',
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
            'pensum_id' => $this->field('pensum_id') ?: null,
            'finicial' => $this->field('finicial'),
            'ffinal' => $this->field('ffinal'),
            'tiempo_ejecucion' => (int) $this->field('tiempo_ejecucion'),
            'diagnostico' => trim((string) $this->field('diagnostico')),
            'observacion' => $this->field('observacion') ?: null,
        ];
    }
}
