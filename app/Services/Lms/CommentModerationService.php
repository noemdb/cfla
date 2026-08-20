<?php

namespace App\Services\Lms;

use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Profesor;
use App\Models\User;
use App\Notifications\CommentRepliedNotification;
use App\Services\EmailDeliveryService;
use App\Services\NotificationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class CommentModerationService
{
    protected ?Profesor $profesor = null;

    public function __construct(
        protected User $user
    ) {
        if (! $user->is_admin) {
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

        if (! $this->profesor) {
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

        if (! $this->profesor) {
            return false;
        }

        return $comment->activity?->pevaluacion?->profesor_id === $this->profesor->id;
    }

    /**
     * Crear una réplica del moderador (autoaprobada) a un comentario raíz.
     *
     * Reglas (SPEC REPLIES-COMMENTS-001):
     *  - Solo comentarios raíz reciben réplicas (profundidad 2 niveles, ADR-003).
     *  - No se responde a comentarios rechazados (la comunicación se cierra
     *    con el motivo del rechazo en `rejected_reason`).
     *  - El autor debe poder moderar la actividad del comentario.
     *  - La réplica nace is_approved = true → visible al estudiante de inmediato.
     *
     * @throws AuthorizationException si el usuario no puede moderar.
     * @throws \InvalidArgumentException si el comentario es una réplica o está rechazado.
     */
    public function reply(ActivityComment $comment, string $body): ActivityComment
    {
        if ($comment->isReply()) {
            throw new \InvalidArgumentException(
                'Solo se puede responder a comentarios raíz.'
            );
        }

        if ($comment->rejected_at !== null) {
            throw new \InvalidArgumentException(
                'No se puede responder a un comentario rechazado.'
            );
        }

        if (! $this->canModerate($comment)) {
            throw new AuthorizationException(
                'No tienes permisos para responder este comentario.'
            );
        }

        $reply = ActivityComment::create([
            'activity_id' => $comment->activity_id,
            'user_id' => $this->user->id,
            'parent_id' => $comment->id,
            'body' => $body,
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $this->user->id,
            'is_instructor_reply' => true,
        ]);

        $this->notifyAuthor($comment, $reply);

        return $reply;
    }

    /**
     * Editar el cuerpo de una réplica del moderador.
     *
     * Solo el autor de la réplica (o un admin) puede editarla. La réplica
     * mantiene su estado aprobado (no reingresa a la cola de moderación).
     *
     * @throws AuthorizationException si el usuario no es autor ni admin.
     * @throws \InvalidArgumentException si el comentario no es una réplica del profesor.
     */
    public function updateReply(ActivityComment $reply, string $body): ActivityComment
    {
        $this->ensureOwnReply($reply);

        $reply->update(['body' => $body]);

        return $reply;
    }

    /**
     * Borrar (soft-delete) una réplica del moderador.
     *
     * Solo el autor de la réplica (o un admin) puede borrarla. Al ser un
     * soft-delete, `scopeApproved()` (que excluye deleted_at) la oculta de la
     * vista del estudiante de inmediato sin perder el registro en la tabla.
     *
     * @throws AuthorizationException si el usuario no es autor ni admin.
     * @throws \InvalidArgumentException si el comentario no es una réplica del profesor.
     */
    public function deleteReply(ActivityComment $reply): void
    {
        $this->ensureOwnReply($reply);

        $reply->delete();
    }

    /**
     * Guard común de autoría para editar/borrar réplicas (ADR-012).
     */
    private function ensureOwnReply(ActivityComment $reply): void
    {
        if (! $reply->isReply() || ! $reply->isInstructorReply()) {
            throw new \InvalidArgumentException(
                'Solo se pueden modificar réplicas del profesor.'
            );
        }

        if ($this->user->id !== $reply->user_id && ! $this->user->is_admin) {
            throw new AuthorizationException(
                'No tienes permisos para modificar esta réplica.'
            );
        }
    }

    /**
     * Avisa al autor del comentario raíz que su hilo recibió una respuesta:
     * notificación de base de datos (campana/broadcast) y email transaccional
     * (SendPulse → Resend). Nunca rompe el flujo de la réplica: cualquier
     * fallo se loguea y se ignora (la réplica ya está creada y es visible).
     */
    private function notifyAuthor(ActivityComment $root, ActivityComment $reply): void
    {
        try {
            $author = $root->user;

            if (! $author) {
                return;
            }

            app(NotificationService::class)->notifyUsers(
                [$author],
                new CommentRepliedNotification($reply, $root)
            );

            $email = $author->email;

            if ($email) {
                app(EmailDeliveryService::class)->send(
                    $email,
                    'Te respondieron en tu comentario',
                    $this->replyEmailHtml($root, $reply)
                );
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo notificar al autor del comentario', [
                'comment_id' => $root->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * HTML del email transaccional de réplica. Contenido minimalista y seguro
     * (e()) — las réplicas son texto plano ya escapado al renderizar.
     */
    private function replyEmailHtml(ActivityComment $root, ActivityComment $reply): string
    {
        $activityUrl = route('student.lms.activity', $reply->activity_id);
        $activityTitle = e($reply->activity?->topic ?? 'actividad');
        $authorName = e($this->user->full_name);
        $replyBody = nl2br(e($reply->body));

        return view('emails.comment-replied', [
            'activityUrl' => $activityUrl,
            'activityTitle' => $activityTitle,
            'authorName' => $authorName,
            'replyBody' => $replyBody,
            'yourComment' => nl2br(e(mb_substr($root->body, 0, 200))),
        ])->render();
    }
}
