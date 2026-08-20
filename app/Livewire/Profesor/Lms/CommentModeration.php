<?php

namespace App\Livewire\Profesor\Lms;

use App\Livewire\Concerns\HasCommentRateLimit;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Profesor;
use App\Services\Lms\CommentModerationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class CommentModeration extends Component
{
    use AuthorizesRequests, HasCommentRateLimit, WireUiActions, WithPagination;

    public string $tab = 'pending'; // pending | approved | rejected

    public string $search = '';

    public string $activityFilter = '';

    // Bulk actions
    public array $selected = [];

    public bool $selectAll = false;

    // Reject modal
    public bool $showRejectModal = false;

    public ?int $rejectCommentId = null;

    public string $rejectReason = '';

    // Reply (inline, sin modal)
    public ?int $replyToCommentId = null;

    public string $replyBody = '';

    // Edit reply (inline, sin modal)
    public ?int $editReplyId = null;

    public string $editReplyBody = '';

    protected CommentModerationService $moderationService;

    public function boot(): void
    {
        $this->moderationService = app(CommentModerationService::class, [
            'user' => Auth::user(),
        ]);
    }

    #[Layout('profesors.layouts.app')]
    public function render(): \Illuminate\View\View
    {
        $query = ActivityComment::with([
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.profesor',
            'user.profile',
            'replies' => fn ($q) => $q->approved()
                ->orderBy('created_at', 'asc')
                ->with('user.profile'),
        ]);

        $query = $this->moderationService->scopeModeratable($query)
            ->root();

        // Filtro por tab
        match ($this->tab) {
            'approved' => $query->approved(),
            'rejected' => $query->rejected(),
            default => $query->pending(),
        };

        // Búsqueda textual
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('body', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('username', 'like', "%{$this->search}%"))
                    ->orWhereHas('activity', fn ($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        // Filtro por actividad
        if ($this->activityFilter) {
            $query->where('activity_id', $this->activityFilter);
        }

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        // Actividades del profesor para el filtro
        $profesor = Profesor::where('user_id', Auth::id())->first();
        $activities = collect();
        if ($profesor) {
            $activities = Activity::whereHas('pevaluacion', fn ($q) => $q->where('profesor_id', $profesor->id)
            )->whereHas('comments')->orderBy('topic')->pluck('topic', 'id');
        } elseif (Auth::user()->is_admin) {
            $activities = Activity::whereHas('comments')->orderBy('topic')->pluck('topic', 'id');
        }

        $pendingCount = $this->moderationService->countPending();

        return view('livewire.profesor.lms.comment-moderation', [
            'comments' => $comments,
            'activities' => $activities,
            'pendingCount' => $pendingCount,
        ]);
    }

    // ─── Acciones individuales ────────────────────────────────────

    public function approveComment(int $commentId): void
    {
        $comment = ActivityComment::findOrFail($commentId);
        $this->authorize('approve', $comment);

        $this->moderationService->approve($comment);
        $this->notification()->success(
            title: 'Comentario aprobado',
            description: 'El comentario ya es visible para los estudiantes.'
        );
    }

    public function confirmReject(int $commentId): void
    {
        $this->rejectCommentId = $commentId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function rejectComment(): void
    {
        $this->validate(['rejectReason' => 'nullable|string|max:500']);

        $comment = ActivityComment::findOrFail($this->rejectCommentId);
        $this->authorize('reject', $comment);

        $this->moderationService->reject($comment, $this->rejectReason ?: null);
        $this->showRejectModal = false;
        $this->rejectCommentId = null;
        $this->rejectReason = '';

        $this->notification()->success(
            title: 'Comentario rechazado',
            description: 'El comentario ha sido rechazado.'
        );
    }

    // ─── Réplicas del profesor ────────────────────────────────────

    public function openReply(int $commentId): void
    {
        $this->replyToCommentId = $commentId;
        $this->replyBody = '';
    }

    public function saveReply(): void
    {
        if (! $this->commentRateLimitPassed('reply', 15, 60)) {
            $seconds = $this->commentRateLimitWaitSeconds('reply');

            $this->notification()->warning(
                title: 'Demasiadas respuestas',
                description: "Estás enviando respuestas muy rápido. Inténtalo de nuevo en {$seconds} segundos."
            );

            return;
        }

        $this->validate(['replyBody' => 'required|string|min:1|max:1000']);

        $comment = ActivityComment::findOrFail($this->replyToCommentId);

        try {
            $this->authorize('reply', $comment);
            $this->moderationService->reply($comment, $this->replyBody);
        } catch (\InvalidArgumentException $e) {
            $this->notification()->error(
                title: 'No se pudo enviar la réplica',
                description: $e->getMessage()
            );

            return;
        } catch (\Throwable $e) {
            Log::error('CommentModeration::saveReply inesperado', [
                'comment_id' => $this->replyToCommentId,
                'error' => $e->getMessage(),
            ]);

            $this->notification()->error(
                title: 'Ocurrió una situación inesperada',
                description: 'Tu respuesta no pudo enviarse. Por favor, inténtalo de nuevo.'
            );

            return;
        }

        $this->replyToCommentId = null;
        $this->replyBody = '';

        $this->dispatch('comment-approved');

        $this->notification()->success(
            title: 'Réplica enviada',
            description: 'El estudiante verá tu respuesta de inmediato.'
        );
    }

    public function openEditReply(int $replyId): void
    {
        $reply = ActivityComment::findOrFail($replyId);
        $this->editReplyId = $replyId;
        $this->editReplyBody = $reply->body;
    }

    public function saveEditReply(): void
    {
        $this->validate(['editReplyBody' => 'required|string|min:1|max:1000']);

        $reply = ActivityComment::findOrFail($this->editReplyId);

        try {
            $this->authorize('update', $reply);
            $this->moderationService->updateReply($reply, $this->editReplyBody);
        } catch (\InvalidArgumentException $e) {
            $this->notification()->error(
                title: 'No se pudo editar la réplica',
                description: $e->getMessage()
            );

            return;
        } catch (\Throwable $e) {
            Log::error('CommentModeration::saveEditReply inesperado', [
                'reply_id' => $this->editReplyId,
                'error' => $e->getMessage(),
            ]);

            $this->notification()->error(
                title: 'Ocurrió una situación inesperada',
                description: 'Tu cambio no pudo guardarse. Por favor, inténtalo de nuevo.'
            );

            return;
        }

        $this->editReplyId = null;
        $this->editReplyBody = '';

        $this->notification()->success(
            title: 'Réplica actualizada',
            description: 'El estudiante verá el texto corregido.'
        );
    }

    public function confirmDeleteReply(int $replyId): void
    {
        $this->dialog()->confirm([
            'title' => '¿Borrar esta réplica?',
            'description' => 'La réplica dejará de ser visible para los estudiantes. Esta acción puede revertirse restaurando el registro.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Borrar',
                'method' => 'deleteReply',
                'params' => [$replyId],
                'color' => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    public function deleteReply(int $replyId): void
    {
        $reply = ActivityComment::findOrFail($replyId);

        try {
            $this->authorize('delete', $reply);
            $this->moderationService->deleteReply($reply);
        } catch (\InvalidArgumentException $e) {
            $this->notification()->error(
                title: 'No se pudo borrar la réplica',
                description: $e->getMessage()
            );

            return;
        } catch (\Throwable $e) {
            Log::error('CommentModeration::deleteReply inesperado', [
                'reply_id' => $replyId,
                'error' => $e->getMessage(),
            ]);

            $this->notification()->error(
                title: 'Ocurrió una situación inesperada',
                description: 'La réplica no pudo borrarse. Por favor, inténtalo de nuevo.'
            );

            return;
        }

        $this->notification()->success(
            title: 'Réplica eliminada',
            description: 'La réplica ya no es visible para los estudiantes.'
        );
    }

    // ─── Bulk actions ─────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selected = ActivityComment::query()
                ->pending()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function approveSelected(): void
    {
        $count = $this->moderationService->approveBatch($this->selected);
        $this->selected = [];

        $this->notification()->success(
            title: "$count comentarios aprobados",
            description: 'Los comentarios seleccionados ahora son visibles.'
        );
    }

    public function rejectSelected(): void
    {
        $count = 0;
        foreach ($this->selected as $id) {
            $comment = ActivityComment::find($id);
            if ($comment && $this->moderationService->canModerate($comment)) {
                $this->moderationService->reject($comment);
                $count++;
            }
        }
        $this->selected = [];

        $this->notification()->success(
            title: "$count comentarios rechazados",
            description: 'Los comentarios seleccionados han sido rechazados.'
        );
    }

    // ─── Filtros ──────────────────────────────────────────────────

    public function updatingTab()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActivityFilter()
    {
        $this->resetPage();
    }
}
