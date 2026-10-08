<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación de la cabecera de la planificación QUINCENAL.
 *
 * ⚠️ FUENTE DE VERDAD = reglas INLINE del legacy (`save()`), no su trait
 * `EiplanningbwkValidateTrait`, que exigía cosas que el runtime nunca aplicó.
 * Blueprint · decisión R1–R5, idéntica a {@see EiplanningwkRequest}: el
 * runtime manda en F2/F3 (MVP sin romper producción).
 *
 * Las reglas coinciden con las de la semanal porque el documento repite el
 * mismo formulario; se copian en vez de heredarse para que cada documento se
 * pueda endurecer sin arrastrar al otro (ya divergence: la quincenal tiene
 * `description` en su estrategia y la semanal no).
 *
 * Claves prefijadas con `eiplanningbwk.` para que los `@error(...)` de la
 * vista las encuentren: ver el bloque de {@see EiplanningwkRequest}.
 */
class EiplanningbwkRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eiplanningbwk';

    public function rules(): array
    {
        return [
            'eiplanningbwk.profesor_id' => ['required', 'integer', 'exists:profesors,id'],
            'eiplanningbwk.grado_id' => ['required', 'integer', 'exists:grados,id'],
            'eiplanningbwk.seccion_id' => ['required', 'integer', 'exists:seccions,id'],
            'eiplanningbwk.eiprojectk_id' => ['nullable', 'integer', 'exists:eiprojectks,id'],

            // R2: coherencia de fechas.
            'eiplanningbwk.finicial' => ['required', 'date'],
            'eiplanningbwk.ffinal' => ['required', 'date', 'after_or_equal:eiplanningbwk.finicial'],

            // R3: manual (el cálculo automático quedó en backlog).
            'eiplanningbwk.tiempo_ejecucion' => ['required', 'integer', 'min:1'],

            // R1: el runtime legacy era min:10, no min:50.
            'eiplanningbwk.diagnostico' => ['required', 'string', 'min:10'],
            'eiplanningbwk.observacion' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'eiplanningbwk.profesor_id' => 'profesor',
            'eiplanningbwk.grado_id' => 'grado',
            'eiplanningbwk.seccion_id' => 'sección',
            'eiplanningbwk.eiprojectk_id' => 'proyecto vinculado',
            'eiplanningbwk.finicial' => 'fecha de inicio',
            'eiplanningbwk.ffinal' => 'fecha de culminación',
            'eiplanningbwk.tiempo_ejecucion' => 'cantidad de semanas',
            'eiplanningbwk.diagnostico' => 'diagnóstico inicial',
            'eiplanningbwk.observacion' => 'observación',
        ];
    }

    public function messages(): array
    {
        return [
            'eiplanningbwk.profesor_id.required' => 'No se pudo identificar al docente.',
            'eiplanningbwk.grado_id.required' => 'Debes elegir el grado.',
            'eiplanningbwk.seccion_id.required' => 'Debes elegir la sección.',
            'eiplanningbwk.finicial.required' => 'Debes indicar la fecha de inicio de la quincena.',
            'eiplanningbwk.ffinal.required' => 'Debes indicar la fecha de culminación.',
            'eiplanningbwk.ffinal.after_or_equal' => 'La fecha de culminación no puede ser anterior a la de inicio.',
            'eiplanningbwk.tiempo_ejecucion.min' => 'La planificación quincenal dura al menos 1 semana.',
            'eiplanningbwk.diagnostico.min' => 'El diagnóstico inicial debe tener al menos 10 caracteres.',
            'eiplanningbwk.eiprojectk_id.exists' => 'El proyecto vinculado no existe.',
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
