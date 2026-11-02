<?php

namespace App\Livewire\Inicial;

use App\Services\Inicial\RegistroInicial;
use App\Services\Inicial\SeguimientoInicial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * Listado de seguimiento de Educación Inicial.
 *
 * Blueprint: blueprint/inicial · doc 11 · §8.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * CONSULTA + REVISIÓN, NADA MÁS
 * ─────────────────────────────────────────────────────────────────────────────
 * No crea, actualiza ni elimina documentos. La ÚNICA escritura permitida es
 * `guardarRevision()`: el manager registra `observacion` (o `recomendacion` en
 * el plan de evaluación) desde el modal «Ver», con la misma regla `min:5` que
 * la Coordinación de Evaluación. El resto del documento sigue siendo de solo
 * lectura, y las rutas del grupo solo declaran GET (la escritura viaja por el
 * endpoint de Livewire).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ LA ENTIDAD ES `Locked`
 * ─────────────────────────────────────────────────────────────────────────────
 * La fija la ruta (`/seguimiento/{entidad}`), no el usuario. Si se pudiera
 * cambiar desde el cliente, un snapshot manipulado podría pedir la entidad de
 * otro catálogo; `Locked` lo impide antes de tocar la base de datos.
 */
class SeguimientoInicialComponent extends Component
{
    use WithPagination, WireUiActions;

    /** Documento activo; lo fija la ruta y no se puede cambiar desde el cliente. */
    #[Locked]
    public string $entidad;

    #[Url(as: 'docente', except: '')]
    public $profesor_id = '';

    #[Url(as: 'nivel', except: '')]
    public $grado_id = '';

    #[Url(as: 'seccion', except: '')]
    public $seccion_id = '';

    #[Url(as: 'lapso', except: '')]
    public $lapso_id = '';

    #[Url(as: 'desde', except: '')]
    public $desde = '';

    #[Url(as: 'hasta', except: '')]
    public $hasta = '';

    #[Url(as: 'estado', except: '')]
    public $estado = '';

    #[Url(as: 'buscar', except: '')]
    public string $search = '';

    #[Url(as: 'vista', except: 'grid')]
    public string $viewMode = 'grid';

    /** Tamaño de página. No se expone en la URL: la vista no lo ofrece en fase 1. */
    public int $paginate = 15;

    /** Documento abierto en el modal de detalle. */
    #[Locked]
    public ?int $detalleId = null;

    /** El modal muestra el formulario de revisión en lugar del detalle. */
    public bool $revisionAbierta = false;

    /** Texto del campo revisable (`observacion` o `recomendacion`). */
    public string $revision = '';

    /** Catálogos del panel de filtros. */
    public Collection $listProfesores;

    public Collection $listGrados;

    public Collection $listSecciones;

    public Collection $listLapsos;

    public function mount(string $entidad): void
    {
        // Defensa en profundidad: la ruta ya exige el middleware, pero el
        // componente no debe ser usable si alguien lo invoca de otra forma.
        abort_unless(auth()->user()?->isInicialManager(), 403);
        abort_unless(RegistroInicial::existe($entidad), 404);

        $this->entidad = $entidad;
        $this->cargarCatalogos();
    }

    public function render()
    {
        $servicio = app(SeguimientoInicial::class);
        $filtros = $this->filtros();

        $documentos = $servicio->paginar($this->entidad, $filtros, $this->paginate);

        return view('livewire.inicial.seguimiento.index', [
            'documentos' => $documentos,
            'resumen' => $servicio->resumen($this->entidad, $filtros),
            'opcionesEstado' => $servicio->opcionesEstado($this->entidad),
            'soportaFechas' => $servicio->soportaFechas($this->entidad),
            'soportaLapso' => $servicio->soportaLapso($this->entidad),
            // `consulta()->first()` y no `buscar()`: dentro de Livewire un 404
            // por un registro borrado mientras el modal estaba abierto rompe
            // la respuesta; aquí el modal simplemente se queda sin cuerpo.
            'detalle' => $this->detalleId
                ? RegistroInicial::consulta($this->entidad)->whereKey($this->detalleId)->first()
                : null,
            'detalleParcial' => RegistroInicial::parcialDetalle($this->entidad),
            'pestanas' => $servicio->pestanas(),
        ]);
    }

    /** Catálogos del panel de filtros. Una sola vez por request. */
    private function cargarCatalogos(): void
    {
        $servicio = app(SeguimientoInicial::class);

        $this->listProfesores = $servicio->profesores();
        $this->listGrados = $servicio->grados();
        $this->listLapsos = $servicio->lapsos();
        $this->listSecciones = $servicio->secciones(
            $this->grado_id === '' ? null : (int) $this->grado_id
        );
    }

    /**
     * Filtros no vacíos que consumen el servicio y la query string.
     *
     * @return array<string, mixed>
     */
    private function filtros(): array
    {
        return array_filter([
            'profesor_id' => $this->profesor_id,
            'grado_id' => $this->grado_id,
            'seccion_id' => $this->seccion_id,
            'lapso_id' => $this->lapso_id,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'estado' => $this->estado,
            'search' => $this->search,
        ], fn ($valor) => $valor !== null && $valor !== '');
    }

