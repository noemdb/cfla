<?php

namespace App\Http\Requests\Inicial;

/**
 * Validación del INFORME FINAL por estudiante (`eifinalks`).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EL ÚNICO INFORME "POR PERSONA" DEL MÓDULO
 * ─────────────────────────────────────────────────────────────────────────────
 * Los otros cinco documentos son del grupo (grado · sección · periodo). Este es
 * por ESTUDIANTE: un informe por alumno y área. De ahí sus particularidades:
 *
 *  · NO tiene `profesor_id`, `grado_id`, `seccion_id` ni `lapso_id`: todo se
 *    hereda de la carga académica (`pevaluacion_id`, obligatorio). Por eso
 *    `eststudents` (tabla en inglés) es la que se valida, no `estudiantes` como
 *    dice el blueprint legacy.
 *  · `order` es obligatorio en el legacy y sigue siéndolo: el boletín se imprime
 *    y se archiva en ese orden.
 *  · Los 13 campos de texto son `nullable`, salvo `title`. Es deliberado: el
 *    docente redacta el informe a lo largo del periodo y lo que aún no tiene
 *    sentido (participación familiar, conclusiones) no debe bloquear el
 *    guardado.
 *  · Las EXPECTATIVAS no se validan aquí: viven en el pivote
 *    `eifinalk_expectation` y las gestiona `sync()`. Ver
 *    {@see \App\Livewire\Inicial\EifinalkComponent::syncExpectations()}.
 *
 * Claves prefijadas con `eifinalk.` para que los `@error(...)` de la vista las
 * encuentren (ver {@see EiplanningwkRequest}).
 */
class EifinalkRequest extends InicialRequest
{
    /** Ruta de la propiedad que contiene el formulario. */
    public const PREFIX = 'eifinalk';

    public function rules(): array
    {
        return [
            'eifinalk.order' => ['required', 'integer', 'min:1'],

            // La carga académica ancla grado, sección, lapso, asignatura y
            // profesor: es obligatoria y no redundante.
            'eifinalk.pevaluacion_id' => ['required', 'integer', 'exists:pevaluacions,id'],

            // El nombre de la tabla NO se escribe a mano: se pide al modelo.
            // El blueprint legacy dice `estudiantes` y el nombre real en cfla es
            // otro; hardcodearlo aquí ya falló una vez (INSERT contra una tabla
            // inexistente) y el fallo aparece en runtime, no al compilar.
            'eifinalk.estudiant_id' => ['required', 'integer', 'exists:'.$this->tablaEstudiante().',id'],

            'eifinalk.title' => ['required', 'string', 'max:191'],

            'eifinalk.context_group' => ['nullable', 'string'],
            'eifinalk.planing_eject' => ['nullable', 'string'],
            'eifinalk.featured_project' => ['nullable', 'string'],
            'eifinalk.special_activities' => ['nullable', 'string'],
            'eifinalk.achievements' => ['nullable', 'string'],
            'eifinalk.individual_observations' => ['nullable', 'string'],
            'eifinalk.specialist_observation' => ['nullable', 'string'],
            'eifinalk.family_participation' => ['nullable', 'string'],
            'eifinalk.conclusions' => ['nullable', 'string'],
            'eifinalk.recommendations' => ['nullable', 'string'],
            'eifinalk.expected_learnings' => ['nullable', 'string'],
        ];
    }

    /**
     * Los 13 campos de texto son de un solo tipo (`string`), así que el mensaje
     * por defecto basta; solo se personalizan los tres que el docente encalla.
     */
    public function messages(): array
    {
        return [
            'eifinalk.order.required' => 'Indica el orden en que se imprimirá el informe.',
            'eifinalk.order.min' => 'El orden debe ser 1 o mayor.',
            'eifinalk.pevaluacion_id.required' => 'Elige la carga académica: de ella dependen el grado, la sección y el lapso del informe.',
            'eifinalk.estudiant_id.required' => 'Elige el estudiante al que corresponde el informe.',
            'eifinalk.title.required' => 'Ponle un título al informe.',
            'eifinalk.title.max' => 'El título no puede pasar de 191 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'eifinalk.order' => 'orden',
            'eifinalk.pevaluacion_id' => 'carga académica',
            'eifinalk.estudiant_id' => 'estudiante',
            'eifinalk.title' => 'título del informe',
            'eifinalk.context_group' => 'contexto del grupo',
            'eifinalk.planing_eject' => 'planeamiento',
            'eifinalk.featured_project' => 'proyecto destacado',
            'eifinalk.special_activities' => 'actividades especiales',
            'eifinalk.achievements' => 'logros',
            'eifinalk.individual_observations' => 'observaciones individuales',
            'eifinalk.specialist_observation' => 'observación del especialista',
            'eifinalk.family_participation' => 'participación familiar',
            'eifinalk.conclusions' => 'conclusiones',
            'eifinalk.recommendations' => 'recomendaciones',
            'eifinalk.expected_learnings' => 'aprendizajes esperados',
        ];
    }

    /**
     * Tabla de estudiantes, resuelta desde el MODELO y no escrita a mano.
     *
     * El nombre en cfla no coincide con el blueprint legacy (`estudiantes`) ni
     * con la forma inglesa (`eststudents`). Escribirlo literal en la regla costó
     * una tanda entera de tests en rojo por un INSERT contra una tabla
     * inexistente — y el fallo aparece en runtime, no al compilar.
     */
    private function tablaEstudiante(): string
    {
        return (new \App\Models\app\Learner\Estudiant)->getTable();
    }

    /**
     * @return array<string, mixed>
     */
    public function reportData(): array
    {
        return [
            'order' => (int) $this->field('order'),
            'pevaluacion_id' => (int) $this->field('pevaluacion_id'),
            'estudiant_id' => (int) $this->field('estudiant_id'),
            'title' => trim((string) $this->field('title')),

            // "" desde el formulario se normaliza a NULL: los campos opcionales
            // no deben acabar como cadena vacía en el informe.
            'context_group' => $this->texto('context_group'),
            'planing_eject' => $this->texto('planing_eject'),
            'featured_project' => $this->texto('featured_project'),
            'special_activities' => $this->texto('special_activities'),
            'achievements' => $this->texto('achievements'),
            'individual_observations' => $this->texto('individual_observations'),
            'specialist_observation' => $this->texto('specialist_observation'),
            'family_participation' => $this->texto('family_participation'),
            'conclusions' => $this->texto('conclusions'),
            'recommendations' => $this->texto('recommendations'),
            'expected_learnings' => $this->texto('expected_learnings'),
        ];
    }

    /** Texto opcional ya recortado, o `null` si llegó vacío. */
    private function texto(string $key): ?string
    {
        $valor = $this->field($key);

        if ($valor === null) {
            return null;
        }

        $valor = trim((string) $valor);

        return $valor === '' ? null : $valor;
    }
}
