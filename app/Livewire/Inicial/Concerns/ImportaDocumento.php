<?php

namespace App\Livewire\Inicial\Concerns;

use App\Services\Inicial\ImportadorDocumento;
use Illuminate\Support\Collection;

/**
 * Asistente de importación desde s2526, compartido por los componentes de los
 * documentos del módulo.
 *
 * Cada componente que lo usa declara la propiedad `profesor_id` (del docente
 * autenticado) y el método `importador()` que devuelve la instancia de su
 * documento ({@see ImportadorDocumento}). Toda la interacción —abrir/cerrar,
 * importar, filtros, orden, paginación, detalle— vive aquí para no duplicarla
 * entre los 5 documentos de "cabecera + hijas".
 *
 * ⚠️ No declara `viewMode`/`toggleView`: eso es del LISTADO del documento, no
 * del asistente de importación.
 */
trait ImportaDocumento
{
    public bool $showImport = false;

    /** Candidatos legacy cuyo contexto coincide con la carga vigente. */
    public Collection $importCandidatos;

    /** Ids legacy marcados con los checkboxes. */
    public array $importSeleccionados = [];

    /** Reporte de la última importación (creados + omitidos). */
    public ?array $importReporte = null;

    /** Modo de vista del asistente: `grid` (tarjetas) o `table` (tabla). */
    public string $importViewMode = 'grid';

    /** Búsqueda libre sobre grado, sección, docente origen y diagnóstico. */
    public string $importSearch = '';

    /** Grado para filtrar los candidatos (id como string, '' = todos). */
    public $importGrado = '';

    /** Docente origen para filtrar (id como string, '' = todos). */
    public $importProfesor = '';

    /** Página actual de los candidatos (paginación manual en memoria). */
    public int $importPage = 1;

    /** Candidatos por página. */
    public int $importPerPage = 6;

    /** Campo de ordenamiento: `finicial` o `grado`. */
    public string $importSort = 'finicial';

    /** Dirección del ordenamiento: `asc` o `desc`. */
    public string $importSortDir = 'desc';

    /** Id legacy con el detalle expandido dentro de su tarjeta. */
    public ?int $importDetalleId = null;

    /**
     * Instancia del asistente de importación del documento.
     *
     * Cada subclase devuelve su propio importador (semanal, quincenal…).
     */
    abstract protected function importador(): ImportadorDocumento;

    /** Abre el asistente con los candidatos que encajan en la carga vigente. */
    public function openImport(): void
    {
        $this->resetValidation();
        $this->importSeleccionados = [];
        $this->importReporte = null;
        $this->importSearch = '';
        $this->importGrado = '';
        $this->importProfesor = '';
        $this->importPage = 1;
        $this->importViewMode = 'grid';

        try {
            $importador = $this->importador();

            if (! $importador->origenDisponible()) {
                $this->notification()->error(
                    title: 'Origen no disponible',
                    description: 'No se puede conectar con s2526 en este momento.'
                );

                return;
            }

            $this->importCandidatos = $importador->candidatos($this->profesor_id);
            $this->showImport = true;
        } catch (\Throwable $e) {
            $this->notification()->error(
                title: 'Error al cargar los planes',
                description: $e->getMessage()
            );
        }
    }

    public function closeImport(): void
    {
        $this->showImport = false;
        $this->importSeleccionados = [];
        $this->importReporte = null;
        $this->importDetalleId = null;
        $this->resetValidation();
    }

    /** Importa los planes marcados. */
    public function importarSeleccionados(): void
    {
        $this->resetValidation();

        if ($this->importSeleccionados === []) {
            $this->addError('importSeleccionados', 'Seleccione al menos un plan para importar.');

            return;
        }

        try {
            $reporte = $this->importador()->importar($this->importSeleccionados, $this->profesor_id);

            $this->importReporte = $reporte;
            $this->importSeleccionados = [];

            $creados = count($reporte['creados']);

            if ($creados > 0) {
                $this->notification()->success(
                    title: $creados === 1 ? 'Plan importado' : $creados.' planes importados',
                    description: 'Los planes quedaron anclados a su carga vigente del lapso en curso.'
                );
            } else {
                $this->notification()->warning(
                    title: 'Nada que importar',
                    description: 'Los planes marcados no encajan en su carga vigente.'
                );
            }
        } catch (\Throwable $e) {
            $this->notification()->error(
                title: 'Error al importar',
                description: $e->getMessage()
            );
        }
    }

    /** Alterna tarjetas ↔ tabla del asistente. */
    public function toggleImportView(): void
    {
        $this->importViewMode = $this->importViewMode === 'grid' ? 'table' : 'grid';
    }

    public function updatedImportSearch(): void
    {
        $this->importPage = 1;
    }

    public function updatedImportGrado(): void
    {
        $this->importPage = 1;
    }

    public function updatedImportProfesor(): void
    {
        $this->importPage = 1;
    }

