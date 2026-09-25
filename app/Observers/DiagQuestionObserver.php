<?php

namespace App\Observers;

use App\Models\app\Instrument\DiagQuestion;
use App\Models\User;
use App\Notifications\DiagQuestionNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DiagQuestionObserver
{
    public function created(DiagQuestion $question): void
    {
        $this->notify($question, 'creada', 'diag_question_created');
    }

    public function updated(DiagQuestion $question): void
    {
        // Evitar notificar toques triviales (solo updated_at/created_at)
        $changes = collect($question->getChanges())->except(['updated_at', 'created_at'])->keys();
        if ($changes->isEmpty()) {
            return;
        }

        $this->notify($question, 'actualizada', 'diag_question_updated');
    }

    public function deleted(DiagQuestion $question): void
    {
        $this->notify($question, 'eliminada', 'diag_question_deleted');
    }

    private function notify(DiagQuestion $question, string $action, string $type): void
    {
        if (! Auth::check()) {
            return;
        }

        try {
            $question->loadMissing('pensum.asignatura', 'pensum.pestudio', 'pensum.grado');

            $pensum = $question->pensum;
            $asignaturaName = $pensum?->asignatura?->name;
            $pensumLabel = $pensum ? trim(($pensum->pestudio?->name ?? '').' · '.($pensum->grado?->name ?? '').' · '.($asignaturaName ?? ''), ' ·') : null;
            if ($pensumLabel === '') {
                $pensumLabel = null;
            }

            $pensumId = $question->pensum_id;

            // --- Planificación (is_planner) — global
            $plannerQuery = User::where('is_planner', true)->where('is_active', 'enable')->where('id', '!=', Auth::id());
            $planners = $plannerQuery->get();

            // Anti-spam: omitir si ya hay un aviso del mismo tipo sobre esta
            // pregunta sin leer dentro de la ventana.
            $dedupeHours = ($action === 'actualizada' || $action === 'actualizado') ? 1 : 24;
            $subject = ['diag_question_id' => (int) $question->id];
            // Huella de idempotencia: la pregunta concreta + la acción. Un
            // guardado reintentado no se duplica; editar OTRA pregunta sí avisa.
            $event = ['diag_question_id' => (int) $question->id, 'action' => $action];
            $planners = app(NotificationService::class)->filterByDedupe(
                $planners,
                $type,
                $subject,
                $dedupeHours
            );

            if ($planners->isNotEmpty()) {
                $urlPlanner = route('app.planning.diagnostico.index');
                $messagePlanner = 'Pregunta diagnóstica '.$action.' en '.($asignaturaName ?? 'pensum #'.$pensumId).'.';

                // Añadir contexto de pregunta si existe (primeros 60 chars)
                if (! empty($question->pregunta)) {
                    $preview = \Illuminate\Support\Str::limit(trim((string) $question->pregunta), 60);
                    $messagePlanner .= ' "'.$preview.'"';
                }

                $notificationPlanner = new DiagQuestionNotification(
                    type: $type,
                    message: $messagePlanner,
                    url: $urlPlanner,
                    diagQuestionId: (int) $question->id,
                    pensumId: $pensumId ? (int) $pensumId : null,
                    asignaturaName: $asignaturaName,
                    pensumLabel: $pensumLabel,
                    action: $action,
                );

                app(NotificationService::class)->notifyUsers($planners, $notificationPlanner, $event);
            }

            // --- Jefatura (is_leadership) — acotado al área de conocimiento del Pensum
            // AreaConocimiento[CampoConocimiento->pensums]->leaderId vía Pensum::areaLeaderIds()
            $leaderIds = $pensum?->areaLeaderIds() ?? [];
            $leaderIds = array_map('intval', $leaderIds);
            $leaderIds = array_filter($leaderIds);
            // Excluir al actor si es líder del área
            $leaderIds = array_values(array_diff($leaderIds, [Auth::id()]));

            if ($leaderIds === []) {
                return;
            }

            $leaders = User::whereIn('id', $leaderIds)
                ->where('is_leadership', true)
                ->where('is_active', 'enable')
                ->get();

            $leaders = app(NotificationService::class)->filterByDedupe(
                $leaders,
                $type,
                $subject,
                $dedupeHours
            );

            if ($leaders->isEmpty()) {
                return;
            }

            // URL para jefatura: revisión de diagnósticos por área (si existe), fallback a planning
            $urlLeadership = route('app.leadership.diagnosticos');
            // Si la ruta no existe para el entorno, fallback
            if (! $urlLeadership) {
                $urlLeadership = route('app.planning.diagnostico.index');
            }

            $messageLeadership = 'Pregunta diagnóstica '.$action.' en tu área ('.($asignaturaName ?? 'pensum #'.$pensumId).').';
            if (! empty($question->pregunta)) {
                $preview = \Illuminate\Support\Str::limit(trim((string) $question->pregunta), 60);
                $messageLeadership .= ' "'.$preview.'"';
            }

            $notificationLeadership = new DiagQuestionNotification(
                type: $type,
                message: $messageLeadership,
                url: $urlLeadership,
                diagQuestionId: (int) $question->id,
                pensumId: $pensumId ? (int) $pensumId : null,
                asignaturaName: $asignaturaName,
                pensumLabel: $pensumLabel,
                action: $action,
            );

            app(NotificationService::class)->notifyUsers($leaders, $notificationLeadership, $event);
        } catch (\Throwable $e) {
            Log::warning('DiagQuestionObserver: fallo al notificar', [
                'diag_question_id' => $question->id ?? null,
                'pensum_id' => $question->pensum_id ?? null,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
