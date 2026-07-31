<?php

namespace App\Livewire\Leadership;

use App\Livewire\Planning\Activities\IndexComponent;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Services\Leadership\LeadershipService;
use Illuminate\Support\Facades\Auth;

class ActivityOverview extends IndexComponent
{
    public $showLessonPreview = false;
    public $previewLessonActivity;

    // ─── Publicar lección programada (SCHEDULED → PUBLISHED) ───
    public bool $showApproveModal = false;
    public ?int $approveActivityId = null;
    public string $approveActivityTitle = '';
    public ?string $approvePublishAt = null;   // fecha publicada; vacío → ahora

    public function confirmApproveLesson(int $activityId): void
    {
        $activity = Activity::find($activityId);

        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        $asignaturaId = $activity?->pevaluacion?->pensum?->asignatura_id;
        if ($asignaturaId) {
            $service->assertCanAccessAsignatura($asignaturaId);
        }

        $this->approveActivityId = $activityId;
        $this->approveActivityTitle = $activity?->topic ?? 'Lección';

        // Pre-cargar la fecha programada (publish_at) si existe; sino, ahora.
        // Formato Y-m-d\TH:i para el input datetime-local.
        $publishAt = $activity?->lmsPublication?->publish_at;
        $this->approvePublishAt = $publishAt
            ? \Carbon\Carbon::parse($publishAt)->format('Y-m-d\TH:i')
            : now()->format('Y-m-d\TH:i');

        $this->showApproveModal = true;
    }

    public function doApproveLesson(): void
    {
        if (!$this->approveActivityId) {
            return;
        }

        $this->validate(['approvePublishAt' => 'nullable|date']);

        $service = app(LeadershipService::class, ['user' => Auth::user()]);

        $activity = Activity::with('lmsPublication')->findOrFail($this->approveActivityId);

        $asignaturaId = $activity->pevaluacion?->pensum?->asignatura_id;
        if ($asignaturaId) {
            $service->assertCanAccessAsignatura($asignaturaId);
        }

        if (!$activity->lmsPublication || $activity->lmsPublication->status !== 'SCHEDULED') {
            $this->dispatch('notify', message: 'Esta lección ya no está programada.', type: 'warning');
            $this->cancelApproveLesson();
            return;
        }

        if (!$activity->status) {
            $this->dispatch('notify', message: 'La actividad debe estar aprobada para poder publicarla.', type: 'warning');
            $this->cancelApproveLesson();
            return;
        }

        $publishAt = $this->approvePublishAt
            ? \Carbon\Carbon::parse($this->approvePublishAt)
            : now();

        $activity->lmsPublication->update([
            'status'       => 'PUBLISHED',
            'publish_at'   => $publishAt,                 // nunca nulo (default now())
            'published_at' => $publishAt->gt(now()) ? null : now(),
            'published_by' => Auth::id(),
        ]);

        $this->dispatch('notify', message: 'Lección publicada correctamente.', type: 'success');
        $this->cancelApproveLesson();
    }

    public function cancelApproveLesson(): void
    {
        $this->showApproveModal = false;
        $this->approveActivityId = null;
        $this->approveActivityTitle = '';
        $this->approvePublishAt = null;
    }

