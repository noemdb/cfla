<?php

namespace App\Livewire\Evaluacion\Inicial;

use App\Services\Inicial\RegistroInicial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

/**
 * Base de los componentes de REVISIÓN de la Coordinación de Evaluación.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EL LÍMITE DE ESCRITURA
 * ─────────────────────────────────────────────────────────────────────────────
 * El coordinador NO edita la planificación: es quien la REVISA. Solo puede
 * escribir dos campos, y no por cortesía sino porque son los únicos que el
 * legacy le daba:
 *
 *  · `observacion` en los cuatro documentos de planificación;
 *  · `recomendacion` en el plan de evaluación;
 *  · nada en el informe final (se emite firmado por la docente).
 *
 * Todo lo demás —estrategias, resúmenes, actividades, expectativas— se muestra
 * pero no se toca. Esa frontera está en UN sitio: los componentes públicos
 * heredan de aquí y solo añaden su tabla.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ UNA BASE Y NO 6 CLASES COPIADAS
 * ─────────────────────────────────────────────────────────────────────────────
 * El legacy tuvo 7 componentes en `Livewire/Evaluacion/Inicial/` que Repeatin
 * casi palabra por palabra: mismos props, mismo `mount`, mismo `render`, misma
 * validación de `observacion`, mismo `showSwal`. La única diferencia real entre
 * cuatro de ellos es el MODELO y el nombre de la columna; el quinto añade el
 * filtro por lapso y el sexto no escribe nada.
 *
 * Aquí la diferencia se DECLARA (una constante y dos métodos) en vez de
 * reescribirse. El repositorio pasa de ~1.000 líneas duplicadas a una base +
 * 6 clases de 20 líneas.
 */
abstract class EvaluacionDocumentComponent extends Component
{
    use WireUiActions;

    /**
     * Clave `ei*` del documento en {@see RegistroInicial}.
     *
     * La sobreescribe cada subclase.
     */
    protected string $entidad = '';

    // ─── Filtros que llegan desde el controlador ──────────────────
    // Se pasan como PROPS desde la vista del índice, no se piden al usuario
    // otra vez: el legacy repetía el filtro del formulario GET en cada
    // pestaña y además lo montaba desalineado (sección antes que grado).

    public ?int $profesorId = null;

    public ?int $gradoId = null;

    public ?int $seccionId = null;

    // ─── Modal de revisión ────────────────────────────────────────

    public bool $showRevision = false;

    public ?int $selectedId = null;

    /** Texto del campo revisable (`observacion` o `recomendacion`). */
    public string $revision = '';

    public function mount(?int $profesorId = null, ?int $gradoId = null, ?int $seccionId = null): void
    {
        $this->profesorId = $profesorId;
        $this->gradoId = $gradoId;
        $this->seccionId = $seccionId;
    }

    public function render()
    {
        // Una sola consulta por render: `urls()` recibe los mismos documentos en
        // lugar de volver a pedirlos.
        $items = $this->items();

        return view('livewire.evaluacion.inicial.document-table', [
            'items' => $items,
            'columnas' => $this->columnas(),
            'entidad' => $this->entidad,
            'campoRevision' => $this->campoRevision(),
            'rotuloRevision' => $this->rotuloRevision(),
            'titulo' => $this->titulo(),
            // Las URLs se resuelven AQUÍ, no como closures para la vista: una
            // función pasada a Blade se invoca sin argumentos y fallaría al
            // pedirle el documento.
            'urls' => $this->urls($items),
        ]);
    }

    /**
     * URLs de cada fila, indexadas por id.
     *
     * Se calculan una vez por render en lugar de por fila y por celda.
     *
     * @param  Collection<int, Model>  $items
     * @return array<int|string, array{detalle: string, pdf: string}>
     */
    private function urls(Collection $items): array
    {
        $urls = [];

        foreach ($items as $documento) {
            $urls[$documento->id] = [
                'detalle' => route('evaluacions.inicials.'.$this->entidad.'.show', $documento->id),
                'pdf' => route('evaluacions.inicials.'.$this->entidad.'.format', $documento->id),
            ];
        }

        return $urls;
    }

    /**
     * Documentos a listar, con sus relaciones y los filtros aplicados.
     *
     * @return Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    protected function items(): Collection
    {
        $query = $this->consultaBase()->latest('id');

        $this->aplicarFiltros($query);

        return $query->get();
    }

    /**
     * Consulta del documento con sus relaciones.
     *
     * @return Builder<\Illuminate\Database\Eloquent\Model>
     */
    protected function consultaBase(): Builder
    {
        return RegistroInicial::consulta($this->entidad);
    }

