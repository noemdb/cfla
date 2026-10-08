<?php

namespace App\Livewire\Evaluacion\Inicial;

use Illuminate\Database\Eloquent\Builder;

/**
 * Revisión del PLAN DE EVALUACIÓN (`eievaluationks`).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EL ÚNICO QUE SE REVISA CON `recomendacion`
 * ─────────────────────────────────────────────────────────────────────────────
 * Los cuatro planes de grupo se revisan con `observacion`; este con
 * `recomendacion`, que es la palabra que usa el Outcome-Based Education: un
 * plan de evaluación termina en recomendaciones, no en observaciones.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EL ÚNICO QUE SE FILTRA POR LAPSO
 * ─────────────────────────────────────────────────────────────────────────────
 * Se evalúa un periodo, no un grupo: su filtro dimensional es el lapso. Por eso
 * esta clase es la única que declara un cuarto filtro. Lo hereda del formulario
 * GET del índice de la perspectiva.
 */
class EievaluationkComponent extends EvaluacionDocumentComponent
{
    protected string $entidad = 'eievaluationks';

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

    protected function aplicarFiltros(Builder $query): void
    {
        parent::aplicarFiltros($query);

        if ($this->lapsoId) {
            $query->where('lapso_id', $this->lapsoId);
        }
    }
}
