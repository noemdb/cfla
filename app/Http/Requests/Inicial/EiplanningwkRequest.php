<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación de la cabecera de la planificación semanal.
 *
 * ⚠️ FUENTE DE VERDAD = reglas INLINE del legacy, no sus traits.
 *
 * El legacy tenía `EiplanningwkValidateTrait` con reglas más estrictas que
 * las que el componente aplicaba de verdad en `save()`. Blueprint · decisión
 * R1–R5: "el runtime manda en F2/F3 (MVP sin romper producción); lo
 * aspiracional va a backlog con dueño pedagógico".
 *
 * Concretamente, `eiplanningwsummaries = 0` en producción demuestra que el
 * docente ya evita la fricción: endurecer validaciones antes de migrar los
 * datos expulsaría el uso.
 *
 * ┌──────┬─────────────────────────────┬──────────────────────────┬─────────┐
 * │ Regla│ Documentada en use-cases    │ Runtime legacy           │ Decisión│
 * ├──────┼─────────────────────────────┼──────────────────────────┼─────────┤
 * │ R1   │ diagnostico min:50          │ min:10                   │ ADOPT   │
 * │ R2   │ ffinal >= finicial          │ sí (after_or_equal)      │ ADOPT   │
 * │ R3   │ tiempo_ejecucion calculado  │ required\|min:1 (manual) │ ADOPT   │
 * │ R4   │ >=3 días con contenido      │ >=1 celda con texto      │ ADOPT   │
 * │ R5   │ resumen 4 campos required   │ idem, línea/énfasis null │ ADOPT   │
 * └──────┴─────────────────────────────┴──────────────────────────┴─────────┘
 *
 * "Diagnóstico de al menos 50 caracteres" sigue visible como ayuda en el
 * formulario, pero no bloquea el guardado.
 *
 * ─────────────────────────────────────────────────────────────────
 * POR QUÉ TODAS LAS CLAVES LLEVAN EL PREFIJO `eiplanningwk.`
 * ─────────────────────────────────────────────────────────────────
 * El formulario Livewire vive en un array de propiedad (`$eiplanningwk`), y
 * los `@error('eiplanningwk.ffinal')` de la vista leen el ErrorBag por esa
 * ruta exacta. Si las reglas se declararan planas (`ffinal => ...`), Laravel
 * metería los mensajes como `ffinal` y la vista NO los mostraría nunca: el
 * error existiría pero quedaría invisible para el docente.
 *
 * Por eso se valida el array ANIDADO completo (`['eiplanningwk' => [...]]`),
 * que es además la forma estándar de validar arrays en Laravel.
 */
class EiplanningwkRequest extends InicialRequest
{
    public function rules(): array
    {
        return [
            'eiplanningwk.profesor_id' => ['required', 'integer', 'exists:profesors,id'],
            'eiplanningwk.grado_id' => ['required', 'integer', 'exists:grados,id'],
            'eiplanningwk.seccion_id' => ['required', 'integer', 'exists:seccions,id'],
            // Área de aprendizaje opcional. La pertenencia al docente se
            // comprueba en el componente (un FormRequest no sabe quién llama).
            'eiplanningwk.pensum_id' => ['nullable', 'integer', 'exists:pensums,id'],
            'eiplanningwk.eiprojectk_id' => ['nullable', 'integer', 'exists:eiprojectks,id'],

            // R2: coherencia de fechas.
            'eiplanningwk.finicial' => ['required', 'date'],
            'eiplanningwk.ffinal' => ['required', 'date', 'after_or_equal:eiplanningwk.finicial'],

            // R3: manual (el cálculo automático quedó en backlog).
            'eiplanningwk.tiempo_ejecucion' => ['required', 'integer', 'min:1'],

            // R1: el runtime legacy era min:10, no min:50.
            'eiplanningwk.diagnostico' => ['required', 'string', 'min:10'],
            'eiplanningwk.observacion' => ['nullable', 'string'],
        ];
    }

    /**
     * El nombre del campo de la BD es `diagnostico` (sin tilde); el mensaje va
     * en español para que el docente entienda qué escribir.
     */
    public function attributes(): array
    {
        return [
            'eiplanningwk.profesor_id' => 'profesor',
            'eiplanningwk.grado_id' => 'grado',
            'eiplanningwk.seccion_id' => 'sección',
            'eiplanningwk.pensum_id' => 'área de aprendizaje',
            'eiplanningwk.eiprojectk_id' => 'proyecto vinculado',
            'eiplanningwk.finicial' => 'fecha de inicio',
            'eiplanningwk.ffinal' => 'fecha de culminación',
            'eiplanningwk.tiempo_ejecucion' => 'cantidad de semanas',
            'eiplanningwk.diagnostico' => 'diagnóstico inicial',
            'eiplanningwk.observacion' => 'observación',
        ];
    }

    public function messages(): array
    {
        return [
            'eiplanningwk.profesor_id.required' => 'No se pudo identificar al docente.',
            'eiplanningwk.grado_id.required' => 'Debes elegir el grado.',
            'eiplanningwk.seccion_id.required' => 'Debes elegir la sección.',
            'eiplanningwk.finicial.required' => 'Debes indicar la fecha de inicio de la semana.',
            'eiplanningwk.ffinal.required' => 'Debes indicar la fecha de culminación.',
            'eiplanningwk.ffinal.after_or_equal' => 'La fecha de culminación no puede ser anterior a la de inicio.',
            'eiplanningwk.tiempo_ejecucion.min' => 'La planificación semanal dura al menos 1 semana.',
            'eiplanningwk.diagnostico.min' => 'El diagnóstico inicial debe tener al menos 10 caracteres.',
            'eiplanningwk.eiprojectk_id.exists' => 'El proyecto vinculado no existe.',
            'eiplanningwk.pensum_id.exists' => 'El área de aprendizaje elegida no existe.',
        ];
    }

    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eiplanningwk';

    /**
     * Datos ya validados y normalizados para el modelo.
     *
     * @return array<string, mixed>
     */
    public function planData(): array
    {
        return [
            'profesor_id' => (int) $this->field('profesor_id'),
            'grado_id' => (int) $this->field('grado_id'),
            'seccion_id' => (int) $this->field('seccion_id'),
            'pensum_id' => $this->field('pensum_id') ?: null,
            // FKs vacías vienen como "" desde el formulario.
            'eiprojectk_id' => $this->field('eiprojectk_id') ?: null,
            'finicial' => $this->field('finicial'),
            'ffinal' => $this->field('ffinal'),
            'tiempo_ejecucion' => (int) $this->field('tiempo_ejecucion'),
            'diagnostico' => trim((string) $this->field('diagnostico')),
            'observacion' => $this->field('observacion') ?: null,
        ];
    }
}