    public function updatedGradoId(): void
    {
        // Al cambiar (o limpiar) el nivel, la sección deja de ser válida: se
        // limpia y sus opciones se recalculan para el nivel nuevo. Sin nivel,
        // el selector de sección queda vacío y deshabilitado en la vista.
        $this->seccion_id = '';
        $this->listSecciones = app(SeguimientoInicial::class)
            ->secciones($this->grado_id === '' ? null : (int) $this->grado_id);

        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset([
            'profesor_id', 'grado_id', 'seccion_id', 'lapso_id',
            'desde', 'hasta', 'estado', 'search',
        ]);

        $this->listSecciones = collect();
        $this->resetPage();
    }

    public function updated(string $name): void
    {
        if (in_array($name, [
            'profesor_id', 'grado_id', 'seccion_id', 'lapso_id',
            'desde', 'hasta', 'estado', 'search',
        ], true)) {
            $this->resetPage();
        }
    }

    public function abrirDetalle(int $id): void
    {
        $existe = RegistroInicial::modelo($this->entidad)::query()->whereKey($id)->exists();

        if (! $existe) {
            $this->cerrarDetalle();

            return;
        }

        $this->detalleId = $id;
        $this->limpiarRevision();
    }

    public function cerrarDetalle(): void
    {
        $this->detalleId = null;
        $this->limpiarRevision();
    }

    /**
     * Abre el modal ya en modo revisión desde el botón de la tarjeta o la tabla.
     *
     * La escritura sigue ocurriendo dentro del modal: este método solo evita el
     * paso intermedio de abrir el detalle y pulsar «Registrar revisión».
     */
    public function revisarDetalle(int $id): void
    {
        $this->abrirDetalle($id);

        if ($this->detalleId) {
            $this->abrirRevision();
        }
    }

    /**
     * Abre el formulario de revisión con el texto actual.
     *
     * El informe final no tiene campo revisable: 404 y no un formulario vacío
     * que al guardar no escribiría nada.
     */
    public function abrirRevision(): void
    {
        $documento = $this->documentoDetalle();
        $campo = RegistroInicial::campoRevision($this->entidad);

        abort_if(! $campo || ! $documento, 404);

        $this->revision = (string) $documento->{$campo};
        $this->revisionAbierta = true;

        $this->resetValidation('revision');
    }

    public function cerrarRevision(): void
    {
        // Cancelar el formulario debe cerrar el modal completo. En particular,
        // `revisarDetalle()` abre directamente el formulario; volver al detalle
        // aquí mostraría de golpe todo el contenido del documento.
        $this->cerrarDetalle();

        $this->resetValidation('revision');
    }

    /**
     * Guarda la revisión del manager en el campo del documento.
     *
     * Misma regla que la Coordinación (`required|min:5`). No toca ningún otro
     * campo ni crea registros: solo `observacion`/`recomendacion`.
     */
    public function guardarRevision(): void
    {
        // Defensa en profundidad: el componente solo se monta para managers,
        // pero una actualización de Livewire no vuelve a ejecutar `mount()`.
        abort_unless(auth()->user()?->isInicialManager(), 403);

        $campo = RegistroInicial::campoRevision($this->entidad);
        abort_if(! $campo, 404);

        $rotulo = RegistroInicial::rotuloRevision($this->entidad);
        $minuscula = mb_strtolower($rotulo);

        $this->validate([
            'revision' => ['required', 'string', 'min:5'],
        ], [
            'revision.required' => "Escribe la {$minuscula} de la Coordinación.",
            'revision.min' => "La {$minuscula} debe tener al menos 5 caracteres.",
        ]);

        $documento = $this->documentoDetalle();
        abort_if(! $documento, 404);

        $documento->{$campo} = trim($this->revision);
        $documento->save();

        $this->limpiarRevision();
        $this->detalleId = null;
        $this->resetValidation('revision');
        $this->notification()->success(
            "{$rotulo} guardada",
            "La {$minuscula} se actualizó correctamente."
        );
    }

    /** Documento del modal, sin relaciones: solo se usa para leer/escribir el campo. */
    private function documentoDetalle(): ?Model
    {
        if (! $this->detalleId) {
            return null;
        }

        return RegistroInicial::modelo($this->entidad)::query()
            ->whereKey($this->detalleId)
            ->first();
    }

    private function limpiarRevision(): void
    {
        $this->revisionAbierta = false;
        $this->revision = '';
    }

    public function alternarVista(): void
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'table' : 'grid';
    }

    /**
     * Filtros activos con los nombres públicos de la URL.
     *
     * Las pestañas viven DENTRO del componente para no conservar filtros
     * viejos cuando Livewire actualiza la query string.
     *
     * @return array<string, mixed>
     */
    public function queryFiltros(): array
    {
        return array_filter([
            'docente' => $this->profesor_id,
            'nivel' => $this->grado_id,
            'seccion' => $this->seccion_id,
            'lapso' => $this->lapso_id,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'estado' => $this->estado,
            'buscar' => $this->search,
            'vista' => $this->viewMode === 'grid' ? null : $this->viewMode,
        ], fn ($valor) => $valor !== null && $valor !== '');
    }
}
