<?php

namespace App\Livewire\Concerns;

use App\Models\app\Academy\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Interacción con lecciones LMS desde listados de actividades
 * (preview "Ver lección" + publicación SCHEDULED → PUBLISHED).
 *
 * Común a coordinación y planning. Cada componente puede sobreescribir
 * `assertLessonAccess()` para validar el scope de rol.
 */
trait InteractsWithLmsLessons
{
    public $showLessonPreview = false;

    public $previewLessonActivity;

    public array $previewData = [];

    public bool $showPublishModal = false;

    public ?int $publishActivityId = null;

    public string $publishActivityTitle = '';

    public ?string $publishPublishAt = null;

    public function previewLesson(int $activityId): void
    {
        $activity = $this->loadLessonPreviewActivity($activityId);

        $this->previewLessonActivity = $activity;
        $this->previewData = $this->buildLessonPreviewData($activity);
        $this->showLessonPreview = true;
    }

    public function closeLessonPreview(): void
    {
        $this->showLessonPreview = false;
        $this->previewLessonActivity = null;
        $this->previewData = [];
    }

    public function confirmPublish(int $activityId): void
    {
        $activity = Activity::find($activityId);
        $this->publishActivityId = $activityId;
        $this->publishActivityTitle = $activity?->topic ?? 'Lección';

        if (! $activity || ! $activity->status) {
            $this->dispatch('notify', message: 'Esta lección está en revisión y no puede publicarse hasta que la actividad asociada sea aprobada.', type: 'warning');
            $this->cancelPublish();

            return;
        }

        $publishAt = $activity?->lmsPublication?->publish_at;
        $this->publishPublishAt = $publishAt
            ? Carbon::parse($publishAt)->format('Y-m-d\TH:i')
            : now()->format('Y-m-d\TH:i');

        $this->showPublishModal = true;
    }

    public function doPublish(): void
    {
        if (! $this->publishActivityId) {
            return;
        }

        $this->validate(['publishPublishAt' => 'nullable|date']);

        $activity = Activity::with('lmsPublication')->findOrFail($this->publishActivityId);

        $asignaturaId = $activity->pevaluacion?->pensum?->asignatura_id;
        if ($asignaturaId) {
            $this->assertLessonAccess($asignaturaId);
        }

        if (! $activity->lmsPublication || $activity->lmsPublication->status !== 'SCHEDULED') {
            $this->dispatch('notify', message: 'Esta lección ya no está programada.', type: 'warning');
            $this->cancelPublish();

            return;
        }

        if (! $activity->status) {
            $this->dispatch('notify', message: 'La actividad debe estar aprobada para poder publicarla.', type: 'warning');
            $this->cancelPublish();

            return;
        }

        $publishAt = $this->publishPublishAt
            ? Carbon::parse($this->publishPublishAt)
            : now();

        $activity->lmsPublication->update([
            'status' => 'PUBLISHED',
            'publish_at' => $publishAt,
            'published_at' => $publishAt->gt(now()) ? null : now(),
            'published_by' => Auth::id(),
        ]);

        $this->dispatch('notify', message: 'Lección publicada correctamente.', type: 'success');
        $this->cancelPublish();
    }

    public function cancelPublish(): void
    {
        $this->showPublishModal = false;
        $this->publishActivityId = null;
        $this->publishActivityTitle = '';
        $this->publishPublishAt = null;
    }

    /**
     * Hook de autorización por rol. Sobreescribir en el componente
     * (p. ej. LeadershipService::assertCanAccessAsignatura) si aplica.
     */
    protected function assertLessonAccess(?int $asignaturaId): void
    {
        // no-op por defecto
    }

    protected function loadLessonPreviewActivity(int $activityId): Activity
    {
        return Activity::with([
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
    }

    protected function buildLessonPreviewData(Activity $activity): array
    {
        return [
            'activity_id' => $activity->id,
            'subject' => $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura',
            'title' => $activity->topic ?? 'Lección',
            'description' => $activity->description ?? '',
            'start_date' => $activity->finicial,
            'end_date' => $activity->ffinal,
            'allow_downloads' => $activity->lmsPublication?->allow_downloads ?? false,
            'review_questions' => collect($activity->lmsSections->toArray())
                ->filter(fn ($s) => ($s['title'] ?? '') === 'Preguntas de Repaso')
                ->flatMap(fn ($s) => collect($s['contents'] ?? [])->pluck('body'))
                ->filter()
                ->implode("\n\n"),
            'sections' => $activity->lmsSections
                ->reject(fn ($s) => $s->title === 'Preguntas de Repaso')
                ->values()
                ->toArray(),
            'resources' => $activity->lmsResources->toArray(),
            'links' => $activity->lmsLinks->toArray(),
            'html_embeds' => $activity->lmsHtmlEmbeds
                ->map(function ($embed): array {
                    $data = $embed->toArray();
                    if (! empty($data['is_mermaid'])) {
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
                ->map(fn ($content, $title) => compact('title', 'content'))
                ->values()
                ->toArray(),
        ];
    }
}
