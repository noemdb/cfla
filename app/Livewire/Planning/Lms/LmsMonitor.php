<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Services\Lms\LmsPublicationService;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class LmsMonitor extends Component
{
    use WireUiActions;
    use WithPagination;

    // ─── View mode ─────────────────────────────────────────────
    public string $viewMode = 'table';

    public function updatedViewMode(string $value): void
    {
        session()->put('lms_monitor_view_mode', $value);
    }

    // ─── Filtros ───────────────────────────────────────────────
    public string $search = '';

    public string $filterStatus = '';

    public string $filterProfesor = '';

    public string $filterGrado = '';

    public string $filterSeccion = '';

    public string $filterAsignatura = '';

    public string $filterPestudio = '';

    /** @var \Illuminate\Support\Collection Secciones filtradas por grado seleccionado */
    public $seccionesFiltradas;

    /** @var \Illuminate\Support\Collection Grados filtrados por pestudio seleccionado */
    public $gradosFiltrados;

    // ─── Bulk selection ────────────────────────────────────────
    public array $selectedIds = [];

    public bool $selectAll = false;

    // ─── Schedule modal ────────────────────────────────────────
    public bool $showScheduleModal = false;

    public ?int $scheduleActivityId = null;

    public ?string $schedulePublishAt = null;

    public ?string $scheduleUnpublishAt = null;

    public bool $scheduleAllowComments = true;

    public bool $scheduleAllowDownloads = true;

    public string $scheduleNotes = '';

    // ─── Settings modal ────────────────────────────────────────
    public bool $showSettingsModal = false;

    public ?int $settingsActivityId = null;

    public bool $settingsAllowComments = true;

    public bool $settingsAllowDownloads = true;

    public string $settingsNotes = '';

    public string $settingsStatus = '';

    // ─── Publish confirmation modal ────────────────────────────
    public bool $showPublishModal = false;

    public ?int $publishActivityId = null;

    public string $publishActivityTitle = '';

    public ?string $publishPublishAt = null;

    // ─── Bulk publish modal ────────────────────────────────────
    public bool $showBulkPublishModal = false;

    public ?string $bulkPublishAt = null;

    // ─── Preview modal (student-preview component) ────────────
    public bool $showPreviewModal = false;

    public ?array $previewData = null;

    // ─── Modal actividad asociada (revisión / aprobación) ──────
    public bool $showActivityModal = false;

    public $activity = null;

    public $activity_id = null;

    public $comments = '';

    public $activity_status = 0;

    public function openActivityReview(int $activityId): void
    {
        $activity = Activity::with([
            'pevaluacion.lapso',
            'pevaluacion.seccion.grado',
            'pevaluacion.pensum.asignatura',
            'pevaluacion.profesor',
            'lmsPublication',
        ])->findOrFail($activityId);

        $this->activity = $activity;
        $this->activity_id = $activityId;
        $this->comments = $activity->comments ?? '';
        $this->activity_status = (int) ($activity->status ?? 0);
        $this->showActivityModal = true;
    }

    public function saveActivityReview(): void
    {
        if (! $this->activity_id) {
            return;
        }

        $this->validate([
            'comments' => 'nullable|string|max:65535',
            'activity_status' => 'required|boolean',
        ]);

        $activity = Activity::findOrFail($this->activity_id);

        $activity->comments = $this->comments;
        $activity->status = (bool) $this->activity_status;
        $activity->save();

        $this->notification()->success(
            title: $this->activity_status ? 'Actividad aprobada' : 'Actividad en revisión',
            description: $this->activity_status
                ? 'La actividad asociada fue aprobada correctamente.'
                : 'La actividad asociada fue marcada en revisión.',
        );
        $this->closeActivityReview();
    }

    public function closeActivityReview(): void
    {
        $this->showActivityModal = false;
        $this->activity = null;
        $this->activity_id = null;
        $this->comments = '';
        $this->activity_status = 0;
    }

    public function openPreview(int $activityId): void
    {
        $activity = Activity::with([
            'pevaluacion.lapso',
            'pevaluacion.seccion',
            'pevaluacion.pensum.grado',
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.pestudio.peducativo.pescolar.institucion',
            'lmsPublication',
            'lmsSections' => fn ($q) => $q->where('is_visible', true)->orderBy('sort_order'),
            'lmsSections.contents' => fn ($q) => $q->where('is_visible', true),
            'lmsResources' => fn ($q) => $q->where('is_visible', true),
            'lmsResources.media',
            'lmsLinks' => fn ($q) => $q->where('is_visible', true),
            'lmsHtmlEmbeds' => fn ($q) => $q->where('is_visible', true),
        ])->findOrFail($activityId);

        $this->previewData = [
            'activity_id' => $activity->id,
            'subject' => $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura',
            'title' => $activity->topic ?? 'Lección',
            'description' => $activity->description ?? '',
            'start_date' => $activity->finicial,
            'end_date' => $activity->ffinal,
            'allow_downloads' => $activity->lmsPublication?->allow_downloads ?? false,
            'sections' => $activity->lmsSections->toArray(),
            'resources' => $activity->lmsResources->toArray(),
            'links' => $activity->lmsLinks->toArray(),
            'html_embeds' => $activity->lmsHtmlEmbeds
                ->map(function ($embed): array {
                    $data = $embed->toArray();

                    // Mimica de ensureMermaidWrapper() del LessonWizard:
                    // detecta código Mermaid plano en html_content y
                    // asigna is_mermaid=true dinámicamente (no persiste en BD).
                    if (! empty($data['is_mermaid'])) {
                        return $data;
                    }

                    $content = trim($data['html_content'] ?? '');

                    // ¿Empieza con keyword Mermaid?
                    if (preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', $content)) {
                        $data['is_mermaid'] = true;

                        return $data;
                    }

                    // ¿Formato legacy con data-mermaid-code?
                    if (preg_match('/data-mermaid-code="([^"]*)"/', $content)) {
                        $data['is_mermaid'] = true;

                        return $data;
                    }

                    // ¿Formato legacy con div.mermaid inline?
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
            'institution' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->name ?? '',
            'institution_rif' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->rif_institution ?? '',
            'institution_city' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->city ?? '',
            'periodo' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->name ?? '',
            'periodo_finicial' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->finicial ?? '',
            'periodo_ffinal' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->ffinal ?? '',
            'plan_educativo' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->name ?? '',
            'plan_educativo_desc' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->description ?? '',
            'plan_estudio' => $activity->pevaluacion?->pensum?->pestudio?->name ?? '',
            'plan_estudio_code' => $activity->pevaluacion?->pensum?->pestudio?->code ?? '',
            'grado' => $activity->pevaluacion?->pensum?->grado?->name ?? '',
            'grado_code' => $activity->pevaluacion?->pensum?->grado?->code ?? '',
            'seccion' => $activity->pevaluacion?->seccion?->name ?? '',
            'seccion_desc' => $activity->pevaluacion?->seccion?->description ?? '',
            'seccion_students' => $activity->pevaluacion?->seccion?->amount_student ?? '',
            'pensum' => $activity->pevaluacion?->pensum?->asignatura?->name ?? '',
            'asignatura_code' => $activity->pevaluacion?->pensum?->asignatura?->code ?? '',
            'asignatura_hours' => $activity->pevaluacion?->pensum?->asignatura?->hour_t_week ?? '',
            'lapso' => $activity->pevaluacion?->lapso?->name ?? '',
            'lapso_finicial' => $activity->pevaluacion?->lapso?->finicial ?? '',
            'lapso_ffinal' => $activity->pevaluacion?->lapso?->ffinal ?? '',
            // Activity extras
            'thematic' => $activity->thematic ?? '',
            'references' => $activity->references ?? '',
            'activity_status' => $activity->status ?? false,
            'teaching' => $activity->teaching ?? '',
            'has_teaching_structure' => $activity->hasTeachingStructure(),
            'teaching_sections' => collect($activity->getTeachingSections())
                ->map(fn ($content, $title) => compact('title', 'content'))
                ->values()
                ->toArray(),
        ];

        $this->showPreviewModal = true;
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewData = null;
    }

    // ─── Lifecycle ─────────────────────────────────────────────
    public function mount(): void
    {
        $this->seccionesFiltradas = collect();
        $this->gradosFiltrados = collect();
        $this->viewMode = session('lms_monitor_view_mode', 'table');
    }

    /** Hook: cuando cambia filterPestudio, actualiza los grados disponibles */
    public function updatedFilterPestudio(string $value): void
    {
        if (blank($value)) {
            $this->gradosFiltrados = Grado::whereHas('pensums.pevaluacions.activities')
                ->where('status_active', 'true')
                ->orderBy('name')
                ->get();
        } else {
            $this->gradosFiltrados = Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->whereHas('pensums.pevaluacions.activities')
                ->orderBy('name')
                ->get();
        }

        $this->filterGrado = '';
        $this->filterSeccion = '';
        $this->seccionesFiltradas = collect();
        $this->resetPage();
    }

    /** Hook: cuando cambia filterGrado, actualiza las secciones disponibles */
    public function updatedFilterGrado(string $value): void
    {
        if (blank($value)) {
            $this->seccionesFiltradas = collect();
            $this->filterSeccion = '';

            return;
        }

        $this->seccionesFiltradas = Seccion::where('status_active', 'true')
            ->whereHas('pevaluacions.pensum', fn ($q) => $q->where('grado_id', $value))
            ->whereHas('pevaluacions.activities')
            ->orderBy('name')
            ->get();

        // Si la sección actual no pertenece al nuevo grado, la reseteamos
        if ($this->filterSeccion && ! $this->seccionesFiltradas->contains('id', (int) $this->filterSeccion)) {
            $this->filterSeccion = '';
        }
    }

    // ─── Rules ─────────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'schedulePublishAt' => 'nullable|date',
            'scheduleUnpublishAt' => 'nullable|date|after_or_equal:schedulePublishAt',
            'scheduleAllowComments' => 'boolean',
            'scheduleAllowDownloads' => 'boolean',
            'scheduleNotes' => 'nullable|string|max:500',
            'settingsAllowComments' => 'boolean',
            'settingsAllowDownloads' => 'boolean',
            'settingsNotes' => 'nullable|string|max:500',
            'publishPublishAt' => 'nullable|date',
            'bulkPublishAt' => 'nullable|date',
        ];
    }

    // ─── Stats cache ───────────────────────────────────────────
    protected function getStats(): array
    {
        return [
            'total' => Activity::count(),
            'published' => Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'PUBLISHED'))->count(),
            'scheduled' => Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))->count(),
            'draft' => Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'DRAFT'))->count(),
            'archived' => Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'ARCHIVED'))->count(),
            'withContent' => Activity::whereHas('lmsSections')->count(),
            'totalActivities' => Activity::count(),
        ];
    }

    // ─── Publicación ──────────────────────────────────────────
    public function confirmPublish(int $activityId): void
    {
        $activity = Activity::find($activityId);
        $this->publishActivityId = $activityId;
        $this->publishActivityTitle = $activity?->topic ?? 'Lección';
        $this->publishPublishAt = null; // vacío → publicar de inmediato
        $this->showPublishModal = true;
    }

    public function doPublish(): void
    {
        if (! $this->publishActivityId) {
            return;
        }
        $this->validate(['publishPublishAt' => 'nullable|date']);
        $this->publish($this->publishActivityId, $this->publishPublishAt);
        $this->showPublishModal = false;
        $this->publishActivityId = null;
        $this->publishActivityTitle = '';
        $this->publishPublishAt = null;
    }

    public function cancelPublish(): void
    {
        $this->showPublishModal = false;
        $this->publishActivityId = null;
        $this->publishActivityTitle = '';
        $this->publishPublishAt = null;
    }

    public function publish(int $activityId, ?string $publishAt = null): void
    {
        $activity = Activity::findOrFail($activityId);
        if (! $this->ensureActivityApproved($activity)) {
            return;
        }
        app(LmsPublicationService::class)->publish(
            $activity,
            ['publish_at' => $publishAt, 'allow_comments' => true, 'allow_downloads' => true],
            auth()->id(),
            true // Planificación: rol autorizado para publicar
        );
        $this->notification()->success('Publicado', 'El contenido ahora es visible para los estudiantes.');
        $this->resetPage();
    }

    /**
     * Prohibición de publicación: no se puede publicar/programar una lección
     * cuya activity asociada esté en revisión (status=0, no aprobada).
     * Misma regla que Coordinación/LessonList. Retorna false si bloquea.
     */
    private function ensureActivityApproved(Activity $activity): bool
    {
        if (! $activity->status) {
            $this->notification()->warning(
                title: 'No aprobada',
                description: 'La activity asociada está en revisión. Apruébala antes de publicar la lección.'
            );

            return false;
        }

        return true;
    }

    public function unpublish(int $activityId): void
    {
        $activity = Activity::findOrFail($activityId);
        app(LmsPublicationService::class)->unpublish($activity, auth()->id());
        $this->notification()->success('Archivado', 'El contenido ya no es visible para los estudiantes.');
        $this->resetPage();
    }

    public function setDraft(int $activityId): void
    {
        $pub = LmsActivityPublication::where('activity_id', $activityId)->first();
        if ($pub) {
            $pub->update(['status' => 'DRAFT']);
            LmsActivityLog::record($activityId, auth()->id(), 'UNPUBLISH');
        }
        $this->notification()->info('Borrador', 'La publicación se revirtió a borrador.');
        $this->resetPage();
    }

    // ─── Schedule modal ────────────────────────────────────────
    public function openSchedule(int $activityId): void
    {
        $this->scheduleActivityId = $activityId;
        $this->schedulePublishAt = null;
        $this->scheduleUnpublishAt = null;
        $this->scheduleAllowComments = true;
        $this->scheduleAllowDownloads = true;
        $this->scheduleNotes = '';
        $this->showScheduleModal = true;
    }

    public function saveSchedule(): void
    {
        $this->validate([
            'schedulePublishAt' => 'required|date|after_or_equal:now',
            'scheduleUnpublishAt' => 'nullable|date|after_or_equal:schedulePublishAt',
            'scheduleAllowComments' => 'boolean',
            'scheduleAllowDownloads' => 'boolean',
            'scheduleNotes' => 'nullable|string|max:500',
        ]);

        $activity = Activity::findOrFail($this->scheduleActivityId);
        if (! $this->ensureActivityApproved($activity)) {
            $this->showScheduleModal = false;
            $this->scheduleActivityId = null;

            return;
        }
        app(LmsPublicationService::class)->publish($activity, [
            'publish_at' => $this->schedulePublishAt,
            'unpublish_at' => $this->scheduleUnpublishAt,
            'allow_comments' => $this->scheduleAllowComments,
            'allow_downloads' => $this->scheduleAllowDownloads,
            'notes' => $this->scheduleNotes,
        ], auth()->id(), true); // Planificación: rol autorizado para publicar

        $this->showScheduleModal = false;
        $this->scheduleActivityId = null;
        $this->notification()->success('Programado', 'La publicación fue programada exitosamente.');
        $this->resetPage();
    }

    // ─── Settings modal ────────────────────────────────────────
    public function openSettings(int $activityId): void
    {
        $pub = LmsActivityPublication::where('activity_id', $activityId)->firstOrFail();
        $this->settingsActivityId = $activityId;
        $this->settingsAllowComments = $pub->allow_comments;
        $this->settingsAllowDownloads = $pub->allow_downloads;
        $this->settingsNotes = $pub->notes ?? '';
        $this->settingsStatus = $pub->status;
        $this->showSettingsModal = true;
    }

    public function saveSettings(): void
    {
        $this->validate([
            'settingsAllowComments' => 'boolean',
            'settingsAllowDownloads' => 'boolean',
            'settingsNotes' => 'nullable|string|max:500',
        ]);

        $pub = LmsActivityPublication::where('activity_id', $this->settingsActivityId)->first();
        if ($pub) {
            $pub->update([
                'allow_comments' => $this->settingsAllowComments,
                'allow_downloads' => $this->settingsAllowDownloads,
                'notes' => $this->settingsNotes ?: null,
            ]);
            LmsActivityLog::record($this->settingsActivityId, auth()->id(), 'EDIT');
        }

        $this->showSettingsModal = false;
        $this->settingsActivityId = null;
        $this->notification()->success('Guardado', 'Configuración de publicación actualizada.');
        $this->resetPage();
    }

    // ─── Bulk actions ──────────────────────────────────────────
    public function clearFilters(): void
    {
        $this->reset([
            'search', 'filterStatus', 'filterProfesor',
            'filterGrado', 'filterSeccion', 'filterAsignatura', 'filterPestudio',
        ]);
        $this->seccionesFiltradas = collect();
        $this->gradosFiltrados = collect();
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedIds = $this->getFilteredPublicationIds();
        } else {
            $this->selectedIds = [];
        }
    }

    public function toggleSelect(int $activityId): void
    {
        $idx = array_search($activityId, $this->selectedIds);
        if ($idx !== false) {
            unset($this->selectedIds[$idx]);
            $this->selectedIds = array_values($this->selectedIds);
        } else {
            $this->selectedIds[] = $activityId;
        }
        $this->selectAll = false;
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function openBulkPublishModal(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        $this->bulkPublishAt = null; // vacío → publicar de inmediato
        $this->showBulkPublishModal = true;
    }

    public function cancelBulkPublish(): void
    {
        $this->showBulkPublishModal = false;
        $this->bulkPublishAt = null;
    }

    public function bulkPublish(): void
    {
        $this->validate(['bulkPublishAt' => 'nullable|date']);

        $count = 0;
        $skipped = 0;
        foreach ($this->selectedIds as $id) {
            $activity = Activity::find($id);
            if (! $activity) {
                continue;
            }
            if (! $activity->status) {
                // Prohibición: activity en revisión → se omite (no se publica).
                $skipped++;

                continue;
            }
            app(LmsPublicationService::class)->publish(
                $activity,
                ['publish_at' => $this->bulkPublishAt, 'allow_comments' => true, 'allow_downloads' => true],
                auth()->id(),
                true // Planificación: rol autorizado para publicar
            );
            $count++;
        }
        $this->showBulkPublishModal = false;
        $this->bulkPublishAt = null;
        $this->clearSelection();
        if ($count > 0) {
            $this->notification()->success('Publicación masiva', "$count contenido(s) publicado(s)." . ($skipped ? " $skipped omitido(s) por estar en revisión." : ''));
        } elseif ($skipped > 0) {
            $this->notification()->warning('Nada publicado', "$skipped seleccionado(s) tienen la activity en revisión. Apruébalas antes de publicar.");
        }
        $this->resetPage();
    }

    public function bulkUnpublish(): void
    {
        $count = 0;
        foreach ($this->selectedIds as $id) {
            $activity = Activity::find($id);
            if ($activity) {
                app(LmsPublicationService::class)->unpublish($activity, auth()->id());
                $count++;
            }
        }
        $this->clearSelection();
        $this->notification()->success('Archivado masivo', "$count contenido(s) archivado(s).");
        $this->resetPage();
    }

    public function bulkDelete(): void
    {
        $count = 0;
        foreach ($this->selectedIds as $id) {
            $pub = LmsActivityPublication::where('activity_id', $id)->first();
            if ($pub) {
                LmsActivityLog::record($id, auth()->id(), 'UNPUBLISH');
                $pub->delete();
                $count++;
            }
        }
        $this->clearSelection();
        $this->notification()->success('Eliminado', "$count publicación(es) eliminada(s) permanentemente.");
        $this->resetPage();
    }

    protected function getFilteredPublicationIds(): array
    {
        return $this->buildFilteredQuery()->pluck('id')->toArray();
    }

    protected function buildFilteredQuery()
    {
        return Activity::query()
            ->when($this->filterStatus, fn ($q) => $q->whereHas('lmsPublication', fn ($sq) => $sq->where('status', $this->filterStatus)))
            ->when($this->filterProfesor, fn ($q) => $q->whereHas('pevaluacion', fn ($sq) => $sq->where('profesor_id', $this->filterProfesor)))
            ->when($this->filterGrado, fn ($q) => $q->whereHas('pevaluacion.pensum', fn ($sq) => $sq->where('grado_id', $this->filterGrado)))
            ->when($this->filterSeccion, fn ($q) => $q->whereHas('pevaluacion', fn ($sq) => $sq->where('seccion_id', $this->filterSeccion)))
            ->when($this->filterAsignatura, fn ($q) => $q->whereHas('pevaluacion.pensum', fn ($sq) => $sq->where('asignatura_id', $this->filterAsignatura)))
            ->when($this->filterPestudio, fn ($q) => $q->whereHas('pevaluacion.pensum', fn ($sq) => $sq->where('pestudio_id', $this->filterPestudio)))
            ->when($this->search, fn ($q) => $q->where('topic', 'like', '%'.$this->search.'%'));
    }

    // ─── Render ────────────────────────────────────────────────
    public function render(): \Illuminate\View\View
    {
        $query = Activity::with([
            'lmsPublication',
            'lmsPublication.publisher',
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.profesor.user',
        ])->withCount([
            'lmsSections',
            'lmsResources',
            'lmsLinks',
        ])
            ->when($this->filterStatus, fn ($q) => $q->whereHas('lmsPublication', fn ($sq) => $sq->where('status', $this->filterStatus)))
            ->when($this->filterProfesor, fn ($q) => $q->whereHas('pevaluacion', fn ($sq) => $sq->where('profesor_id', $this->filterProfesor)))
            ->when($this->filterGrado, fn ($q) => $q->whereHas('pevaluacion.pensum', fn ($sq) => $sq->where('grado_id', $this->filterGrado)))
            ->when($this->filterSeccion, fn ($q) => $q->whereHas('pevaluacion', fn ($sq) => $sq->where('seccion_id', $this->filterSeccion)))
            ->when($this->filterAsignatura, fn ($q) => $q->whereHas('pevaluacion.pensum', fn ($sq) => $sq->where('asignatura_id', $this->filterAsignatura)))
            ->when($this->filterPestudio, fn ($q) => $q->whereHas('pevaluacion.pensum', fn ($sq) => $sq->where('pestudio_id', $this->filterPestudio)))
            ->when($this->search, fn ($q) => $q->where('topic', 'like', '%'.$this->search.'%'));

        return view('livewire.planning.lms.monitor', [
            'publications' => $query->latest('updated_at')->paginate(20),
            'stats' => $this->getStats(),
            'profesores' => Profesor::with('user')->whereHas('pevaluacions.activities')->where('status_active', 'true')->orderBy('lastname')->orderBy('name')->get(),
            'grados' => $this->gradosFiltrados->isNotEmpty()
                ? $this->gradosFiltrados
                : Grado::whereHas('pensums.pevaluacions.activities')->where('status_active', 'true')->orderBy('name')->get(),
            'secciones' => $this->seccionesFiltradas,
            'asignaturas' => Asignatura::whereHas('pensums.pevaluacions.activities')->get(),
            'pestudios' => Pestudio::whereHas('pensums.pevaluacions.activities')->where('status_active', 'true')->get(),
        ])->layout('planning.layouts.app');
    }
}
