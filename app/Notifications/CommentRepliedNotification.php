<?php

namespace App\Notifications;

use App\Models\app\Academy\Lms\ActivityComment;
use Illuminate\Notifications\Notification;

/**
 * Aviso al autor del comentario (estudiante) cuando un moderador responde
 * su hilo (SPEC REPLIES-COMMENTS-001, mejora #3+#6). Se persiste en `notifications`
 * vía NotificationService (campana + broadcast); el email transaccional se
 * envía aparte con EmailDeliveryService (SendPulse → Resend).
 */
class CommentRepliedNotification extends Notification
{
    public function __construct(
        public ActivityComment $reply,
        public ActivityComment $root,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $activity = $this->reply->activity;
        $title = $activity?->topic ?? 'actividad';

        return [
            'type' => 'comment_replied',
            'comment_id' => $this->root->id,
            'reply_id' => $this->reply->id,
            'activity_id' => $this->reply->activity_id,
            'activity_title' => $title,
            'reply_body' => mb_substr($this->reply->body, 0, 120),
            'author_name' => $this->reply->user?->full_name ?? 'Profesor',
            'message' => 'Tu comentario recibió una respuesta.',
            'url' => $this->reply->activity_id
                            ? route('student.lms.activity', $this->reply->activity_id)
                            : null,
        ];
    }
}
