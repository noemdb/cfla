<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\Pensum;
use Livewire\Attributes\On;
use Livewire\Component;

class CompetenciasDialog extends Component
{
    /** @var bool Controla la visibilidad del modal */
    public $showModal = false;

    /** @var int|null ID del pensum seleccionado */
    public $pensum_id = null;

    /** @var string Nombre de la asignatura */
    public $asignaturaName = '';

    /** @var string Nombre del plan de estudio */
    public $pestudioName = '';

    /** @var array Competencias con indicadores y referentes */
    public $competencias = [];

    /** @var string Filtro de búsqueda por nombre/descripción */
    public $search = '';

    /** @var string Filtro por referente */
    public $filterReferentId = '';

    /** @var bool Mostrar solo competencias con indicadores */
    public $onlyWithIndicators = false;

    // ─── MÉTODOS PRINCIPALES ───────────────────────────────────

    #[On('openCompetenciasDialog')]
    public function open($pensumId)
    {
        $this->pensum_id = $pensumId;
        $this->loadCompetencias();
        $this->showModal = true;
    }

    public function close()
    {
        $this->showModal = false;
        $this->pensum_id = null;
        $this->competencias = [];
        $this->asignaturaName = '';
        $this->pestudioName = '';
    }

    // ─── CARGAR DATOS ──────────────────────────────────────────

    protected function loadCompetencias()
    {
        $pensum = Pensum::with([
            'asignatura',
            'pestudio',
            'diagCompetencies' => fn($q) => $q->orderBy('name'),
            'diagCompetencies.indicators',
            'diagCompetencies.referent',
        ])->find($this->pensum_id);

        if (!$pensum) {
            $this->asignaturaName = '—';
            $this->pestudioName = '—';
            $this->competencias = [];
            return;
        }

        $this->asignaturaName = $pensum->asignatura?->name ?? '—';
        $this->pestudioName = $pensum->pestudio?->name ?? '—';
        $this->competencias = $pensum->diagCompetencies->toArray();
    }

    // ─── RENDER ────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.profesor.activity.competencias-dialog');
    }
}
