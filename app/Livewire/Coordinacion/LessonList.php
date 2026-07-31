<?php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Services\Lms\LmsPublicationService;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class LessonList extends Component
{
    use WithPagination, WireUiActions, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    public $lapsoId = '';
    public $pestudioId = '';
    public $profesorId = '';
    public $paginate = 15;
    public $lapsos;
    public $listPestudio;
    public $listProfesores = [];
    public $filterStatus = '';
    protected $paginationTheme = 'tailwind';

    // ─── Preview modal ────────────────────────────────────────────
    public ?int $previewLessonId = null;
    public ?array $previewData = null;
    public bool $showLessonPreview = false;

    // ─── Publish modal ───────────────────────────────────────────
    public bool $showPublishModal = false;
    public ?int $publishLessonId = null;
    public string $publishLessonTitle = '';
    public ?string $publishPublishAt = null;

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
        $service = $this->getCoordinacionService();

        $this->listPestudio = Pestudio::whereIn('id', $service->getPestudioIds())
            ->where('status_active', 'true')
            ->orderBy('order')
            ->get()
            ->pluck('name', 'id');

        $this->listProfesores = Profesor::where('status_active', 'true')
            ->whereHas('pevaluacions.pensum', fn($q) => $q->whereIn('pestudio_id', $service->getPestudioIds()))
            ->orderBy('lastname')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');

        $this->lapsos = Lapso::orderBy('finicial')
            ->orderBy('id')
            ->get();

        // Default to current lapso
        $lapsoCurrent = Lapso::current();
        $this->lapsoId = $lapsoCurrent?->id ?? '';
    }

    public function selectLapso($id): void
    {
        $this->lapsoId = $id;
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.pestudio.peducativo',
            'pevaluacion.profesor',
            'pevaluacion.lapso',
            'lmsPublication',
            'lmsSections',
        ]);

        $query = $service->scopeActivities($query);
        $query->whereHas('lmsPublication'); // Solo actividades con publicación LMS

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }
        if ($this->pestudioId) {
            $query->whereHas('pevaluacion.pensum', fn($q) => $q->where('pestudio_id', $this->pestudioId));
        }
        if ($this->profesorId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('profesor_id', $this->profesorId));
        }
        if ($this->filterStatus) {
            $query->whereHas('lmsPublication', fn($q) => $q->where('status', $this->filterStatus));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%");
            });
        }

        $lessons = $query->orderBy('activities.created_at', 'desc')
            ->paginate($this->paginate);

        return view('livewire.coordinacion.lesson-list', [
            'lessons' => $lessons,
            'lapsos'  => $this->lapsos,
        ])->layout('coordinacion.layouts.app');
    }

    // ─── Preview modal ──────────────────────────────────────────

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
            'institution'          => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->name ?? '',
            'institution_rif'      => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->rif_institution ?? '',
            'institution_city'     => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->city ?? '',
            'periodo'              => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->name ?? '',
            'periodo_finicial'     => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->finicial ?? '',
            'periodo_ffinal'       => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->ffinal ?? '',
            'plan_educativo'       => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->name ?? '',
            'plan_educativo_desc'  => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->description ?? '',
            'plan_estudio'         => $activity->pevaluacion?->pensum?->pestudio?->name ?? '',
            'plan_estudio_code'    => $activity->pevaluacion?->pensum?->pestudio?->code ?? '',
            'grado'                => $activity->pevaluacion?->pensum?->grado?->name ?? '',
            'grado_code'           => $activity->pevaluacion?->pensum?->grado?->code ?? '',
            'seccion'              => $activity->pevaluacion?->seccion?->name ?? '',
            'seccion_desc'         => $activity->pevaluacion?->seccion?->description ?? '',
            'seccion_students'     => $activity->pevaluacion?->seccion?->amount_student ?? '',
            'pensum'               => $activity->pevaluacion?->pensum?->asignatura?->name ?? '',
            'asignatura_code'      => $activity->pevaluacion?->pensum?->asignatura?->code ?? '',
            'asignatura_hours'     => $activity->pevaluacion?->pensum?->asignatura?->hour_t_week ?? '',
            'lapso'                => $activity->pevaluacion?->lapso?->name ?? '',
            'lapso_finicial'       => $activity->pevaluacion?->lapso?->finicial ?? '',
            'lapso_ffinal'         => $activity->pevaluacion?->lapso?->ffinal ?? '',
            'thematic'             => $activity->thematic ?? '',
            'references'           => $activity->references ?? '',
            'activity_status'      => $activity->status ?? false,
            'teaching'             => $activity->teaching ?? '',
            'has_teaching_structure' => $activity->hasTeachingStructure(),
            'teaching_sections'    => collect($activity->getTeachingSections())
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

    // ─── Publish ─────────────────────────────────────────────────

    public function confirmPublish(int $activityId): void
    {
        $activity = Activity::findOrFail($activityId);
        $this->publishLessonId = $activityId;
        $this->publishLessonTitle = $activity->topic ?? 'Lección';
        $this->publishPublishAt = null; // vacío → publicar de inmediato
        $this->showPublishModal = true;
    }

    public function cancelPublish(): void
    {
        $this->showPublishModal = false;
        $this->publishLessonId = null;
        $this->publishLessonTitle = '';
        $this->publishPublishAt = null;
    }

    public function doPublish(): void
    {
        if (!$this->publishLessonId) {
            return;
        }
        $this->validate(['publishPublishAt' => 'nullable|date']);

        $activity = Activity::findOrFail($this->publishLessonId);
        app(LmsPublicationService::class)->publish(
            $activity,
            ['publish_at' => $this->publishPublishAt, 'allow_comments' => true, 'allow_downloads' => true],
            auth()->id()
        );

        $this->cancelPublish();
        $this->notification()->success(
            title: 'Publicada',
            description: 'La lección ahora es visible para los estudiantes.'
        );
        $this->resetPage();
    }

    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPestudioId() { $this->resetPage(); }
    public function updatingProfesorId() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }
}
