<?php

namespace App\Livewire\Student\Lms;

use App\Livewire\Concerns\HasCommentRateLimit;
use App\Livewire\Student\Lms\Concerns\HasStudentScope;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityProgress;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class ActivityView extends Component
{
    use HasCommentRateLimit;
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
        $previewService = app(\App\Services\Lms\LmsPreviewService::class);
        $this->isPreview = $previewService->isPreview($activity);

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

        $this->htmlEmbeds = $previewService->normalizeEmbeds(
            $activity->lmsHtmlEmbeds()
                ->where('is_visible', true)
                ->get()
        );

        // Vista previa (now() < publish_at): solo la 1ª sección y sus adjuntos
        // vinculados. Los adjuntos globales (section_id vacío) quedan ocultos.
        $preview = $previewService->applyPreview(
            $this->sections,
            $this->resources,
            $this->links,
            $this->htmlEmbeds,
            $this->isPreview
        );
        $this->sections = $preview['sections'];
        $this->resources = $preview['resources'];
        $this->links = $preview['links'];
        $this->htmlEmbeds = $preview['htmlEmbeds'];

        $this->comments = ActivityComment::with([
            'user.profile',
            'replies' => fn ($q) => $q
                ->approved()
                ->orderBy('created_at', 'asc')
                ->with('user.profile'),
        ])
            ->forActivity($activity->id)
            ->approved()
            ->root()
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
    }

    public function saveComment(): void
    {
        if (! $this->commentRateLimitPassed('comment', 10, 60)) {
            $seconds = $this->commentRateLimitWaitSeconds('comment');

            $this->notification()->warning(
                title: 'Demasiados comentarios',
                description: "Estás enviando mensajes muy rápido. Inténtalo de nuevo en {$seconds} segundos."
            );

            return;
        }

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
