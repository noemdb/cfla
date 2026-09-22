<?php

namespace App\Livewire\Leadership;

use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Educational\DebateQuestion;
use App\Services\Leadership\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class DebateQuestionReview extends Component
{
    use WithPagination, WireUiActions;

    public string $search = '';
    public string $filterUnderReview = ''; // '', '1', '0'
    public string $filterActive = ''; // '', '1', '0'
    public string $filterAreaId = '';
    public string $filterCategory = '';

    public ?int $editingId = null;
    public string $observation = '';

    public bool $showObservationModal = false;

    public int $paginate = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAreaId' => ['except' => ''],
        'filterActive' => ['except' => ''],
        'filterUnderReview' => ['except' => ''],
        'filterCategory' => ['except' => ''],
    ];

    public function updatingPaginate(): void { $this->resetPage(); }
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterAreaId(): void { $this->resetPage(); }
    public function updatingFilterActive(): void { $this->resetPage(); }
    public function updatingFilterUnderReview(): void { $this->resetPage(); }
    public function updatingFilterCategory(): void { $this->resetPage(); }

    public function toggleActive(int $id): void
    {
        $q = $this->scopedQuery()->findOrFail($id);
        $this->assertCanReview($q);

        $q->status_active = ! (bool) $q->status_active;
        $q->save();

        $this->notification()->success(
            $q->status_active ? 'Pregunta activada' : 'Pregunta desactivada',
            $q->status_active ? 'La pregunta quedó activa.' : 'La pregunta quedó inactiva.'
        );
    }

    public function toggleUnderReview(int $id): void
    {
        $q = $this->scopedQuery()->findOrFail($id);
        $this->assertCanReview($q);

        $q->status_under_review = ! (bool) $q->status_under_review;
        // si sale de revisión, opcionalmente dejar observación vacía? no tocar
        $q->save();

        $this->notification()->success(
            $q->status_under_review ? 'Marcada en revisión' : 'Revisión retirada',
            $q->status_under_review ? 'La pregunta está en revisión.' : 'La pregunta ya no está en revisión.'
        );
    }

    public function openObservation(int $id): void
    {
        $q = $this->scopedQuery()->findOrFail($id);
        $this->assertCanReview($q);
        $this->editingId = $id;
        $this->observation = (string) ($q->observation ?? '');
        $this->showObservationModal = true;
    }

    public function closeObservation(): void
    {
        $this->showObservationModal = false;
        $this->editingId = null;
        $this->observation = '';
    }

    public function saveObservation(): void
    {
        $this->validate(['observation' => 'nullable|string|max:2000']);
        if ($this->editingId === null) return;

        $q = $this->scopedQuery()->findOrFail($this->editingId);
        $this->assertCanReview($q);

        $q->observation = $this->observation ?: null;
        // al guardar observación, si estaba en revisión se mantiene; el jefe decide quitar marca manualmente
        $q->save();

        $this->notification()->success('Observación guardada', 'La observación se actualizó correctamente.');
        $this->closeObservation();
    }

    /** Scope base: solo preguntas de sus áreas (via pensum_id). */
    private function scopedQuery()
    {
        $service = new LeadershipService(Auth::user());
        $query = DebateQuestion::query()->with(['pensum.asignatura', 'pensum.grado.pestudio', 'debate.competition', 'user']);
        return $service->scopeDebateQuestions($query);
    }

    private function assertCanReview(DebateQuestion $q): void
    {
        // liderazgo: verifica pensum pertenezca a sus áreas
        if (Auth::user()->is_admin) return;
        $service = new LeadershipService(Auth::user());
        if ($q->pensum_id) {
            $service->assertCanAccessPensum((int) $q->pensum_id);
        } else {
            // sin pensum, no hay área asociada -> bloquear a no-admin
            abort(403, 'Pregunta sin pensum asociado, no puede ser revisada por jefatura.');
        }
    }

    #[Layout('leadership.layouts.app')]
    public function render()
    {
        $user = Auth::user();
        $service = new LeadershipService($user);

        // Áreas del líder para filtro
        $areas = $service->isUnrestricted() // admin ve todas, pero usamos bypass
            ? AreaConocimiento::withCount('campo_conocimientos')->orderBy('name')->get()
            : AreaConocimiento::where('leader_id', $user->id)->withCount('campo_conocimientos')->orderBy('name')->get();

        // Si filtra por área, limitar pensumIds a los de esa área
        $query = $this->scopedQuery();

        if ($this->filterAreaId !== '') {
            $area = AreaConocimiento::find((int) $this->filterAreaId);
            if ($area) {
                $pensumIds = \App\Models\app\Academy\CampoConocimiento::where('area_conocimiento_id', $area->id)
                    ->whereNotNull('pensum_id')->pluck('pensum_id')->unique();
                $query->whereIn('pensum_id', $pensumIds);
            }
        }

        if ($this->search !== '') {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('text', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%")
                  ->orWhere('observation', 'like', "%{$s}%");
            });
        }

        if ($this->filterActive !== '') {
            $query->where('status_active', (bool) $this->filterActive);
        }
        if ($this->filterUnderReview !== '') {
            $query->where('status_under_review', (bool) $this->filterUnderReview);
        }
        if ($this->filterCategory !== '') {
            $query->where('category', $this->filterCategory);
        }

        $questions = $query->orderByDesc('status_under_review')
            ->orderByDesc('created_at')
            ->paginate($this->paginate);

        // Métricas rápidas para KPI cards
        $baseScoped = $this->scopedQuery();
        if ($this->filterAreaId !== '') {
            $area = AreaConocimiento::find((int) $this->filterAreaId);
            if ($area) {
                $pensumIds = \App\Models\app\Academy\CampoConocimiento::where('area_conocimiento_id', $area->id)->whereNotNull('pensum_id')->pluck('pensum_id');
                $baseScoped->whereIn('pensum_id', $pensumIds);
            }
        }
        $metrics = [
            'total' => (clone $baseScoped)->count(),
            'en_revision' => (clone $baseScoped)->where('status_under_review', true)->count(),
            'activas' => (clone $baseScoped)->where('status_active', true)->count(),
            'inactivas' => (clone $baseScoped)->where('status_active', false)->count(),
        ];

        // Categorías disponibles en su scope para filtro
        $categories = (clone $this->scopedQuery())->distinct()->pluck('category')->filter()->sort()->values();

        return view('livewire.leadership.debate-question-review', [
            'questions' => $questions,
            'areas' => $areas,
            'metrics' => $metrics,
            'categories' => $categories,
        ]);
    }
}
