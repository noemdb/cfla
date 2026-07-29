<?php

namespace App\Services\Lms;

use App\Models\User;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Profesor;
use Illuminate\Database\Eloquent\Builder;

class CommentModerationService
{
    protected ?Profesor $profesor = null;

    public function __construct(
        protected User $user
    ) {
        if (!$user->is_admin) {
            $this->profesor = Profesor::where('user_id', $user->id)->first();
        }
    }

    /**
     * Scope: comentarios que este profesor puede moderar.
     */
    public function scopeModeratable(Builder $query): Builder
    {
        if ($this->user->is_admin) {
            return $query;
        }

        if (!$this->profesor) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('activity.pevaluacion', function ($q) {
            $q->where('profesor_id', $this->profesor->id);
        });
    }

    /**
     * Scope: solo comentarios pendientes que este profesor puede moderar.
     */
    public function scopePendingForModeration(Builder $query): Builder
    {
        return $this->scopeModeratable($query)->pending();
    }

    /**
     * Contar pendientes para badge.
     */
    public function countPending(): int
    {
        return ActivityComment::query()
            ->unless($this->user->is_admin, function ($q) {
                $q->whereHas('activity.pevaluacion', function ($q) {
                    $q->where('profesor_id', $this->profesor?->id);
                });
            })
            ->pending()
            ->count();
    }

    /**
     * Aprobar un comentario (delega al modelo).
     */
    public function approve(ActivityComment $comment): void
    {
        $comment->approve($this->user->id);
    }

    /**
     * Rechazar un comentario (delega al modelo).
     */
    public function reject(ActivityComment $comment, ?string $reason = null): void
    {
        $comment->reject($this->user->id, $reason);
    }

    /**
     * Aprobar múltiples comentarios en lote.
     */
    public function approveBatch(array $commentIds): int
    {
        $comments = ActivityComment::whereIn('id', $commentIds)->pending()->get();
        $count = 0;

        foreach ($comments as $comment) {
            if ($this->canModerate($comment)) {
                $comment->approve($this->user->id);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Verificar si el usuario puede moderar este comentario específico.
     */
    public function canModerate(ActivityComment $comment): bool
    {
        if ($this->user->is_admin) {
            return true;
        }

        if (!$this->profesor) {
            return false;
        }

        return $comment->activity?->pevaluacion?->profesor_id === $this->profesor->id;
    }
}
