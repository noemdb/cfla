<?php
// app/Livewire/Director/ActivityList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Seccion;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';

    // Filtros de contexto (snake_case, patrón del módulo Planning)
    public $pestudio_id = '';
    public $grado_id = '';
    public $seccion_id = '';
    public $profesor_id = '';
    public $lapso_id = '';

    public $paginate = 15;
    protected $paginationTheme = 'tailwind';

    // Listas para los selects del panel de filtros
    public $list_pestudio;
    public $list_grado;
    public $list_seccion;
    public $list_profesor;

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
        $service = $this->getDirectorService();

        $this->list_pestudio = $service->queryPestudios()
            ->orderBy('order')
            ->pluck('name', 'id');
        $this->list_grado = Grado::active('true')->orderBy('order')->pluck('name', 'id');
        $this->list_seccion = collect();
        $this->list_profesor = $service->queryProfesores()
            ->orderBy('lastname')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($p) => [$p->id => "{$p->lastname}, {$p->name}"]);
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = $service->queryActivities()->with([
            'pevaluacion' => fn($q) => $q->with([
                'profesor:id,name,lastname',
                'seccion.grado',
                'pensum.asignatura',
                'pensum.pestudio.peducativo',
                'lapso',
            ]),
            'lmsPublication',
        ]);

        if ($this->pestudio_id) {
            $query->whereHas('pevaluacion.pensum', fn($q) => $q->where('pestudio_id', $this->pestudio_id));
        }
        if ($this->grado_id) {
            $query->whereHas('pevaluacion.seccion', fn($q) => $q->where('grado_id', $this->grado_id));
        }
        if ($this->seccion_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('seccion_id', $this->seccion_id));
        }
        if ($this->profesor_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('profesor_id', $this->profesor_id));
        }
        if ($this->lapso_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapso_id));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%");
            });
        }

        $activities = $query->orderBy('activities.created_at', 'desc')->paginate($this->paginate);
        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.director.activity-list', [
            'activities' => $activities,
            'lapsos'     => $lapsos,
        ])->layout('director.layouts.app');
    }

    // ─── FILTERS CASCADE (patrón del módulo Planning) ──────────

    public function updatedPestudioId($value)
    {
        $this->resetPage();
        $this->list_grado = $value
            ? Grado::where('pestudio_id', $value)->where('status_active', 'true')->orderBy('order')->pluck('name', 'id')
            : Grado::active('true')->orderBy('order')->pluck('name', 'id');
        $this->grado_id = null;
        $this->seccion_id = null;
        $this->list_seccion = collect();
    }

    public function updatedGradoId($value)
    {
        $this->resetPage();
        $this->list_seccion = $value
            ? Seccion::list_seccion_grado($value)
            : collect();
        $this->seccion_id = null;
    }

    public function updatedSeccionId($value)     { $this->resetPage(); }
    public function updatedProfesorId($value)    { $this->resetPage(); }
    public function updatedLapsoId($value)       { $this->resetPage(); }

    public function updatingSearch()  { $this->resetPage(); }
    public function updatingPaginate(){ $this->resetPage(); }
}
