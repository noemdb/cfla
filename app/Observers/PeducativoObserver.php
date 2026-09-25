<?php

namespace App\Observers;

use App\Models\app\Academy\Peducativo;
use App\Models\User;
use App\Notifications\PeducativoNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PeducativoObserver
{
    /**
     * Ventanas anti-spam por acción. Un "creado" o "eliminado" es un evento de
     * vida del programa educativo: 24h bastan para no repetirlo. Las
     * actualizaciones son más frecuentes (edición de un campo cada vez), así
     * que la ventana es corta: interesa ver el último cambio, no cinco
     * avisos del mismo guardado.
     */
    private const DEDUPE_HOURS = [
        'creado' => 24,
        'actualizado' => 1,
        'eliminado' => 24,
    ];

    public function created(Peducativo $peducativo): void
    {
        $this->notify($peducativo, 'creado', 'peducativo_created');
    }

    public function updated(Peducativo $peducativo): void
    {
        // Evitar notificar toques triviales (solo updated_at)
        $changes = collect($peducativo->getChanges())->except(['updated_at', 'created_at'])->keys();
        if ($changes->isEmpty()) {
            return;
        }

        $this->notify($peducativo, 'actualizado', 'peducativo_updated');
    }

    public function deleted(Peducativo $peducativo): void
    {
        $this->notify($peducativo, 'eliminado', 'peducativo_deleted');
    }

    private function notify(Peducativo $peducativo, string $action, string $type): void
    {
        if (! Auth::check()) {
            return;
        }

        try {
            // Destinatarios: Planificación (is_planner) activos. Excluir al actor para no auto-notificar.
            $query = User::where('is_planner', true)->where('is_active', 'enable')->where('id', '!=', Auth::id());

            $planners = $query->get();
            if ($planners->isEmpty()) {
                return;
            }

            // Anti-spam: si ya hay un aviso del mismo tipo sobre este
            // peducativo sin leer dentro de la ventana, se omite.
            $subject = ['peducativo_id' => (int) $peducativo->id];
            $window = self::DEDUPE_HOURS[$action] ?? 24;

            $planners = app(NotificationService::class)->filterByDedupe(
                $planners,
                $type,
                // Huella de idempotencia: este peducativo + esta acción.
                ['peducativo_id' => (int) $peducativo->id, 'action' => $action]
            );

            if ($planners->isEmpty()) {
                return;
            }

            $name = $peducativo->name ?? 'Programa Educativo';
            $message = 'Programa Educativo "'.$name.'" '.$action.' por '.($this->actorLabel() ?? 'Planificación').'.';
            $url = route('app.planning.peducativos.index');

            app(NotificationService::class)->notifyUsers(
                $planners,
                new PeducativoNotification(
                    type: $type,
                    message: $message,
                    url: $url,
                    peducativoId: (int) $peducativo->id,
                    peducativoName: $name,
                    action: $action,
                ),
                // Huella de idempotencia: este peducativo + esta acción.
                ['peducativo_id' => (int) $peducativo->id, 'action' => $action]
            );
        } catch (\Throwable $e) {
            Log::warning('PeducativoObserver: fallo al notificar', [
                'peducativo_id' => $peducativo->id ?? null,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function actorLabel(): ?string
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        if (! empty($user->is_admin)) {
            return 'Administración';
        }
        if (! empty($user->is_planner)) {
            return 'Planificación';
        }
        if (! empty($user->is_coordinacion)) {
            return 'Coordinación';
        }
        if (! empty($user->is_leadership)) {
            return 'Jefe de Área';
        }

        return $user->username ?? null;
    }
}
