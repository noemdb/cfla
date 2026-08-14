<?php

namespace App\Livewire\Planning\Lms;

use App\Models\UserLessonRead;
use App\Services\Lms\LmsPublicationService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class LessonPendingCount extends Component
{
    use WireUiActions;

    public int $count = 0;

    protected function getListeners(): array
    {
        return [
            'lesson-scheduled' => 'refreshCount',
            'echo-private:App.Models.User.'.auth()->id().',.lesson.scheduled' => 'refreshCountFromEcho',
        ];
    }

    public function refreshCountFromEcho(array $payload = []): void
    {
        $this->refreshCount();
        // El toast se muestra SOLO desde el listener Echo (no desde el dispatch
        // de bootstrap.js) para no duplicarlo, y se deduplica por usuario+lección.
        if (! empty($payload)) {
            $this->showScheduledToast($payload);
        }
    }

    /**
     * Límite de entradas del dedup de toasts por sesión: evita que la sesión
     * crezca sin límite (blueprint Opción 5 / hallazgo de auditoría).
     */
    private const MAX_TOAST_DEDUP = 50;

    /**
     * Muestra el toast WireUI al recibir el broadcast. Deduplicación por
     * usuario + activity_id: evita N toasts cuando hay varios badges
     * (planning/coordinación/director/admin) en la misma página.
     */
    private function showScheduledToast(array $payload): void
    {
        $activityId = (int) ($payload['activity_id'] ?? 0);
        $shownKey = 'lms_scheduled_toast_shown_'.auth()->id();
        $shown = (array) session($shownKey, []);

        if ($activityId && in_array($activityId, $shown, true)) {
            return;
        }
        if ($activityId) {
            $shown[] = $activityId;
            // Cota el array para que la sesión no crezca indefinidamente.
            session([$shownKey => array_slice($shown, -self::MAX_TOAST_DEDUP)]);
        }

        $url = $payload['url'] ?? route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']);
        $message = $payload['message'] ?? 'Nueva lección programada para aprobación.';

        // La descripción usa x-html (WireUI), por lo que el texto y la URL se
        // escapan antes de insertarlos: `message` interpola activity->topic
        // (contenido editable por profesores) y no debe renderizar HTML.
        $description = e($message).' <a class="font-semibold underline" href="'.e($url).'">Ver en el monitor →</a>';

        $this->notification()->success(
            title: 'Lección programada',
            description: $description,
        );
    }

    public function mount(): void
    {
        $this->refreshCount();
    }

    /**
     * Contador de lecciones SCHEDULED **no leídas** por el usuario actual,
     * filtrado por rol y scope (blueprint Opción 2 + Opción 5). El scope se
     * delega en LmsPublicationService::scopedScheduledQuery() para que badge y
     * monitor cuenten/marquen exactamente el mismo conjunto de lecciones.
     *
     * El resultado se cachea por usuario (TTL = poll interval): con N badges
     * en la página o el poll activo, solo la primera consulta toca la DB; la
     * caché se invalida por destinatario en notifyScheduled() (Opción 5), así
     * el broadcast refresca el badge al instante.
     */
    public function refreshCount(): void
    {
        $user = auth()->user();
        $cacheKey = LmsPublicationService::PENDING_COUNT_CACHE_PREFIX.$user->id;

        $this->count = Cache::remember($cacheKey, LmsPublicationService::cacheTtlSeconds(), function () use ($user) {
            return app(LmsPublicationService::class)
                ->scopedScheduledQuery($user)
                ->whereDoesntHave('lessonReads', fn ($q) => $q->where('user_id', $user->id))
                ->count();
        });
    }

    /**
     * Marca como leídas (por el usuario actual) un lote de actividades
     * programadas. Actualiza el contador tras el cambio.
     *
     * @param  array<int, int>  $activityIds
     */
    public function markAsRead(array $activityIds): void
    {
        $userId = auth()->id();

        if ($userId && ! empty($activityIds)) {
            $existing = UserLessonRead::where('user_id', $userId)
                ->whereIn('activity_id', $activityIds)
                ->pluck('activity_id')
                ->all();

            $new = array_values(array_diff($activityIds, $existing));

            if (! empty($new)) {
                $now = now();
                UserLessonRead::insert(
                    collect($new)->map(fn ($id) => [
                        'user_id' => $userId,
                        'activity_id' => $id,
                        'read_at' => $now,
                    ])->all()
                );
            }
        }

        // El estado de lectura cambió: invalidar la caché del contador.
        if ($userId) {
            Cache::forget(LmsPublicationService::PENDING_COUNT_CACHE_PREFIX.$userId);
        }

        $this->refreshCount();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.planning.lms.lesson-pending-count');
    }
}
