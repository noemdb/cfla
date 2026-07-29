<?php

namespace App\Livewire\Profesor\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Profesor;
use App\Services\Lms\CommentModerationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class CommentModeration extends Component
{
    use WithPagination, WireUiActions, AuthorizesRequests;

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
        ]);

        $query = $this->moderationService->scopeModeratable($query);

        // Filtro por tab
        match ($this->tab) {
            'approved' => $query->approved(),
            'rejected' => $query->rejected(),
            default    => $query->pending(),
        };

        // Búsqueda textual
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('body', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('username', 'like', "%{$this->search}%"))
                  ->orWhereHas('activity', fn($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
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
            $activities = Activity::whereHas('pevaluacion', fn($q) =>
                $q->where('profesor_id', $profesor->id)
            )->whereHas('comments')->orderBy('topic')->pluck('topic', 'id');
        } elseif (Auth::user()->is_admin) {
            $activities = Activity::whereHas('comments')->orderBy('topic')->pluck('topic', 'id');
        }

        $pendingCount = $this->moderationService->countPending();

        return view('livewire.profesor.lms.comment-moderation', [
            'comments'      => $comments,
            'activities'    => $activities,
            'pendingCount'  => $pendingCount,
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

    public function updatingTab() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }
    public function updatingActivityFilter() { $this->resetPage(); }
}
