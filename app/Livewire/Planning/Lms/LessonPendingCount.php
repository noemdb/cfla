<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Activity;
use App\Models\User;
use App\Models\UserLessonRead;
use App\Services\Leadership\LeadershipService;
use App\Services\Lms\CoordinacionScopeService;
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
            session([$shownKey => $shown]);
        }

        $url = $payload['url'] ?? route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']);
        $message = $payload['message'] ?? 'Nueva lección programada para aprobación.';

        // La descripción usa x-html (WireUI): el enlace es navegable.
        $description = $message.' <a class="font-semibold underline" href="'.$url.'">Ver en el monitor →</a>';

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
     * filtrado por rol y scope (blueprint Opción 2 + Opción 5):
     * - Admin / Planner / Director: ven todas (sin restricción de scope).
     * - Coordinación: solo las lecciones de sus peducativos (CoordinacionScopeService).
     * - Leadership: solo las lecciones de sus áreas asignadas (LeadershipService).
     */
    public function refreshCount(): void
    {
        $user = auth()->user();

        $query = Activity::query()
            ->whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
            ->whereDoesntHave('lessonReads', fn ($q) => $q->where('user_id', $user->id));

        // Opción 2 — scope por rol (roles globales no se restringen)
        if (! $this->hasGlobalScope($user)) {
            $query->where(function ($q) use ($user) {
                $scoped = false;

                if ($user->isCoordinacion()) {
                    $pestudioIds = app(CoordinacionScopeService::class, ['user' => $user])->getPestudioIds();
                    $q->orWhereHas('pevaluacion.pensum', fn ($sq) => $sq->whereIn('pestudio_id', $pestudioIds));
                    $scoped = true;
                }

                if ($user->isLeadership()) {
                    $asignaturaIds = app(LeadershipService::class, ['user' => $user])->getAssignedAsignaturaIds();
                    $q->orWhereHas('pevaluacion.pensum', fn ($sq) => $sq->whereIn('asignatura_id', $asignaturaIds));
                    $scoped = true;
                }

                if (! $scoped) {
                    $q->whereRaw('1 = 0');
                }
            });
        }

        $this->count = $query->count();
    }

    /**
     * Roles con visión global (sin restricción de scope sobre SCHEDULED).
     * is_planner/is_director son accessors que ya incluyen a los admins.
     */
    private function hasGlobalScope(User $user): bool
    {
        return $user->is_planner || $user->is_director;
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

        $this->refreshCount();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.planning.lms.lesson-pending-count');
    }
}
