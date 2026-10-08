<?php

namespace App\Livewire\Evaluacion\Inicial;

use App\Models\app\Inicial\Eifinalk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Lectura del INFORME FINAL (`eifinalks`) para la Coordinación.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * SOLO LECTURA
 * ─────────────────────────────────────────────────────────────────────────────
 * Hereda la escritura de la base, pero el catálogo declara `revision = null` para
 * los informes finales: `openRevision()` aborta con 404 y `saveRevision()`
 * también, así que no hay forma de escribir un campo inexistente. Ninguna
 * acción de escritura llega a renderizarse.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * `groupBy` SIN AGREGADO — CORREGIDO
 * ─────────────────────────────────────────────────────────────────────────────
 * El legacy hacía:
 *
 *     Eifinalk::…->orderBy('estudiant_id')->orderBy('pevaluacion_id')
 *              ->groupBy('estudiant_id')->get()
 *
 * Eso devuelve UNA fila arbitraria por estudiante (la que el servidor decida) y
 * solo funciona porque el servidor no tiene `ONLY_FULL_GROUP_BY` activo. Con él
 * activo es un error 1055; y aunque no lo tuviera, el resultado ya era
 * INCORRECTO: de los N informes de un estudiante solo se veía UNO y el resto
 * desaparecía de la revisión.
 *
 * Aquí se agrupa la COLECCIÓN ya traída —`get()->groupBy(...)` en PHP—, que no
 * depende de la configuración del servidor y no pierde filas. Para cada
 * estudiante se toma, de forma explícita y visible, el informe de MENOR `order`,
 * que es el primero que imprime el boletín, en lugar del que el servidor elija.
 */
class EifinalkComponent extends EvaluacionDocumentComponent
{
    protected string $entidad = 'eifinalks';

    public ?int $lapsoId = null;

    public function mount(
        ?int $profesorId = null,
        ?int $gradoId = null,
        ?int $seccionId = null,
        ?int $lapsoId = null
    ): void {
        parent::mount($profesorId, $gradoId, $seccionId);

        $this->lapsoId = $lapsoId;
    }

    protected function filtraPorPevaluacion(): bool
    {
        return true;
    }

    protected function aplicarFiltrosPorPevaluacion(Builder $query): void
    {
        parent::aplicarFiltrosPorPevaluacion($query);

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn ($p) => $p->where('lapso_id', $this->lapsoId));
        }
    }

    /**
     * Un renglón por estudiante, con su primer informe.
     *
     * @return Collection<int, Eifinalk>
     */
    protected function items(): Collection
    {
        $query = $this->consultaBase()->orderBy('estudiant_id')->orderBy('order')->orderBy('id');

        $this->aplicarFiltros($query);

        return $query->get()
            ->groupBy('estudiant_id')
            ->map(function ($informes) {
                $representante = $informes->first();

                // El total del estudiante va como atributo del renglón: la
                // columna del catálogo lo lee y así no hace falta una consulta
                // `count()` por fila (el propio N+1 que el legacy tenía aquí).
                $representante->informes_total = $informes->count();

                return $representante;
            });
    }
}
