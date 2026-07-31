<?php

namespace App\Livewire\Leadership;

use App\Services\Leadership\LeadershipService;
use App\Models\app\Academy\Activity;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class LessonMonitor extends Component
{
    use WithPagination;

    public $search = '';
    public $paginate = 15;
    public $area_id = '';
    public $lapso_id = '';
    public $filter_published = false;
    public $filter_scheduled = false;

    // Filters from activities
    public $pestudio_id = '';
    public $grado_id = '';
    public $seccion_id = '';
    public $profesor_id = '';
    public $status_activities = '';
    public $filter_observations = false;
    public $filter_revision = false;
    public $filter_status = '';

    // Modal preview
    public $showLessonPreview = false;
    public $previewLessonId = null;
    public ?array $previewData = null;

    public $list_pestudio, $list_grado, $list_seccion, $list_lapso;
    public $list_profesors;
    public $tabsLapsos;

    public function mount()
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);

        $this->list_pestudio = \App\Models\app\Academy\Pestudio::whereHas('grados.pensums.pevaluacions')
            ->where('planning_module', true)
            ->where('status_active', 'true')
            ->orderBy('order')
            ->pluck('name', 'id');

        $this->list_grado = \App\Models\app\Academy\Grado::active('true')->pluck('name', 'id');
        $this->list_seccion = collect();
        $this->list_lapso = \App\Models\app\Academy\Lapso::select('name', 'id')->orderBy('name')->pluck('name', 'id');
        $this->tabsLapsos = \App\Models\app\Academy\Lapso::orderBy('name')->orderBy('id')->get();

        $this->lapso_id = \App\Models\app\Academy\Lapso::current()?->id;

        $this->setProfesorLists();
    }

    public function render()
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);

        $query = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.lapso',
            'pevaluacion.seccion.grado',
            'pevaluacion.profesor',
            'lmsPublication',
            'lmsSections.contents',
        ]);

        $service->scopeActivities($query);

        if ($this->filter_published) {
            $query->whereHas('lmsPublication', fn($q) => $q->where('status', 'PUBLISHED'));
        }

        if ($this->filter_scheduled) {
            $query->whereHas('lmsPublication', fn($q) => $q->where('status', 'SCHEDULED'));
        }

        if ($this->search) {
            $query->where('topic', 'like', "%{$this->search}%");
        }

        if ($this->lapso_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapso_id));
        }

        // Activities-style filters
        if ($this->pestudio_id) {
            $query->whereHas('pevaluacion.pensum.pestudio', fn($q) => $q->where('id', $this->pestudio_id));
        }
        if ($this->grado_id) {
            $query->whereHas('pevaluacion.seccion.grado', fn($q) => $q->where('id', $this->grado_id));
        }
        if ($this->seccion_id) {
            $query->whereHas('pevaluacion.seccion', fn($q) => $q->where('id', $this->seccion_id));
        }
        if ($this->profesor_id) {
            $query->whereHas('pevaluacion.profesor', fn($q) => $q->where('id', $this->profesor_id));
        }

        $lessons = $query->orderBy('created_at', 'desc')
            ->paginate($this->paginate);

        $lapsos = \App\Models\app\Academy\Lapso::orderBy('name')->get();

        $activeTabIndex = 1;
        if ($this->tabsLapsos && $this->lapso_id) {
            $found = $this->tabsLapsos->search(fn($lapso) => $lapso->id == $this->lapso_id);
            if ($found !== false) {
                $activeTabIndex = $found + 1;
            }
        }

        return view('livewire.leadership.lesson-monitor', [
            'lessons' => $lessons,
            'lapsos' => $lapsos,
            'activeTabIndex' => $activeTabIndex,
        ]);
    }

    // ─── FILTERS CASCADE ────────────────────────────────────────

    public function updatedPestudioId($value)
    {
        $this->resetPage();
        if ($value) {
            $this->list_grado = \App\Models\app\Academy\Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->pluck('name', 'id');
            $this->list_profesors = \App\Models\app\Academy\Profesor::list_profesors_pestudio($value);
        } else {
            $this->list_grado = \App\Models\app\Academy\Grado::active('true')->pluck('name', 'id');
        }
        $this->grado_id = null;
        $this->seccion_id = null;
        $this->list_seccion = collect();
    }

    public function updatedGradoId($value)
    {
        $this->resetPage();
        if ($value) {
            $this->list_seccion = \App\Models\app\Academy\Seccion::list_seccion_grado($value);
        } else {
            $this->list_seccion = collect();
        }
        $this->seccion_id = null;
    }

    public function updatedSeccionId($value) { $this->resetPage(); }
    public function updatedProfesorId($value) { $this->resetPage(); }
    public function updatedStatusActivities($value) { $this->resetPage(); }
    public function updatedFilterObservations($value) { $this->resetPage(); }
    public function updatedFilterRevision($value) { $this->resetPage(); }
    public function updatedFilterStatus($value) { $this->resetPage(); }
    public function updatedFilterPublished($value) { $this->resetPage(); }
    public function updatedFilterScheduled($value) { $this->resetPage(); }

    // ─── PUBLISH CONFIRMATION MODAL ──────────────────────────────
    public bool $showPublishModal = false;
    public ?int $publishActivityId = null;
    public string $publishActivityTitle = '';
    public ?string $publishPublishAt = null;   // fecha de publicación; vacío → ahora

    public function confirmPublishLesson(int $lessonId): void
    {
        $activity = Activity::find($lessonId);
        $this->publishActivityId = $lessonId;
        $this->publishActivityTitle = $activity?->topic ?? 'Lección';

        // Pre-cargar la fecha programada (publish_at) si existe; sino, ahora.
        // Formato Y-m-d\TH:i para el input datetime-local.
        $publishAt = $activity?->lmsPublication?->publish_at;
        $this->publishPublishAt = $publishAt
            ? \Carbon\Carbon::parse($publishAt)->format('Y-m-d\TH:i')
            : now()->format('Y-m-d\TH:i');

        $this->showPublishModal = true;
    }

    public function doPublishLesson(): void
    {
        if (!$this->publishActivityId) {
            return;
        }

        $this->validate(['publishPublishAt' => 'nullable|date']);

        $service = app(LeadershipService::class, ['user' => Auth::user()]);

        $activity = Activity::with('lmsPublication')->findOrFail($this->publishActivityId);

        // Verificar que el líder tenga acceso a esta asignatura
        $asignaturaId = $activity->pevaluacion?->pensum?->asignatura_id;
        if ($asignaturaId) {
            $service->assertCanAccessAsignatura($asignaturaId);
        }

        if (!$activity->lmsPublication || $activity->lmsPublication->status !== 'SCHEDULED') {
            $this->dispatch('notify', message: 'Esta lección ya no está programada.', type: 'warning');
            $this->cancelPublishLesson();
            return;
        }

        if (!$activity->status) {
            $this->dispatch('notify', message: 'La actividad debe estar aprobada para poder publicarla.', type: 'warning');
            $this->cancelPublishLesson();
            return;
        }

        $publishAt = $this->publishPublishAt
            ? \Carbon\Carbon::parse($this->publishPublishAt)
            : now();

        $activity->lmsPublication->update([
            'status'       => 'PUBLISHED',
            'publish_at'   => $publishAt,                 // nunca nulo (default now())
            'published_at' => $publishAt->gt(now()) ? null : now(),
            'published_by' => Auth::id(),
        ]);

        $this->dispatch('notify', message: 'Lección publicada correctamente.', type: 'success');
        $this->cancelPublishLesson();
    }

    public function cancelPublishLesson(): void
    {
        $this->showPublishModal = false;
        $this->publishActivityId = null;
        $this->publishActivityTitle = '';
        $this->publishPublishAt = null;
    }
    public function updatedLapsoId($value) { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    // ─── MODAL PREVIEW (student-preview component) ──────────────

    public function previewLesson(int $activityId): void
    {
        $activity = Activity::with([
            'pevaluacion.lapso',
            'pevaluacion.seccion',
            'pevaluacion.pensum.grado',
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.pestudio.peducativo.pescolar.institucion',
            'lmsPublication',
            'lmsSections' => fn($q) => $q->where('is_visible', true)->orderBy('sort_order'),
            'lmsSections.contents' => fn($q) => $q->where('is_visible', true),
            'lmsResources' => fn($q) => $q->where('is_visible', true),
            'lmsResources.media',
            'lmsLinks' => fn($q) => $q->where('is_visible', true),
            'lmsHtmlEmbeds' => fn($q) => $q->where('is_visible', true),
        ])->findOrFail($activityId);

        $this->previewData = [
            'activity_id'   => $activity->id,
            'subject'       => $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura',
            'title'         => $activity->topic ?? 'Lección',
            'description'   => $activity->description ?? '',
            'start_date'    => $activity->finicial,
            'end_date'      => $activity->ffinal,
            'allow_downloads' => $activity->lmsPublication?->allow_downloads ?? false,
            'sections'      => $activity->lmsSections->toArray(),
            'resources'     => $activity->lmsResources->toArray(),
            'links'         => $activity->lmsLinks->toArray(),
            'html_embeds'   => $activity->lmsHtmlEmbeds
                ->map(function ($embed): array {
                    $data = $embed->toArray();
                    if (!empty($data['is_mermaid'])) return $data;

                    $content = trim($data['html_content'] ?? '');

                    if (preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', $content)) {
                        $data['is_mermaid'] = true;
                        return $data;
                    }
                    if (preg_match('/data-mermaid-code="([^"]*)"/', $content)) {
                        $data['is_mermaid'] = true;
                        return $data;
                    }
                    if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $content, $m)) {
                        $inner = trim(strip_tags($m[1]));
                        if (preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', $inner)) {
                            $data['is_mermaid'] = true;
                            return $data;
                        }
                    }

                    $data['is_mermaid'] = false;
                    return $data;
                })
                ->values()
                ->toArray(),
            // Portada institucional
            'institution'       => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->name ?? '',
            'institution_rif'   => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->rif_institution ?? '',
            'institution_city'  => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->city ?? '',
            'periodo'           => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->name ?? '',
            'periodo_finicial'  => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->finicial ?? '',
            'periodo_ffinal'    => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->ffinal ?? '',
            'plan_educativo'    => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->name ?? '',
            'plan_educativo_desc' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->description ?? '',
            'plan_estudio'      => $activity->pevaluacion?->pensum?->pestudio?->name ?? '',
            'plan_estudio_code' => $activity->pevaluacion?->pensum?->pestudio?->code ?? '',
            'grado'             => $activity->pevaluacion?->pensum?->grado?->name ?? '',
            'grado_code'        => $activity->pevaluacion?->pensum?->grado?->code ?? '',
            'seccion'           => $activity->pevaluacion?->seccion?->name ?? '',
            'seccion_desc'      => $activity->pevaluacion?->seccion?->description ?? '',
            'seccion_students'  => $activity->pevaluacion?->seccion?->amount_student ?? '',
            'pensum'            => $activity->pevaluacion?->pensum?->asignatura?->name ?? '',
            'asignatura_code'   => $activity->pevaluacion?->pensum?->asignatura?->code ?? '',
            'asignatura_hours'  => $activity->pevaluacion?->pensum?->asignatura?->hour_t_week ?? '',
            'lapso'             => $activity->pevaluacion?->lapso?->name ?? '',
            'lapso_finicial'    => $activity->pevaluacion?->lapso?->finicial ?? '',
            'lapso_ffinal'      => $activity->pevaluacion?->lapso?->ffinal ?? '',
            // Activity extras
            'thematic'          => $activity->thematic ?? '',
            'references'        => $activity->references ?? '',
            'activity_status'   => $activity->status ?? false,
            'teaching'          => $activity->teaching ?? '',
            'has_teaching_structure' => $activity->hasTeachingStructure(),
            'teaching_sections' => collect($activity->getTeachingSections())
                ->map(fn($content, $title) => compact('title', 'content'))
                ->values()
                ->toArray(),
        ];

        $this->previewLessonId = $activityId;
        $this->showLessonPreview = true;
    }

    public function closeLessonPreview(): void
    {
        $this->showLessonPreview = false;
        $this->previewLessonId = null;
        $this->previewData = null;
    }

    public function selectLapso($id)
    {
        $this->lapso_id = $id;
        $this->resetPage();
    }

    public function setProfesorLists($value = null)
    {
        $profesors = \App\Models\app\Academy\Profesor::select('profesors.id')
            ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")
            ->where('profesors.status_active', true)
            ->orderBy('profesors.lastname');

        if ($value) {
            $profesors->where(function ($q) use ($value) {
                $q->where('profesors.name', 'like', "%{$value}%")
                  ->orWhere('profesors.lastname', 'like', "%{$value}%");
            });
        }

        $this->list_profesors = $profesors->pluck('profesor_fullname', 'id');
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
