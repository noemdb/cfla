<?php

namespace App\Services\Inicial;

use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiplanningwsummary;
use App\Models\User;
use App\Notifications\InicialWeeklyPlanNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class InicialWeeklyPlanNotifier
{
    public function planCreado(Eiplanningwk $plan): void
    {
        $this->notificar(
            $plan,
            'inicial_weekly_plan_created',
            'creado',
            'registró un nuevo plan semanal'
        );
    }

    public function estrategiasCompletadas(Eiplanningwk $plan): void
    {
        $this->notificar(
            $plan,
            'inicial_weekly_strategies_completed',
            'completado',
            'completó las estrategias de un plan semanal'
        );
    }

    public function resumenRegistrado(Eiplanningwk $plan, Eiplanningwsummary $summary): void
    {
        $plan->loadMissing('profesor');
        $summary->loadMissing('pevaluacion.pensum.asignatura');

        $area = $summary->pevaluacion?->pensum?->asignatura?->name;
        $detalle = $area ? " del área {$area}" : '';

        $this->notificar(
            $plan,
            'inicial_weekly_summary_created',
            'registrado',
            "registró un resumen semanal{$detalle}",
            (int) $summary->getKey()
        );
    }

    public function estrategiasCompletas(int $planId): bool
    {
        $dias = array_keys(Eiplanningwstrategy::WEEK_DAYS);
        $momentos = array_keys(Eiplanningwstrategy::LIST_MOMENT);
        $esperadas = [];

        foreach ($dias as $dia) {
            foreach ($momentos as $momento) {
                $esperadas[$dia.'|'.$momento] = false;
            }
        }

        $estrategias = Eiplanningwstrategy::query()
            ->where('eiplanningwk_id', $planId)
            ->get(['day_of_week', 'momento_rutina_diaria', 'lunes']);

        foreach ($estrategias as $estrategia) {
            $celda = $estrategia->day_of_week.'|'.$estrategia->momento_rutina_diaria;

            if (isset($esperadas[$celda]) && filled($estrategia->lunes)) {
                $esperadas[$celda] = true;
            }
        }

        return $esperadas !== [] && ! in_array(false, $esperadas, true);
    }

    private function notificar(
        Eiplanningwk $plan,
        string $type,
        string $action,
        string $evento,
        ?int $summaryId = null,
    ): void {
        if (! Schema::hasColumn('users', 'is_inicial_manager')) {
            Log::warning('No se pudo notificar el plan semanal: falta users.is_inicial_manager.', [
                'plan_id' => $plan->getKey(),
                'type' => $type,
            ]);

            return;
        }

        $destinatarios = User::query()
            ->where('is_inicial_manager', true)
            ->where('is_active', 'enable')
            ->when(Auth::check(), fn ($query) => $query->where('id', '!=', Auth::id()))
            ->get();

        if ($destinatarios->isEmpty()) {
            return;
        }

        $plan->loadMissing('profesor');
        $docente = $plan->profesor?->full_name ?? 'Un docente';
        $message = "{$docente} {$evento}.";

        try {
            app(NotificationService::class)->notifyUsers(
                $destinatarios,
                new InicialWeeklyPlanNotification(
                    type: $type,
                    message: $message,
                    url: route('inicials.seguimiento.eiplanningwks.index'),
                    planId: (int) $plan->getKey(),
                    action: $action,
                    summaryId: $summaryId
                ),
                [
                    'plan_id' => (int) $plan->getKey(),
                    'summary_id' => $summaryId ?? 0,
                    'type' => $type,
                ]
            );
        } catch (Throwable $exception) {
            Log::warning('Falló la notificación de seguimiento de planificación semanal.', [
                'plan_id' => $plan->getKey(),
                'summary_id' => $summaryId,
                'type' => $type,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
