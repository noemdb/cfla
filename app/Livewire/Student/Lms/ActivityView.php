<?php

namespace App\Livewire\Student\Lms;

use App\Livewire\Student\Lms\Concerns\HasStudentScope;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityProgress;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class ActivityView extends Component
{
    use HasStudentScope;
    use WireUiActions;

    public Activity $activity;

    public $sections = [];

    public $resources = [];

    public $links = [];

    public $htmlEmbeds = [];

    public $comments;

    public string $newComment = '';

    public $completed = false;

    /** true cuando now() < publish_at → solo se muestra la 1ª sección. */
    public $isPreview = false;

    /** ¿Comentario aprobado del usuario en esta lección? (C1) */
    public $isCommented = false;

    /** ¿El usuario descargó un recurso de esta lección? (C1) */
    public $hasDownload = false;

    /** ¿Mostrar la mascota? (C4) — oculta para 13–15 años. */
    public bool $showMascot = false;

    /** ¿Mascota con énfasis (ojos de estrella)? (C4) — solo 5–8 años. */
    public bool $mascotEmphasis = false;

    /** ¿Modo lectura (franja 5–8)? (F1) — tipografía mayor y menos opciones. */
    public bool $modoLectura = false;

    /** ¿Se ofrece el toggle de modo libro? (≥2 secciones, publicada, no modo lectura). */
    public bool $flipEnabled = false;

    /** ¿Está disponible el modo PDF para impresión? (siempre true si hay actividad válida) */
    public bool $pdfEnabled = true;

    /** ¿Disparar la celebración al completar la lección? (C3) */
    public bool $celebrate = false;

    public function mount(Activity $activity): void
    {
        $this->initializeHasStudentScope();

        if (! $this->studentService->isActivityVisible($activity)) {
            abort(404);
        }

        // La edad se computa en mount() (no en render()); puede ser null
        // (sin relación estudiant), '-' (fecha no cargada) o int.
        $age = auth()->user()?->estudiant?->age;
        $this->showMascot = $age === null || $age === '-' || (int) $age <= 12;
        $this->mascotEmphasis = $age !== null && $age !== '-' && (int) $age <= 8;

        // F1: misma base que la mascota (edad, no pestudio). La relación
        // estudiant ya se cargó en la línea anterior, sin query extra.
        $this->modoLectura = (bool) (auth()->user()?->estudiant?->modo_lectura ?? false);

        $this->activity = $activity;
        $this->isPreview = $activity->lmsPublication?->isPreviewToStudents() ?? false;

        $this->sections = $activity->lmsSections()
            ->where('is_visible', true)
            ->with(['visibleContents.media'])
            ->get();

        $this->resources = $activity->lmsResources()
            ->where('is_visible', true)
            ->with('media')
            ->get();

        $this->links = $activity->lmsLinks()
            ->where('is_visible', true)
            ->get();

        $this->htmlEmbeds = $activity->lmsHtmlEmbeds()
            ->where('is_visible', true)
            ->get()
            ->map(function ($embed) {
                $embed->is_mermaid = preg_match(
                    '/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/',
                    trim($embed->html_content ?? '')
                ) === 1;

                return $embed;
            });

        // Vista previa (now() < publish_at): solo la 1ª sección y sus adjuntos
        // vinculados. Los adjuntos globales (section_id vacío) quedan ocultos.
        if ($this->isPreview) {
            $firstSection = $this->sections->first();
            $firstSectionId = $firstSection?->id;
            $this->sections = $firstSection ? collect([$firstSection]) : collect();
            $this->resources = $this->resources->filter(fn ($r) => $r->section_id === $firstSectionId);
            $this->links = $this->links->filter(fn ($l) => $l->section_id === $firstSectionId);
            $this->htmlEmbeds = $this->htmlEmbeds->filter(fn ($e) => $e->section_id === $firstSectionId);
        }

        // Modo libro: requiere ≥2 secciones visibles, publicación completa y
        // no estar en la franja de lectura asistida (5–8).
        $this->flipEnabled = $this->sections->count() >= 2
            && ! $this->isPreview
            && ! $this->modoLectura;

        // Modo PDF: siempre disponible cuando hay actividad válida
        $this->pdfEnabled = true;

        $this->comments = ActivityComment::with('user')
            ->forActivity($activity->id)
            ->approved()
            ->orderBy('created_at', 'desc')
            ->get();

        $this->completed =
            LmsActivityProgress::where('activity_id', $activity->id)
                ->where('student_id', auth()->id())
                ->where('status', 'COMPLETED')
                ->exists()
            ||
            LmsActivityLog::where('activity_id', $activity->id)
                ->where('user_id', auth()->id())
                ->where('event', 'COMPLETE')
                ->exists();

        // Estrellas del detalle (C1): comentario propio aprobado y descarga
        // de recursos, además del $completed ya computado.
        $this->isCommented = ActivityComment::where('activity_id', $activity->id)
            ->where('user_id', auth()->id())
            ->approved()
            ->exists();

        $this->hasDownload = LmsActivityLog::where('activity_id', $activity->id)
            ->where('user_id', auth()->id())
            ->where('event', 'RESOURCE_DOWNLOAD')
            ->exists();

        // Registrar o actualizar progreso
        $progress = LmsActivityProgress::firstOrCreate(
            [
                'activity_id' => $activity->id,
                'student_id' => auth()->id(),
            ],
            [
                'status' => 'IN_PROGRESS',
                'completion_pct' => 0,
                'first_access_at' => now(),
                'last_access_at' => now(),
            ]
        );
        if (! $progress->wasRecentlyCreated) {
            $progress->update(['last_access_at' => now()]);
        }

        LmsActivityLog::record($activity->id, auth()->id(), 'VIEW');
    }

    public function markComplete(): void
    {
        LmsActivityLog::record($this->activity->id, auth()->id(), 'COMPLETE');

        LmsActivityProgress::updateOrCreate(
            [
                'activity_id' => $this->activity->id,
                'student_id' => auth()->id(),
            ],
            [
                'status' => 'COMPLETED',
                'completion_pct' => 100,
                'completed_at' => now(),
                'last_access_at' => now(),
            ]
        );

        $this->completed = true;
        $this->celebrate = true;

        $this->dispatch('activity-completed');
    }

    public function saveComment(): void
    {
        $this->validate(['newComment' => 'required|string|min:1|max:1000']);

        ActivityComment::create([
            'activity_id' => $this->activity->id,
            'user_id' => auth()->id(),
            'body' => $this->newComment,
            'is_approved' => false,
        ]);

        $this->newComment = '';

        $this->notification()->success(
            title: 'Comentario enviado',
            description: 'Tu comentario será visible una vez aprobado.'
        );
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.activity-view')
            ->layout('student.layouts.app');
    }
}
