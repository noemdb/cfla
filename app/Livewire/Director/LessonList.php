<?php
// app/Livewire/Director/LessonList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Seccion;
use Livewire\Component;
use Livewire\WithPagination;

class LessonList extends Component
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

        // Solo lecciones con contenido LMS (al menos una sección o recurso),
        // mismo criterio que los monitores LMS y los dashboards.
        $query = $service->queryActivities()->withLmsContent()->with([
            'pevaluacion' => fn($q) => $q->with([
                'profesor:id,name,lastname',
                'seccion.grado',
                'pensum.asignatura',
                'pensum.pestudio.peducativo',
                'lapso',
            ]),
            'lmsPublication',
            'lmsSections.contents',
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

        $lessons = $query->orderBy('activities.created_at', 'desc')->paginate($this->paginate);
        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.director.lesson-list', [
            'lessons' => $lessons,
            'lapsos'  => $lapsos,
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

    // ─── Preview modal (student-preview component, solo lectura) ──────────

    public bool $showPreviewModal = false;

    public ?array $previewData = null;

    public function openPreview(int $activityId): void
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
            'activity_id' => $activity->id,
            'subject' => $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura',
            'title' => $activity->topic ?? 'Lección',
            'description' => $activity->description ?? '',
            'start_date' => $activity->finicial,
            'end_date' => $activity->ffinal,
            'allow_downloads' => $activity->lmsPublication?->allow_downloads ?? false,
            'review_questions' => collect($activity->lmsSections->toArray())
                ->filter(fn($s) => ($s['title'] ?? '') === 'Preguntas de Repaso')
                ->flatMap(fn($s) => collect($s['contents'] ?? [])->pluck('body'))
                ->filter()
                ->implode("\n\n"),
            'sections' => $activity->lmsSections
                ->reject(fn($s) => $s->title === 'Preguntas de Repaso')
                ->values()
                ->toArray(),
            'resources' => $activity->lmsResources->toArray(),
            'links' => $activity->lmsLinks->toArray(),
            'html_embeds' => $activity->lmsHtmlEmbeds
                ->map(function ($embed): array {
                    $data = $embed->toArray();
                    if (!empty($data['is_mermaid'])) {
                        return $data;
                    }

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
                ->map(fn($content, $title) => compact('title', 'content'))
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
}
