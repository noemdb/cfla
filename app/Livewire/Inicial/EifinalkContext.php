<?php

namespace App\Livewire\Inicial;

/**
 * Datos de contexto del INFORME FINAL que el componente necesita y que en el
 * legacy vivían como accessors dispersos sobre `Pevaluacion` y `Eilearningarea`.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ UNA CLASE Y NO MÁS MÉTODOS EN EL COMPONENTE
 * ─────────────────────────────────────────────────────────────────────────────
 * El legacy resolvía estas cuatro cosas con accessors que NO existen en cfla
 * (`Pevaluacion::list_pevaluacion()`, `Pevaluacion->grado`,
 * `Pevaluacion->estudiantes`), así que hubo que reconstruirlas. Se collects aquí
 * en vez de metódos sueltos del componente por dos razones concretas:
 *
 *  1. Son consultas CARAS (3 joins para las cargas del docente, 2 para las
 *     áreas de un grado, 2 para los estudiantes de una sección) y se necesitan en
 *     varios sitios: el listado, el modal y el acordeón de expectativas.
 *  2. Las necesita el CONTROLADOR del formato imprimible también. Si vivieran
 *     en el componente, el `format()` tendría que duplicarlas.
 *
 * Todas son de SOLO LECTURA sobre el mismo dominio y devuelven
 * `Collection`/`Eilearningarea` ya con las relaciones anidadas.
 */
class EifinalkContext
{
    public function __construct(
        public ?int $profesorId = null,
    ) {}

    /**
     * Cargas académicas (`pevaluacions`) del docente, con lo que el informe
     * necesita para poder escribirse: sección, lapso, grado y asignatura.
     *
     * El informe final NO tiene `grado_id`, `seccion_id` ni `lapso_id`: se
     * heredan de la carga académica. Por eso la pevaluación es obligatoria en el
     * formulario (regla del legacy) y no un dato redundante.
     *
     * Filtra por `pestudio_id = 6`: un docente de Inicial que también da
     * Bachillerato no debe poder emitir informes finales de bachilleres.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\app\Academy\Pevaluacion>
     */
    public function pevaluacions()
    {
        if (! $this->profesorId) {
            return collect();
        }

        return \App\Models\app\Academy\Pevaluacion::query()
            ->select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->where('pevaluacions.profesor_id', $this->profesorId)
            ->where('grados.pestudio_id', config('inicial.pestudio_id'))
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->with(['seccion', 'lapso', 'pensum.asignatura', 'pensum.grado'])
            // El join a `lapsos` existe solo para poder ordenar por el nombre del
            // periodo: la relación se eager-loada aparte para la vista.
            ->orderBy('grados.name')
            ->orderBy('lapsos.name')
            ->get();
    }

    /**
     * Las mismas cargas, como `id => etiqueta` para el `<select>`.
     *
     * Reutiliza {@see pevaluacions()} para que el desplegable y el listado no
     * puedan mostrar conjuntos distintos: el legacy los montaba por separado.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function pevaluacionList()
    {
        return $this->pevaluacions()->mapWithKeys(function ($pevaluacion) {
            $etiqueta = implode(' · ', array_filter([
                $pevaluacion->pensum?->grado?->name,
                $pevaluacion->seccion?->name,
                $pevaluacion->lapso?->name,
                $pevaluacion->pensum?->asignatura?->name,
            ]));

            return [$pevaluacion->id => $etiqueta !== '' ? $etiqueta : "Carga #{$pevaluacion->id}"];
        });
    }

    /**
     * Áreas de aprendizaje del GRADO de una carga, con sus expectativas, para el
     * acordeón de checkboxes del modal.
     *
     * ⚠️ Sin filtro de pestudio a propósito: las áreas se eligen por grado, y el
     * grado ya viene filtrado por pestudio 6 a través de la pevaluación. Aplicar
     * el filtro dos veces solo añadiría una condición que puede desincronizarse.
     *
     * @param  int|null  $gradoId
     * @return \Illuminate\Support\Collection<int, \App\Models\app\Inicial\Eilearningarea>
     */
    public function learningAreas($gradoId)
    {
        if (! $gradoId) {
            return collect();
        }

        return \App\Models\app\Inicial\Eilearningarea::with('expectations')
            ->where('grado_id', $gradoId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Estudiantes matriculados en la SECCIÓN de una carga.
     *
     * La tabla de estudiantes se pide al MODELO (`Estudiant::getTable()`): su
     * nombre en cfla no es el del blueprint legacy, y escribirlo literal en una
     * consulta falla en runtime sin avisar en compilación.
     *
     * El vínculo estudiante↔sección en cfla es `inscripcions`, no un
     * `grado_id`/`seccion_id` directo en `eststudents`: el legacy usaba el
     * accessor `Pevaluacion->estudiantes`, que no existe aquí. Solo se cuentan
     * inscripciones no borradas (`deleted_at IS NULL`).
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\app\Learner\Estudiant>
     */
    public function estudiantesDeSeccion(?int $seccionId)
    {
        if (! $seccionId) {
            return collect();
        }

        $estudiant = new \App\Models\app\Learner\Estudiant;

        // El nombre de la tabla se pide al MODELO: calcarlo al escribirlo dejó una
        // consulta que mezclaba el FROM correcto con alias inexistentes
        // ("Unknown table"), porque ambas tablas comparten `id` y el SELECT
        // necesita calificar.
        $tabla = $estudiant->getTable();

        return $estudiant->newQuery()
            ->select($tabla.'.*')
            ->join('inscripcions', 'inscripcions.estudiant_id', '=', $tabla.'.id')
            ->where('inscripcions.seccion_id', $seccionId)
            ->whereNull('inscripcions.deleted_at')
            ->orderBy($tabla.'.lastname')
            ->orderBy($tabla.'.name')
            ->distinct()
            ->get();
    }
}