    public function importPaginaAnterior(): void
    {
        $this->importPage = max(1, $this->importPage - 1);
    }

    public function importPaginaSiguiente(): void
    {
        $this->importPage = min($this->importTotalPaginas(), $this->importPage + 1);
    }

    public function gotoImportPage(int $pagina): void
    {
        $this->importPage = min(max(1, $pagina), $this->importTotalPaginas());
    }

    public function sortImport(string $campo): void
    {
        if (! in_array($campo, ['finicial', 'grado'], true)) {
            return;
        }

        if ($this->importSort === $campo) {
            $this->importSortDir = $this->importSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->importSort = $campo;
            $this->importSortDir = $campo === 'finicial' ? 'desc' : 'asc';
        }

        $this->importPage = 1;
    }

    /** Importa un solo plan (acceso directo desde el menú de su tarjeta). */
    public function importarPlan(int $id): void
    {
        $this->importSeleccionados = [$id];
        $this->importarSeleccionados();
    }

    /** Expande/colapsa el detalle de una tarjeta, saltando a su página. */
    public function verImportDetalle(int $id): void
    {
        if ($this->importDetalleId === $id) {
            $this->importDetalleId = null;

            return;
        }

        $indice = $this->importFiltrados()->pluck('id')->search($id);

        if ($indice !== false) {
            $this->importPage = (int) floor($indice / $this->importPerPage) + 1;
        }

        $this->importDetalleId = $id;
    }

    /** Contexto del subtítulo: lapso en curso + conteo de candidatos. */
    public function importSubtitulo(): string
    {
        $lapso = \App\Models\app\Academy\Lapso::current()?->name ?? '—';

        return $this->importCandidatos->count().' planes · '.$lapso;
    }

    /** Grados presentes en los candidatos, para el filtro. */
    public function importGradosOpciones(): array
    {
        $opciones = [];

        foreach ($this->importCandidatos as $candidato) {
            $opciones[$candidato['grado']] = $candidato['grado'];
        }

        asort($opciones);

        return $opciones;
    }

    /** Docentes origen presentes en los candidatos (id => nombre). */
    public function importProfesoresOpciones(): array
    {
        $opciones = [];

        foreach ($this->importCandidatos as $candidato) {
            if ($candidato['profesor_origen_id'] !== null) {
                $opciones[$candidato['profesor_origen_id']] = $candidato['profesor_origen'];
            }
        }

        asort($opciones);

        return $opciones;
    }

    /**
     * Candidatos tras aplicar búsqueda, filtro de grado, filtro por docente y
     * ordenamiento.
     *
     * @return Collection<int, array>
     */
    public function importFiltrados(): Collection
    {
        $termino = mb_strtolower(trim($this->importSearch));

        $filtrados = $this->importCandidatos
            ->when($this->importGrado !== '', fn ($c) => $c->filter(fn ($cand) => $cand['grado'] === $this->importGrado))
            ->when($this->importProfesor !== '', fn ($c) => $c->filter(fn ($cand) => (string) ($cand['profesor_origen_id'] ?? '') === (string) $this->importProfesor))
            ->when($termino !== '', fn ($c) => $c->filter(fn ($cand) => str_contains(
                mb_strtolower(implode(' ', [
                    $cand['grado'], $cand['seccion'], $cand['profesor_origen'],
                    implode(' ', $cand['asignaturas']),
                    $cand['diagnostico'] ?? '', $cand['finicial'] ?? '', $cand['ffinal'] ?? '',
                ])),
                $termino
            )));

        $ordenados = $this->importSortDir === 'asc'
            ? $filtrados->sortBy($this->importSort)
            : $filtrados->sortByDesc($this->importSort);

        return $ordenados->values();
    }

    /**
     * Páginas para la paginación numerada (con elipsis).
     *
     * @return array<int, int|string>
     */
    public function importPaginas(): array
    {
        $total = $this->importTotalPaginas();
        $actual = $this->importPage;

        if ($total <= 7) {
            return range(1, $total);
        }

        $paginas = [1];

        if ($actual > 3) {
            $paginas[] = '…';
        }

        foreach (range(max(2, $actual - 1), min($total - 1, $actual + 1)) as $p) {
            $paginas[] = $p;
        }

        if ($actual < $total - 2) {
            $paginas[] = '…';
        }

        $paginas[] = $total;

        return $paginas;
    }

    /** Número de páginas de los candidatos filtrados (mínimo 1). */
    public function importTotalPaginas(): int
    {
        return max(1, (int) ceil($this->importFiltrados()->count() / $this->importPerPage));
    }

    /**
     * Candidatos de la página actual. La página se sujeta al rango válido.
     *
     * @return Collection<int, array>
     */
    public function importPagina(): Collection
    {
        $this->importPage = min(max(1, $this->importPage), $this->importTotalPaginas());

        return $this->importFiltrados()->slice(($this->importPage - 1) * $this->importPerPage, $this->importPerPage)->values();
    }
}