    /**
     * Aplica los tres filtros de la URL.
     *
     * El informe final NO tiene `profesor_id`/`seccion_id`: se heredan de la
     * carga académica, así que el filtro viaja por `pevaluacion`. La subclase
     * lo declara con {@see filtraPorPevaluacion()}.
     */
    protected function aplicarFiltros(Builder $query): void
    {
        if ($this->filtraPorPevaluacion()) {
            $this->aplicarFiltrosPorPevaluacion($query);

            return;
        }

        if ($this->profesorId) {
            $query->where('profesor_id', $this->profesorId);
        }

        if ($this->gradoId) {
            $query->where('grado_id', $this->gradoId);
        }

        if ($this->seccionId) {
            $query->where('seccion_id', $this->seccionId);
        }
    }

    protected function aplicarFiltrosPorPevaluacion(Builder $query): void
    {
        $query->whereHas('pevaluacion', function ($pevaluacion) {
            if ($this->profesorId) {
                $pevaluacion->where('profesor_id', $this->profesorId);
            }

            if ($this->seccionId) {
                $pevaluacion->where('seccion_id', $this->seccionId);
            }
        });

        // El grado llega por el pensum de la carga.
        if ($this->gradoId) {
            $query->whereHas('pevaluacion.pensum', fn ($pensum) => $pensum->where('grado_id', $this->gradoId));
        }
    }

    /** ¿El filtro debe viajar por `pevaluacion` en vez de por columnas propias? */
    protected function filtraPorPevaluacion(): bool
    {
        return false;
    }

    /**
     * Definición de columnas de la tabla.
     *
     * No la declara cada subclase: viene del catálogo
     * ({@see RegistroInicial::columnas()}), que es la MISMA definición que usa
     * la tabla de la perspectiva de Planificación. Que compartan origen es lo que
     * garantiza que ambas perspectivas enseñen lo mismo.
     *
     * @return array<int, array{0: string, 1: callable}>
     */
    protected function columnas(): array
    {
        return RegistroInicial::columnas($this->entidad);
    }

    /** Campo revisable del documento, o `null` si no admite revisión. */
    protected function campoRevision(): ?string
    {
        return RegistroInicial::campoRevision($this->entidad);
    }

    protected function rotuloRevision(): string
    {
        return RegistroInicial::rotuloRevision($this->entidad);
    }

    protected function titulo(): string
    {
        return RegistroInicial::titulo($this->entidad);
    }

    // ─── Revisión ─────────────────────────────────────────────────

    /**
     * Abre el modal de revisión de un documento.
     */
    public function openRevision(?int $id): void
    {
        if (! $this->campoRevision()) {
            abort(404, 'Este documento no admite revisión de la Coordinación.');
        }

        $documento = $this->findDocumento($id);

        $this->selectedId = $documento->id;
        $this->revision = (string) $documento->{$this->campoRevision()};
        $this->showRevision = true;

        $this->resetValidation();
    }

    public function closeRevision(): void
    {
        $this->showRevision = false;
        $this->selectedId = null;
        $this->revision = '';

        $this->resetValidation();
    }

    /**
     * Guarda el campo revisable.
     *
     * `min:5` es la regla del legacy y se conserva: una observación de dos
     * letras no es una observación. Se valida aquí y no en el modelo porque el
     * campo vive en la tabla del documento, no en una de revisión.
     */
    public function saveRevision(): void
    {
        $campo = $this->campoRevision();

        abort_if(! $campo, 404, 'Este documento no admite revisión de la Coordinación.');
        abort_if(! $this->selectedId, 404, 'No hay ningún documento en revisión.');

        $this->validate(
            ['revision' => ['required', 'string', 'min:5']],
            ['revision.required' => 'Escribe la '.$this->rotuloRevision().' del Coordinado'.'.',
                'revision.min' => 'La '.mb_strtolower($this->rotuloRevision()).' debe tener al menos 5 caracteres.'],
            ['revision' => $this->rotuloRevision()]
        );

        $documento = $this->findDocumento($this->selectedId);

        $documento->{$campo} = trim($this->revision);
        $documento->save();

        $this->notification()->success(
            title: $this->rotuloRevision().' guardada',
            description: 'El documento se actualizó correctamente.'
        );

        $this->closeRevision();
    }

    /**
     * Busca un documento por id, con sus relaciones.
     *
     * 404 y no 403: la respuesta no debe confirmar qué ids existen. Aquí no hay
     * propiedad que comprobar (la Coordinación ve todo el módulo), pero un id
     * inexistente tampoco debe colarse en un `save`.
     */
    protected function findDocumento(?int $id)
    {
        abort_if(! $id, 404, 'Documento no especificado.');

        $documento = $this->consultaBase()->whereKey($id)->first();

        abort_if(! $documento, 404, 'Este documento no existe.');

        return $documento;
    }

    /** Recarga de la tabla, útil tras cambiar los filtros desde la URL. */
    public function refrescar(): void
    {
        //
    }
}