    protected function getPevaluaciones(array $filters)
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);

        $query = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
        ])
        ->with('activities.lmsPublication')
        ->with('activities.lmsSections')
        ->withCount([
            'activities',
            'activities as activities_revision_count' => fn($q) => $q->where('status', 0),
            'activities as activities_approved_count' => fn($q) => $q->where('status', 1),
            'activities as activities_lessons_count' => fn($q) => $q->whereHas('lmsPublication'),
        ])
        ->whereHas('pensum.pestudio', fn($q) => $q->where('planning_module', true))
        ->whereNull('pevaluacions.deleted_at');

        // Apply leadership scope before filters
        $query = $service->scopePevaluacions($query);

        if (isset($filters['pestudio_id'])) {
            $query->whereHas('pensum.pestudio', fn($q) => $q->where('id', $filters['pestudio_id']));
        }
        if (isset($filters['grado_id'])) {
            $query->whereHas('seccion.grado', fn($q) => $q->where('id', $filters['grado_id']));
        }
        if (isset($filters['seccion_id'])) {
            $query->where('seccion_id', $filters['seccion_id']);
        }
        if (isset($filters['lapso_id'])) {
            $query->where('lapso_id', $filters['lapso_id']);
        }
        if (isset($filters['profesor_id'])) {
            $query->where('profesor_id', $filters['profesor_id']);
        }
        if (isset($filters['status_activities'])) {
            if ($filters['status_activities'] === 'SI') {
                $query->having('activities_count', '>', 0);
            } elseif ($filters['status_activities'] === 'NO') {
                $query->having('activities_count', '=', 0);
            }
        }
        if (!empty($filters['filter_observations'])) {
            $query->whereNotNull('pevaluacions.observations')
                  ->where('pevaluacions.observations', '!=', '');
        }
        if (!empty($filters['filter_revision'])) {
            $query->whereHas('activities', fn($q) => $q->where('status', 0));
        }

        if (!empty($filters['filter_status'])) {
            if ($filters['filter_status'] === 'pending') {
                $query->whereHas('activities', fn($q) => $q->where('status', 0));
            } elseif ($filters['filter_status'] === 'approved') {
                $query->has('activities')
                      ->whereDoesntHave('activities', fn($q) => $q->where('status', 0));
            }
        }

        $query->orderBy('created_at', 'desc');

        if ((int) $this->paginate === 9999) {
            $all = $query->get();
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $all,
                $all->count(),
                max($all->count(), 1),
                1,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        }

        return $query->paginate($this->paginate);
    }

    public function saveComent(...$args)
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        $service->assertCanAccessAsignatura(
            $this->activity->pevaluacion->pensum->asignatura_id
        );

        return parent::saveComent(...$args);
    }

    public function previewLesson(int $activityId): void
    {
        $this->previewLessonActivity = Activity::findOrFail($activityId);
        $this->showLessonPreview = true;
    }

    public function closeLessonPreview(): void
    {
        $this->showLessonPreview = false;
        $this->previewLessonActivity = null;
    }

    public function render()
    {
        $filters = array_filter([
            'pestudio_id' => $this->pestudio_id,
            'grado_id' => $this->grado_id,
            'seccion_id' => $this->seccion_id,
            'lapso_id' => $this->lapso_id,
            'profesor_id' => $this->profesor_id,
            'status_activities' => $this->status_activities,
            'filter_observations' => $this->filter_observations ? true : null,
            'filter_revision' => $this->filter_revision ? true : null,
            'filter_status' => $this->filter_status ?: null,
        ], fn($v) => $v !== null && $v !== '');

        $pevaluacions = $this->getPevaluaciones($filters);

        $activeTabIndex = 1;
        if ($this->tabsLapsos && $this->lapso_id) {
            $found = $this->tabsLapsos->search(fn($lapso) => $lapso->id == $this->lapso_id);
            if ($found !== false) {
                $activeTabIndex = $found + 1;
            }
        }

        return view('livewire.leadership.activity-overview', [
            'pevaluacions' => $pevaluacions,
            'activeTabIndex' => $activeTabIndex,
        ]);
    }

    /** @codeCoverageIgnore */
    public function createObservation($id)
    {
        abort(403, 'Leadership no tiene permisos para crear observaciones.');
    }

    /** @codeCoverageIgnore */
    public function saveObservation()
    {
        abort(403, 'Leadership no tiene permisos para guardar observaciones.');
    }

    /** @codeCoverageIgnore */
    public function deleteObservation($id)
    {
        abort(403, 'Leadership no tiene permisos para eliminar observaciones.');
    }
}
